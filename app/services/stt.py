"""Speech-to-text provider interfaces."""

from __future__ import annotations

from dataclasses import dataclass


@dataclass
class TranscriptChunk:
    text: str
    is_final: bool


class STTProvider:
    provider_name: str = "base"

    async def transcribe_chunk(
        self,
        audio_bytes: bytes,
        source_language: str,
    ) -> TranscriptChunk:
        raise NotImplementedError


class MockSTTProvider(STTProvider):
    provider_name = "mock"

    async def transcribe_chunk(self, audio_bytes: bytes, source_language: str) -> TranscriptChunk:
        # Placeholder: in production integrate with streaming Whisper/Azure Speech SDK.
        token_count = max(1, len(audio_bytes) // 256)
        return TranscriptChunk(text=f"[{source_language}] speech_tokens={token_count}", is_final=True)
