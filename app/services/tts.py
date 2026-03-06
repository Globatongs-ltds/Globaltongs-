"""Text-to-speech provider interfaces."""

from __future__ import annotations


class TTSProvider:
    provider_name: str = "base"

    async def synthesize(self, text: str, language: str, speaker_id: str | None = None) -> bytes:
        raise NotImplementedError


class MockTTSProvider(TTSProvider):
    provider_name = "mock"

    async def synthesize(self, text: str, language: str, speaker_id: str | None = None) -> bytes:
        # Returns mock bytes to validate end-to-end streaming contracts.
        payload = f"voice={speaker_id or 'default'} lang={language} text={text}"
        return payload.encode("utf-8")
