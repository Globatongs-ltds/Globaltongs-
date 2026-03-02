# World Voice Teach - Realtime Voice Translation Backend

This repository contains a backend scaffold for a **realtime multilingual voice translation engine** with:

- Translation support for **30 major languages**.
- Streaming speech-to-text and translation pipeline design.
- Voice profile modeling (speaker embedding) endpoint for voice personalization.
- WebSocket realtime interface for low-latency chunk-by-chunk translation.
- Persistent storage support for **MySQL** or **MongoDB**.

## Architecture

```text
Client mic stream -> /ws/realtime
  -> STT provider (streaming ASR)
  -> Translation provider
  -> TTS provider (optionally cloned speaker)
  -> translated text + synthesized audio back to client
  -> persist translation/voice metadata in MySQL or MongoDB
```

### Core API

- `GET /health` - service heartbeat.
- `GET /languages` - 30 supported languages.
- `POST /translate` - text translation + persist translation log.
- `GET /translations/recent` - read recent translation logs.
- `GET /dashboard/admin` - simple admin dashboard page.
- `GET /dashboard/user` - simple user dashboard page.
- `GET /dashboard/admin/summary` - admin dashboard JSON summary.
- `GET /dashboard/user/summary` - user dashboard JSON summary.
- `POST /payments/initialize` - initialize payment checkout via Flutterwave or PayPal.
- `POST /voice-profile` - build speaker profile + persist metadata.
- `WS /ws/realtime` - realtime speech translation events + persist translation logs.

## Quick start

```bash
python -m venv .venv
source .venv/bin/activate
pip install -r requirements.txt
uvicorn app.main:app --host 0.0.0.0 --port 8000 --reload
```

## Database configuration

Set `WVT_DB_BACKEND` to one of: `memory`, `mysql`, `mongodb`.

### Option A: MySQL

```bash
export WVT_DB_BACKEND=mysql
export WVT_MYSQL_URL='mysql+aiomysql://user:password@localhost:3306/world_voice_teach'
```

### Option B: MongoDB

```bash
export WVT_DB_BACKEND=mongodb
export WVT_MONGODB_URL='mongodb://localhost:27017'
export WVT_MONGODB_DATABASE='world_voice_teach'
```

## Realtime WebSocket message format

Client sends JSON:

```json
{
  "source_language": "en",
  "target_language": "es",
  "speaker_id": "speaker-optional",
  "audio": "<base64 PCM/WAV bytes chunk>"
}
```

Server responds JSON:

```json
{
  "type": "translation",
  "payload": {
    "transcript": "...",
    "translated_text": "...",
    "audio": "<base64 synthesized audio>"
  }
}
```

## AI providers (plug-in strategy)

Current implementation ships with mock providers to keep local setup lightweight and deterministic.
Swap providers in `app/config.py` and implement corresponding classes for:

- **STT**: Whisper (faster-whisper), Azure Speech, Deepgram.
- **Translation**: NLLB / SeamlessM4T / GPT-4.1 translation.
- **TTS**: XTTS-v2, Azure Neural TTS, ElevenLabs.
- **Voice modeling**: Resemblyzer / pyannote speaker embeddings.

## Notes for production

- Use 16kHz mono PCM chunks (~20–40ms) for low-latency streaming.
- Add VAD (voice activity detection) for bandwidth and compute savings.
- Add Redis or NATS for horizontal scaling and sticky sessions.
- Add auth (JWT/API key), rate limits, and encrypted object storage for voice samples.


## Payment gateway configuration

### Flutterwave

```bash
export WVT_FLUTTERWAVE_SECRET_KEY=FLWSECK_TEST-xxxxx
```

Initialize payment with `gateway=flutterwave`.

### PayPal

```bash
export WVT_PAYPAL_CLIENT_ID=your_client_id
export WVT_PAYPAL_CLIENT_SECRET=your_client_secret
export WVT_PAYPAL_SANDBOX=true
```

Initialize payment with `gateway=paypal`.

Sample payload:

```json
{
  "gateway": "paypal",
  "amount": 15.5,
  "currency": "USD",
  "customer_email": "user@example.com",
  "reference": "order-1001",
  "callback_url": "https://your-app.com/payment/callback"
}
```
