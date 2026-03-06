"""Shared request/response models."""

from datetime import datetime

from pydantic import BaseModel, Field


class HealthResponse(BaseModel):
    status: str = "ok"


class TranslationRequest(BaseModel):
    text: str = Field(min_length=1)
    source_language: str = Field(min_length=2, max_length=8)
    target_language: str = Field(min_length=2, max_length=8)


class TranslationResponse(BaseModel):
    translated_text: str
    provider: str


class TranslationHistoryItem(BaseModel):
    source_language: str
    target_language: str
    source_text: str
    translated_text: str
    provider: str
    created_at: datetime


class DashboardSummaryResponse(BaseModel):
    total_translations: int
    total_voice_profiles: int
    recent_translations: list[TranslationHistoryItem]


class PaymentInitRequest(BaseModel):
    gateway: str = Field(description="flutterwave | paypal | mock")
    amount: float = Field(gt=0)
    currency: str = Field(min_length=3, max_length=3)
    customer_email: str
    reference: str = Field(min_length=3)
    callback_url: str | None = None


class PaymentInitResponse(BaseModel):
    gateway: str
    payment_link: str
    reference: str
    status: str


class VoiceProfileResponse(BaseModel):
    speaker_id: str
    embedding_size: int
    provider: str


class RealtimeEvent(BaseModel):
    type: str
    payload: dict
