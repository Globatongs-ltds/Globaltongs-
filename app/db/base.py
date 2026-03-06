"""Database abstraction and implementations (MySQL / MongoDB / in-memory)."""

from __future__ import annotations

from abc import ABC, abstractmethod
from dataclasses import dataclass
from datetime import datetime, timezone

from app.config import Settings


@dataclass
class TranslationLog:
    source_language: str
    target_language: str
    source_text: str
    translated_text: str
    provider: str
    created_at: datetime


@dataclass
class VoiceProfileLog:
    speaker_id: str
    embedding_size: int
    provider: str
    created_at: datetime


class DatabaseClient(ABC):
    @abstractmethod
    async def connect(self) -> None:
        raise NotImplementedError

    @abstractmethod
    async def disconnect(self) -> None:
        raise NotImplementedError

    @abstractmethod
    async def save_translation(self, log: TranslationLog) -> None:
        raise NotImplementedError

    @abstractmethod
    async def save_voice_profile(self, log: VoiceProfileLog) -> None:
        raise NotImplementedError

    @abstractmethod
    async def list_recent_translations(self, limit: int = 20) -> list[TranslationLog]:
        raise NotImplementedError

    @abstractmethod
    async def count_translations(self) -> int:
        raise NotImplementedError

    @abstractmethod
    async def count_voice_profiles(self) -> int:
        raise NotImplementedError


class InMemoryDatabaseClient(DatabaseClient):
    def __init__(self) -> None:
        self._translations: list[TranslationLog] = []
        self._voice_profiles: list[VoiceProfileLog] = []

    async def connect(self) -> None:
        return

    async def disconnect(self) -> None:
        return

    async def save_translation(self, log: TranslationLog) -> None:
        self._translations.append(log)

    async def save_voice_profile(self, log: VoiceProfileLog) -> None:
        self._voice_profiles.append(log)

    async def list_recent_translations(self, limit: int = 20) -> list[TranslationLog]:
        return list(reversed(self._translations[-limit:]))

    async def count_translations(self) -> int:
        return len(self._translations)

    async def count_voice_profiles(self) -> int:
        return len(self._voice_profiles)


def utc_now() -> datetime:
    return datetime.now(timezone.utc)


def create_database_client(settings: Settings) -> DatabaseClient:
    backend = settings.db_backend.lower()
    if backend == "mysql":
        from app.db.mysql_client import MySQLDatabaseClient

        return MySQLDatabaseClient(settings.mysql_url)
    if backend == "mongodb":
        from app.db.mongodb_client import MongoDatabaseClient

        return MongoDatabaseClient(settings.mongodb_url, settings.mongodb_database)
    return InMemoryDatabaseClient()
