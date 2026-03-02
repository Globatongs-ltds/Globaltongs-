"""Voice modeling (speaker embedding) interfaces."""

from __future__ import annotations

import hashlib
from dataclasses import dataclass


@dataclass
class VoiceProfile:
    speaker_id: str
    embedding: list[float]


class VoiceModelProvider:
    provider_name: str = "base"

    async def build_profile(self, audio_bytes: bytes) -> VoiceProfile:
        raise NotImplementedError


class MockVoiceModelProvider(VoiceModelProvider):
    provider_name = "mock"

    async def build_profile(self, audio_bytes: bytes) -> VoiceProfile:
        speaker_hash = hashlib.sha256(audio_bytes).hexdigest()[:12]
        embedding = [float((byte % 32) / 31.0) for byte in audio_bytes[:32]]
        if not embedding:
            embedding = [0.0] * 32
        return VoiceProfile(speaker_id=f"speaker-{speaker_hash}", embedding=embedding)
