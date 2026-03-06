"""FastAPI backend for realtime multilingual voice translation."""

from __future__ import annotations

import base64
from contextlib import asynccontextmanager

from fastapi import FastAPI, File, HTTPException, Query, UploadFile, WebSocket, WebSocketDisconnect
from fastapi.responses import HTMLResponse

from app.config import get_settings
from app.db import TranslationLog, VoiceProfileLog, create_database_client, utc_now
from app.languages import SUPPORTED_LANGUAGES, SUPPORTED_LANGUAGE_CODES
from app.schemas import (
    DashboardSummaryResponse,
    HealthResponse,
    PaymentInitRequest,
    PaymentInitResponse,
    TranslationHistoryItem,
    TranslationRequest,
    TranslationResponse,
    VoiceProfileResponse,
)
from app.services.payment import create_payment_provider
from app.services.realtime import RealtimeTranslator
from app.services.stt import MockSTTProvider
from app.services.translation import MockTranslationProvider
from app.services.tts import MockTTSProvider
from app.services.voice_model import MockVoiceModelProvider

settings = get_settings()
database_client = create_database_client(settings)


@asynccontextmanager
async def lifespan(_: FastAPI):
    await database_client.connect()
    yield
    await database_client.disconnect()


app = FastAPI(title=settings.app_name, version="0.4.0", lifespan=lifespan)

stt_provider = MockSTTProvider()
translation_provider = MockTranslationProvider()
tts_provider = MockTTSProvider()
voice_model_provider = MockVoiceModelProvider()
realtime_translator = RealtimeTranslator(
    stt=stt_provider,
    translation=translation_provider,
    tts=tts_provider,
)


@app.get("/health", response_model=HealthResponse)
async def health() -> HealthResponse:
    return HealthResponse()


@app.get("/languages")
async def list_languages() -> list[dict[str, str]]:
    return [{"code": language.code, "name": language.name} for language in SUPPORTED_LANGUAGES]


@app.get("/translations/recent", response_model=list[TranslationHistoryItem])
async def recent_translations(limit: int = Query(default=20, ge=1, le=100)) -> list[TranslationHistoryItem]:
    logs = await database_client.list_recent_translations(limit=limit)
    return [TranslationHistoryItem(**log.__dict__) for log in logs]


@app.get("/dashboard/admin/summary", response_model=DashboardSummaryResponse)
async def admin_dashboard_summary(limit: int = Query(default=10, ge=1, le=100)) -> DashboardSummaryResponse:
    recent_logs = await database_client.list_recent_translations(limit=limit)
    return DashboardSummaryResponse(
        total_translations=await database_client.count_translations(),
        total_voice_profiles=await database_client.count_voice_profiles(),
        recent_translations=[TranslationHistoryItem(**log.__dict__) for log in recent_logs],
    )


@app.get("/dashboard/user/summary", response_model=DashboardSummaryResponse)
async def user_dashboard_summary(limit: int = Query(default=10, ge=1, le=100)) -> DashboardSummaryResponse:
    recent_logs = await database_client.list_recent_translations(limit=limit)
    return DashboardSummaryResponse(
        total_translations=await database_client.count_translations(),
        total_voice_profiles=await database_client.count_voice_profiles(),
        recent_translations=[TranslationHistoryItem(**log.__dict__) for log in recent_logs],
    )


@app.get("/dashboard/admin", response_class=HTMLResponse)
async def admin_dashboard_page() -> HTMLResponse:
    html = """
    <html><body>
      <h1>Admin Dashboard</h1>
      <p>Use <code>/dashboard/admin/summary</code> for JSON analytics.</p>
      <ul>
        <li>Total translations</li>
        <li>Total voice profiles</li>
        <li>Recent translation activities</li>
      </ul>
    </body></html>
    """
    return HTMLResponse(content=html)


@app.get("/dashboard/user", response_class=HTMLResponse)
async def user_dashboard_page() -> HTMLResponse:
    html = """
    <html><body>
      <h1>User Dashboard</h1>
      <p>Use <code>/dashboard/user/summary</code> for your recent activity payload.</p>
      <ul>
        <li>Recent translations</li>
        <li>Voice profile usage count</li>
      </ul>
    </body></html>
    """
    return HTMLResponse(content=html)


@app.post("/payments/initialize", response_model=PaymentInitResponse)
async def initialize_payment(request: PaymentInitRequest) -> PaymentInitResponse:
    try:
        provider = create_payment_provider(settings, request.gateway)
        result = await provider.initialize_payment(
            amount=request.amount,
            currency=request.currency.upper(),
            customer_email=request.customer_email,
            reference=request.reference,
            callback_url=request.callback_url,
        )
    except ValueError as exc:
        raise HTTPException(status_code=400, detail=str(exc)) from exc
    except Exception as exc:
        raise HTTPException(status_code=502, detail=f"Payment gateway error: {exc}") from exc

    return PaymentInitResponse(
        gateway=result.gateway,
        payment_link=result.payment_link,
        reference=result.reference,
        status=result.status,
    )


@app.post("/translate", response_model=TranslationResponse)
async def translate(request: TranslationRequest) -> TranslationResponse:
    if request.source_language not in SUPPORTED_LANGUAGE_CODES:
        raise HTTPException(status_code=400, detail="Unsupported source language")
    if request.target_language not in SUPPORTED_LANGUAGE_CODES:
        raise HTTPException(status_code=400, detail="Unsupported target language")

    translated = await translation_provider.translate(
        text=request.text,
        source_language=request.source_language,
        target_language=request.target_language,
    )
    await database_client.save_translation(
        TranslationLog(
            source_language=request.source_language,
            target_language=request.target_language,
            source_text=request.text,
            translated_text=translated,
            provider=translation_provider.provider_name,
            created_at=utc_now(),
        )
    )
    return TranslationResponse(translated_text=translated, provider=translation_provider.provider_name)


@app.post("/voice-profile", response_model=VoiceProfileResponse)
async def build_voice_profile(sample: UploadFile = File(...)) -> VoiceProfileResponse:
    audio_bytes = await sample.read()
    if not audio_bytes:
        raise HTTPException(status_code=400, detail="Empty audio upload")

    profile = await voice_model_provider.build_profile(audio_bytes)
    await database_client.save_voice_profile(
        VoiceProfileLog(
            speaker_id=profile.speaker_id,
            embedding_size=len(profile.embedding),
            provider=voice_model_provider.provider_name,
            created_at=utc_now(),
        )
    )
    return VoiceProfileResponse(
        speaker_id=profile.speaker_id,
        embedding_size=len(profile.embedding),
        provider=voice_model_provider.provider_name,
    )


@app.websocket("/ws/realtime")
async def realtime_socket(websocket: WebSocket) -> None:
    await websocket.accept()
    try:
        while True:
            message = await websocket.receive_json()
            source_language = message.get("source_language", "en")
            target_language = message.get("target_language", "es")
            speaker_id = message.get("speaker_id")
            audio_base64 = message.get("audio")

            if not audio_base64:
                await websocket.send_json({"type": "error", "payload": {"message": "Missing audio"}})
                continue

            try:
                audio_bytes = base64.b64decode(audio_base64)
                result = await realtime_translator.process_chunk(
                    audio_bytes=audio_bytes,
                    source_language=source_language,
                    target_language=target_language,
                    speaker_id=speaker_id,
                )
                await database_client.save_translation(
                    TranslationLog(
                        source_language=source_language,
                        target_language=target_language,
                        source_text=result.transcript,
                        translated_text=result.translated_text,
                        provider=translation_provider.provider_name,
                        created_at=utc_now(),
                    )
                )
            except ValueError as exc:
                await websocket.send_json({"type": "error", "payload": {"message": str(exc)}})
                continue

            await websocket.send_json(
                {
                    "type": "translation",
                    "payload": {
                        "transcript": result.transcript,
                        "translated_text": result.translated_text,
                        "audio": base64.b64encode(result.synthesized_audio).decode("utf-8"),
                    },
                }
            )
    except WebSocketDisconnect:
        return
