# Bagisto Clover

[![Packagist Version](https://img.shields.io/packagist/v/ashrafic/bagisto-clover)](https://packagist.org/packages/ashrafic/bagisto-clover)
[![Packagist Downloads](https://img.shields.io/packagist/dt/ashrafic/bagisto-clover)](https://packagist.org/packages/ashrafic/bagisto-clover/stats)
[![License](https://img.shields.io/packagist/l/ashrafic/bagisto-clover)](LICENSE)

Clover [Hosted Checkout](https://docs.clover.com/dev/docs/hosted-checkout-api) payment gateway for [Bagisto](https://github.com/bagisto/bagisto) 2.x.

Customers are redirected to a PCI-compliant, Clover-hosted payment page — no card data ever touches your server (SAQ-A PCI scope). After payment, the order, invoice and order transaction are created automatically, verified through Clover's signed webhook with a redirect fallback so an order is never lost if the webhook is delayed.

## How it works

```
Checkout ──▶ Create session ──▶ Clover Hosted Checkout page ──▶ Customer pays
                                                                     │
              ┌──────────────────────────────────────────────────────┤
              │                                                      ▼
        Signed webhook                                     Redirect back to store
        (HMAC-SHA256)                                     (/clover/success)
              │                                                      │
              │ settles the order                          creates the order,
              │ (status + invoice                          deactivates the cart
              │  + transaction)                                 naturally
              └─────────────────▶ one order per payment ◀──────────┘
```

Following Bagisto's standard flow for redirect payments (the same shape as PayPal Standard + IPN):

- The **success return** (`/clover/success`) creates the order and deactivates the cart inside the customer's own request — exactly like Bagisto core does it, so the cart and its session binding are cleared naturally and the customer lands on the order success page.
- The **webhook** (`POST clover/webhook`) is the server-side payment verification. Its `Clover-Signature` header is verified with HMAC-SHA256 against your signing secret; unsigned or forged requests are rejected with `401`. It follows strict IPN semantics: it confirms the payment and settles an already-created order (status, invoice, transaction) — it never creates orders or touches carts, so it can never race the customer's browser.
- If the customer's browser never makes it back, schedule the recovery command to create those orders offline:

    ```bash
    # e.g. every 15 minutes via cron or the Laravel scheduler
    php artisan clover:settle-abandoned
    ```

- Both paths are idempotent: one order per payment, no duplicates regardless of which arrives first or how often the webhook retries.
- Every session is tracked in the `clover_checkout_sessions` table (status, payment id, verification source), giving you a full audit trail per checkout attempt.

## Features

- Hosted Checkout redirect flow — no card data on your server
- Signed webhook processing (`Clover-Signature` HMAC-SHA256) with strict rejection of unauthenticated calls
- Idempotent dual-path order processing — webhook and redirect never double-create an order
- Automatic order creation, status update, invoice and order transaction
- Line items for products, shipping, tax and discounts, converted to the smallest currency unit
- Sandbox and production credentials per channel, with separate test merchant settings
- Optional Hosted Checkout page configuration UUID for merchants with multiple payment pages
- Admin configuration UI translated in 22 locales
- Pest unit and feature tests included

## Requirements

| Requirement | Version |
| --- | --- |
| PHP | ^8.3 |
| Bagisto | 2.x |
| Laravel | 12.x |

You also need a Clover merchant account with **Hosted Checkout** enabled (Ecommerce section of the Merchant Dashboard) and an HTTPS store URL (required by Clover for redirect URLs and webhooks).

## Installation

```bash
composer require ashrafic/bagisto-clover
```

Register the module in `config/concord.php` (inside the `modules` array) — this is the only manual step, and it is required by Bagisto's module system for every package:

```php
Webkul\Clover\Providers\ModuleServiceProvider::class,
```

Run the migration, publish the checkout logo and refresh:

```bash
php artisan migrate
php artisan vendor:publish --tag=clover
php artisan optimize:clear
```

That's it — no core file edits are needed. The webhook route is CSRF-exempt by design (stateless, throttled), and the migration creates the `clover_checkout_sessions` table. If your app disables package discovery, register the provider manually in `bootstrap/providers.php`:

```php
use Webkul\Clover\Providers\CloverServiceProvider;

return [
    // ...
    CloverServiceProvider::class,
];
```

### When does Clover appear in the store?

- **Admin**: the configuration page (Configure → Sales → Payment Methods → Clover) appears automatically after installation.
- **Checkout**: the payment method only appears when it is actually usable — the admin must set **Status** to ON **and** provide valid credentials for the active mode (sandbox or production). A payment method without credentials is hidden on purpose, so customers can never reach a dead-end at redirect.

## Clover dashboard setup

1. Log in to the [Clover Merchant Dashboard](https://www.clover.com/).
2. Go to **Settings → View all settings → Ecommerce → Hosted Checkout**.
3. Generate an **Ecommerce API token** for the **Hosted Checkout** integration type:
   - The **private token** is your API token (Bearer token)
   - Note your **merchant ID** (`mId`)
4. In the **Webhook** section, enter `https://your-store.com/clover/webhook`, click **Generate** and copy the **signing secret**.
5. Leave the dashboard **redirect URLs empty** — the package sends per-transaction redirect URLs (`/clover/success`, `/clover/cancel`) with the checkout session id embedded, and dashboard values would override them.

## Configuration

In the Bagisto admin: **Configure → Sales → Payment Methods → Clover**.

| Field | Description |
| --- | --- |
| Status | Enable/disable the method |
| Title / Description | Shown at checkout (per channel and locale) |
| Logo | Payment method logo shown at checkout |
| Merchant ID / API Token / Webhook Signing Secret | Production credentials |
| Sandbox | Toggle between sandbox and production credentials |
| Test Merchant ID / Test API Token / Test Webhook Signing Secret | Sandbox credentials |
| Page Config UUID | Optional UUID when using multiple Hosted Checkout page configurations |

## Testing

### Sandbox test transaction

1. Create a free account on the [Clover Developer Dashboard](https://docs.clover.com/dev/docs/gdp-create-global-developer-account) — a sandbox test merchant is provisioned automatically.
2. Configure the sandbox credentials in the admin (sandbox enabled) and set `APP_URL` to an HTTPS URL (use a tunnel such as [ngrok](https://ngrok.com/) for local development).
3. Check out and pay with the test card:

    | Field | Value |
    | --- | --- |
    | Card number | `6011 3610 0000 6668` |
    | Expiry | any future date |
    | CVV | any 3 digits |
    | ZIP | any 5 digits |

### Test suite

The tests run inside a Bagisto installation. Add the suites to the app's `phpunit.xml`:

```xml
<testsuite name="Clover Unit Test">
    <directory suffix="Test.php">vendor/ashrafic/bagisto-clover/tests/Unit</directory>
</testsuite>
<testsuite name="Clover Feature Test">
    <directory suffix="Test.php">vendor/ashrafic/bagisto-clover/tests/Feature</directory>
</testsuite>
```

Bind the test case in `tests/Pest.php`:

```php
use Webkul\Clover\Tests\CloverTestCase;

uses(CloverTestCase::class)->in('../vendor/ashrafic/bagisto-clover/tests');
```

```bash
./vendor/bin/pest --testsuite="Clover Unit Test"
./vendor/bin/pest --testsuite="Clover Feature Test"
```

The feature suite covers the full payment flow: redirect processing, webhook-first processing, the webhook/redirect race, signature rejection and declined payments — no real Clover account required.

## Notes

- Hosted Checkout charges in the merchant's Clover currency — it must match your store's base currency.
- Discounts are sent as a negative line item; verify with a sandbox transaction if your account applies cart-level discounts.
- Webhook requests are rejected (`401`) when no signing secret is configured — configure the secret before enabling webhooks.

## Contributing

Contributions are welcome. Please make sure the existing code style (Laravel preset, `pint.json`) and the test suite pass:

```bash
./vendor/bin/pint --test
./vendor/bin/pest
```

## License

[MIT](LICENSE)
