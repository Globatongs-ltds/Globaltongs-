"""Configuration for the realtime translation backend."""

from functools import lru_cache

from pydantic import Field
from pydantic_settings import BaseSettings, SettingsConfigDict


class Settings(BaseSettings):
    app_name: str = "World Voice Teach Backend"
    env: str = "dev"

    # Select providers: mock, whisper, seamless, openai, azure
    stt_provider: str = Field(default="mock")
    translation_provider: str = Field(default="mock")
    tts_provider: str = Field(default="mock")
    voice_model_provider: str = Field(default="mock")

    # Database backend: memory, mysql, mongodb
    db_backend: str = Field(default="memory")
    mysql_url: str | None = None
    mongodb_url: str | None = None
    mongodb_database: str = "world_voice_teach"

    # Payments
    flutterwave_secret_key: str | None = None
    paypal_client_id: str | None = None
    paypal_client_secret: str | None = None
    paypal_sandbox: bool = True

    # Optional external keys/endpoints.
    openai_api_key: str | None = None
    azure_speech_key: str | None = None
    azure_speech_region: str | None = None

    model_config = SettingsConfigDict(env_prefix="WVT_", env_file=".env", extra="ignore")


@lru_cache
def get_settings() -> Settings:
    return Settings()
