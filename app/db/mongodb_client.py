"""MongoDB database client backed by Motor."""

from __future__ import annotations

from datetime import datetime

from motor.motor_asyncio import AsyncIOMotorClient

from app.db.base import DatabaseClient, TranslationLog, VoiceProfileLog


class MongoDatabaseClient(DatabaseClient):
    def __init__(self, mongodb_url: str | None, database_name: str):
        if not mongodb_url:
            raise ValueError("WVT_MONGODB_URL must be set when db_backend=mongodb")
        self.client = AsyncIOMotorClient(mongodb_url)
        self.database = self.client[database_name]

    async def connect(self) -> None:
        await self.database.command("ping")

    async def disconnect(self) -> None:
        self.client.close()

    async def save_translation(self, log: TranslationLog) -> None:
        await self.database.translations.insert_one(
            {
                "source_language": log.source_language,
                "target_language": log.target_language,
                "source_text": log.source_text,
                "translated_text": log.translated_text,
                "provider": log.provider,
                "created_at": log.created_at,
            }
        )

    async def save_voice_profile(self, log: VoiceProfileLog) -> None:
        await self.database.voice_profiles.insert_one(
            {
                "speaker_id": log.speaker_id,
                "embedding_size": log.embedding_size,
                "provider": log.provider,
                "created_at": log.created_at,
            }
        )

    async def list_recent_translations(self, limit: int = 20) -> list[TranslationLog]:
        cursor = self.database.translations.find().sort("_id", -1).limit(limit)
        rows = await cursor.to_list(length=limit)
        return [
            TranslationLog(
                source_language=row["source_language"],
                target_language=row["target_language"],
                source_text=row["source_text"],
                translated_text=row["translated_text"],
                provider=row["provider"],
                created_at=row.get("created_at", datetime.utcnow()),
            )
            for row in rows
        ]


    async def count_translations(self) -> int:
        return int(await self.database.translations.count_documents({}))

    async def count_voice_profiles(self) -> int:
        return int(await self.database.voice_profiles.count_documents({}))
