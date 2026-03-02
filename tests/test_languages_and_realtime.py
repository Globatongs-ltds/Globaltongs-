import asyncio

from app.db.base import InMemoryDatabaseClient, TranslationLog, VoiceProfileLog, utc_now
from app.languages import SUPPORTED_LANGUAGE_CODES, SUPPORTED_LANGUAGES
from app.services.payment import MockPaymentProvider
from app.services.realtime import RealtimeTranslator
from app.services.stt import MockSTTProvider
from app.services.translation import MockTranslationProvider
from app.services.tts import MockTTSProvider


def test_supports_30_languages() -> None:
    assert len(SUPPORTED_LANGUAGES) == 30
    assert "en" in SUPPORTED_LANGUAGE_CODES
    assert "es" in SUPPORTED_LANGUAGE_CODES
    assert "zh" in SUPPORTED_LANGUAGE_CODES


def test_realtime_pipeline_roundtrip() -> None:
    pipeline = RealtimeTranslator(
        stt=MockSTTProvider(),
        translation=MockTranslationProvider(),
        tts=MockTTSProvider(),
    )

    result = asyncio.run(
        pipeline.process_chunk(
            audio_bytes=b"sample-bytes-for-test",
            source_language="en",
            target_language="es",
            speaker_id="speaker-123",
        )
    )
    assert "[en]" in result.transcript
    assert "[en->es]" in result.translated_text
    assert b"speaker-123" in result.synthesized_audio


def test_inmemory_database_translation_and_dashboard_counts() -> None:
    db = InMemoryDatabaseClient()
    asyncio.run(db.connect())
    asyncio.run(
        db.save_translation(
            TranslationLog(
                source_language="en",
                target_language="es",
                source_text="hello",
                translated_text="hola",
                provider="mock",
                created_at=utc_now(),
            )
        )
    )
    asyncio.run(
        db.save_voice_profile(
            VoiceProfileLog(
                speaker_id="speaker-123",
                embedding_size=32,
                provider="mock",
                created_at=utc_now(),
            )
        )
    )
    logs = asyncio.run(db.list_recent_translations(limit=10))
    assert len(logs) == 1
    assert logs[0].translated_text == "hola"
    assert asyncio.run(db.count_translations()) == 1
    assert asyncio.run(db.count_voice_profiles()) == 1


def test_mock_payment_provider_initializes_checkout() -> None:
    provider = MockPaymentProvider()
    result = asyncio.run(
        provider.initialize_payment(
            amount=49.0,
            currency="USD",
            customer_email="user@example.com",
            reference="order-1",
            callback_url="https://example.com/callback",
        )
    )
    assert result.gateway == "mock"
    assert result.status == "initialized"
    assert "order-1" in result.payment_link
