"""MySQL database client backed by SQLAlchemy async ORM."""

from __future__ import annotations

from datetime import datetime

from sqlalchemy import DateTime, Integer, String, Text, desc, func, select
from sqlalchemy.ext.asyncio import AsyncSession, async_sessionmaker, create_async_engine
from sqlalchemy.orm import DeclarativeBase, Mapped, mapped_column

from app.db.base import DatabaseClient, TranslationLog, VoiceProfileLog


class Base(DeclarativeBase):
    pass


class TranslationRecord(Base):
    __tablename__ = "translations"

    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)
    source_language: Mapped[str] = mapped_column(String(8), nullable=False)
    target_language: Mapped[str] = mapped_column(String(8), nullable=False)
    source_text: Mapped[str] = mapped_column(Text, nullable=False)
    translated_text: Mapped[str] = mapped_column(Text, nullable=False)
    provider: Mapped[str] = mapped_column(String(50), nullable=False)
    created_at: Mapped[datetime] = mapped_column(DateTime(timezone=True), nullable=False)


class VoiceProfileRecord(Base):
    __tablename__ = "voice_profiles"

    id: Mapped[int] = mapped_column(Integer, primary_key=True, autoincrement=True)
    speaker_id: Mapped[str] = mapped_column(String(128), nullable=False, index=True)
    embedding_size: Mapped[int] = mapped_column(Integer, nullable=False)
    provider: Mapped[str] = mapped_column(String(50), nullable=False)
    created_at: Mapped[datetime] = mapped_column(DateTime(timezone=True), nullable=False)


class MySQLDatabaseClient(DatabaseClient):
    def __init__(self, mysql_url: str | None):
        if not mysql_url:
            raise ValueError("WVT_MYSQL_URL must be set when db_backend=mysql")
        self.engine = create_async_engine(mysql_url, pool_pre_ping=True)
        self.session_factory = async_sessionmaker(bind=self.engine, class_=AsyncSession, expire_on_commit=False)

    async def connect(self) -> None:
        async with self.engine.begin() as conn:
            await conn.run_sync(Base.metadata.create_all)

    async def disconnect(self) -> None:
        await self.engine.dispose()

    async def save_translation(self, log: TranslationLog) -> None:
        async with self.session_factory() as session:
            session.add(
                TranslationRecord(
                    source_language=log.source_language,
                    target_language=log.target_language,
                    source_text=log.source_text,
                    translated_text=log.translated_text,
                    provider=log.provider,
                    created_at=log.created_at,
                )
            )
            await session.commit()

    async def save_voice_profile(self, log: VoiceProfileLog) -> None:
        async with self.session_factory() as session:
            session.add(
                VoiceProfileRecord(
                    speaker_id=log.speaker_id,
                    embedding_size=log.embedding_size,
                    provider=log.provider,
                    created_at=log.created_at,
                )
            )
            await session.commit()

    async def list_recent_translations(self, limit: int = 20) -> list[TranslationLog]:
        async with self.session_factory() as session:
            result = await session.execute(
                select(TranslationRecord).order_by(desc(TranslationRecord.id)).limit(limit)
            )
            rows = result.scalars().all()
            return [
                TranslationLog(
                    source_language=row.source_language,
                    target_language=row.target_language,
                    source_text=row.source_text,
                    translated_text=row.translated_text,
                    provider=row.provider,
                    created_at=row.created_at,
                )
                for row in rows
            ]

    async def count_translations(self) -> int:
        async with self.session_factory() as session:
            result = await session.execute(select(func.count()).select_from(TranslationRecord))
            return int(result.scalar_one())

    async def count_voice_profiles(self) -> int:
        async with self.session_factory() as session:
            result = await session.execute(select(func.count()).select_from(VoiceProfileRecord))
            return int(result.scalar_one())
