"""Supported language catalog for translation."""

from dataclasses import dataclass


@dataclass(frozen=True)
class Language:
    code: str
    name: str


# 30 major spoken languages by number of speakers and global usage.
SUPPORTED_LANGUAGES: tuple[Language, ...] = (
    Language("en", "English"),
    Language("zh", "Chinese (Mandarin)"),
    Language("hi", "Hindi"),
    Language("es", "Spanish"),
    Language("fr", "French"),
    Language("ar", "Arabic"),
    Language("bn", "Bengali"),
    Language("pt", "Portuguese"),
    Language("ru", "Russian"),
    Language("ur", "Urdu"),
    Language("id", "Indonesian"),
    Language("de", "German"),
    Language("ja", "Japanese"),
    Language("sw", "Swahili"),
    Language("mr", "Marathi"),
    Language("te", "Telugu"),
    Language("tr", "Turkish"),
    Language("ta", "Tamil"),
    Language("ko", "Korean"),
    Language("vi", "Vietnamese"),
    Language("it", "Italian"),
    Language("pl", "Polish"),
    Language("uk", "Ukrainian"),
    Language("fa", "Persian"),
    Language("nl", "Dutch"),
    Language("th", "Thai"),
    Language("gu", "Gujarati"),
    Language("kn", "Kannada"),
    Language("pa", "Punjabi"),
    Language("ml", "Malayalam"),
)

SUPPORTED_LANGUAGE_CODES = {language.code for language in SUPPORTED_LANGUAGES}
