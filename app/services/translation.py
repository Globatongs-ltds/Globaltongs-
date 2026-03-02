"""Translation provider interfaces."""

from __future__ import annotations


class TranslationProvider:
    provider_name: str = "base"

    async def translate(self, text: str, source_language: str, target_language: str) -> str:
        raise NotImplementedError


class MockTranslationProvider(TranslationProvider):
    provider_name = "mock"

    async def translate(self, text: str, source_language: str, target_language: str) -> str:
        return f"[{source_language}->{target_language}] {text}"
