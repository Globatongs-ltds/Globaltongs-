"""Realtime orchestration pipeline for streaming voice translation."""

from __future__ import annotations

from dataclasses import dataclass

from app.languages import SUPPORTED_LANGUAGE_CODES
from app.services.stt import STTProvider, TranscriptChunk
from app.services.translation import TranslationProvider
from app.services.tts import TTSProvider


@dataclass
class RealtimeResult:
    transcript: str
    translated_text: str
    synthesized_audio: bytes


class RealtimeTranslator:
    def __init__(self, stt: STTProvider, translation: TranslationProvider, tts: TTSProvider):
        self.stt = stt
        self.translation = translation
        self.tts = tts

    async def process_chunk(
        self,
        audio_bytes: bytes,
        source_language: str,
        target_language: str,
        speaker_id: str | None = None,
    ) -> RealtimeResult:
        self._validate_language(source_language)
        self._validate_language(target_language)

        transcript: TranscriptChunk = await self.stt.transcribe_chunk(
            audio_bytes=audio_bytes,
            source_language=source_language,
        )
        translated_text = await self.translation.translate(
            text=transcript.text,
            source_language=source_language,
            target_language=target_language,
        )
        synthesized_audio = await self.tts.synthesize(
            text=translated_text,
            language=target_language,
            speaker_id=speaker_id,
        )
        return RealtimeResult(
            transcript=transcript.text,
            translated_text=translated_text,
            synthesized_audio=synthesized_audio,
        )

    @staticmethod
    def _validate_language(language_code: str) -> None:
        if language_code not in SUPPORTED_LANGUAGE_CODES:
            raise ValueError(f"Unsupported language code: {language_code}")
