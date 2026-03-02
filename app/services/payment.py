"""Payment gateway provider interfaces for Flutterwave and PayPal."""

from __future__ import annotations

from dataclasses import dataclass

import httpx

from app.config import Settings


@dataclass
class PaymentInitResult:
    gateway: str
    payment_link: str
    reference: str
    status: str


class PaymentProvider:
    provider_name: str = "base"

    async def initialize_payment(
        self,
        amount: float,
        currency: str,
        customer_email: str,
        reference: str,
        callback_url: str | None = None,
    ) -> PaymentInitResult:
        raise NotImplementedError


class MockPaymentProvider(PaymentProvider):
    provider_name = "mock"

    async def initialize_payment(
        self,
        amount: float,
        currency: str,
        customer_email: str,
        reference: str,
        callback_url: str | None = None,
    ) -> PaymentInitResult:
        return PaymentInitResult(
            gateway=self.provider_name,
            payment_link=f"https://mock-payments.local/checkout/{reference}",
            reference=reference,
            status="initialized",
        )


class FlutterwavePaymentProvider(PaymentProvider):
    provider_name = "flutterwave"

    def __init__(self, secret_key: str):
        if not secret_key:
            raise ValueError("WVT_FLUTTERWAVE_SECRET_KEY is required for Flutterwave")
        self.secret_key = secret_key

    async def initialize_payment(
        self,
        amount: float,
        currency: str,
        customer_email: str,
        reference: str,
        callback_url: str | None = None,
    ) -> PaymentInitResult:
        payload = {
            "tx_ref": reference,
            "amount": amount,
            "currency": currency,
            "redirect_url": callback_url or "https://example.com/payment/callback",
            "customer": {"email": customer_email},
            "customizations": {"title": "World Voice Teach"},
        }
        headers = {"Authorization": f"Bearer {self.secret_key}"}
        async with httpx.AsyncClient(timeout=15.0) as client:
            response = await client.post(
                "https://api.flutterwave.com/v3/payments",
                json=payload,
                headers=headers,
            )
            response.raise_for_status()
            data = response.json().get("data", {})
            return PaymentInitResult(
                gateway=self.provider_name,
                payment_link=data.get("link", ""),
                reference=reference,
                status="initialized",
            )


class PayPalPaymentProvider(PaymentProvider):
    provider_name = "paypal"

    def __init__(self, client_id: str, client_secret: str, base_url: str = "https://api-m.paypal.com"):
        if not client_id or not client_secret:
            raise ValueError("WVT_PAYPAL_CLIENT_ID and WVT_PAYPAL_CLIENT_SECRET are required for PayPal")
        self.client_id = client_id
        self.client_secret = client_secret
        self.base_url = base_url.rstrip("/")

    async def _token(self, client: httpx.AsyncClient) -> str:
        response = await client.post(
            f"{self.base_url}/v1/oauth2/token",
            data={"grant_type": "client_credentials"},
            auth=(self.client_id, self.client_secret),
            headers={"Accept": "application/json"},
        )
        response.raise_for_status()
        return response.json()["access_token"]

    async def initialize_payment(
        self,
        amount: float,
        currency: str,
        customer_email: str,
        reference: str,
        callback_url: str | None = None,
    ) -> PaymentInitResult:
        async with httpx.AsyncClient(timeout=20.0) as client:
            access_token = await self._token(client)
            order_payload = {
                "intent": "CAPTURE",
                "purchase_units": [
                    {
                        "reference_id": reference,
                        "amount": {
                            "currency_code": currency,
                            "value": f"{amount:.2f}",
                        },
                    }
                ],
                "payer": {"email_address": customer_email},
                "application_context": {
                    "return_url": callback_url or "https://example.com/payment/success",
                    "cancel_url": callback_url or "https://example.com/payment/cancel",
                },
            }
            order_response = await client.post(
                f"{self.base_url}/v2/checkout/orders",
                json=order_payload,
                headers={
                    "Authorization": f"Bearer {access_token}",
                    "Content-Type": "application/json",
                },
            )
            order_response.raise_for_status()
            order_data = order_response.json()
            approval_link = ""
            for link in order_data.get("links", []):
                if link.get("rel") == "approve":
                    approval_link = link.get("href", "")
                    break
            return PaymentInitResult(
                gateway=self.provider_name,
                payment_link=approval_link,
                reference=reference,
                status="initialized",
            )


def create_payment_provider(settings: Settings, gateway: str) -> PaymentProvider:
    selected = gateway.lower()
    if selected == "flutterwave":
        return FlutterwavePaymentProvider(settings.flutterwave_secret_key or "")
    if selected == "paypal":
        base_url = "https://api-m.sandbox.paypal.com" if settings.paypal_sandbox else "https://api-m.paypal.com"
        return PayPalPaymentProvider(
            client_id=settings.paypal_client_id or "",
            client_secret=settings.paypal_client_secret or "",
            base_url=base_url,
        )
    return MockPaymentProvider()
