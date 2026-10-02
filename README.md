# Bagisto Clover

Clover [Hosted Checkout](https://docs.clover.com/dev/docs/hosted-checkout-api) payment gateway integration for [Bagisto](https://github.com/bagisto/bagisto) 2.x.

Customers are redirected to a PCI-compliant, Clover-hosted payment page. After payment, the order, invoice and order transaction are created automatically — verified through Clover's signed webhook, with a redirect fallback so an order is never lost if the webhook is delayed.

## Features

- Hosted Checkout redirect flow — no card data ever touches your server (SAQ-A PCI scope)
- Signed webhook processing (`Clover-Signature` HMAC-SHA256) as the server-side payment verification
- Redirect fallback — if the webhook has not arrived when the customer returns, the order is still processed; the webhook handler is fully idempotent
- Automatic order creation, order status update, invoice and order transaction
- Sandbox/production credentials per channel, with separate test merchant settings
- Optional Hosted Checkout page configuration UUID for merchants with multiple payment pages
- Line items for products, shipping, tax and discounts (amounts converted to the smallest currency unit)
- Admin configuration UI in 22 locales, Pest unit and feature tests included

## Requirements

- Bagisto 2.x
- PHP 8.3+
- A Clover merchant account with **Hosted Checkout** enabled (Ecommerce section of the Merchant Dashboard)
- HTTPS store URL (required by Clover for redirect URLs and webhooks)

## Installation

```bash
composer require ashrafic/bagisto-clover
```

Register the module in `config/concord.php` (inside the `modules` array):

```php
Webkul\Clover\Providers\ModuleServiceProvider::class,
```

Run the migration and refresh:

```bash
php artisan migrate
php artisan optimize:clear
```

The `CloverServiceProvider` is auto-discovered. If your app disables package discovery, add it to `bootstrap/providers.php`:

```php
use Webkul\Clover\Providers\CloverServiceProvider;

return [
    // ...
    CloverServiceProvider::class,
];
```

### Installing from source (local development)

Add a path repository to your app's `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../bagisto-clover",
            "options": {
                "symlink": true,
                "versions": { "ashrafic/bagisto-clover": "1.0.0" }
            }
        }
    ]
}
```

```bash
composer require ashrafic/bagisto-clover
```

## Clover dashboard setup

1. Log in to the [Clover Merchant Dashboard](https://www.clover.com/).
2. Go to **Settings → View all settings → Ecommerce → Hosted Checkout**.
3. Generate an **Ecommerce API token** for the **Hosted Checkout** integration type:
   - The **private key** is your API key (Bearer token)
   - Note your **merchant ID** (`mId`)
4. In the **Webhook** section, enter `https://your-store.com/clover/webhook`, click **Generate** and copy the **signing secret**.
5. Leave the dashboard redirect URLs empty — the package sends per-transaction redirect URLs.

## Configuration

In the Bagisto admin: **Configure → Sales → Payment Methods → Clover**.

| Field | Description |
| --- | --- |
| Status | Enable/disable the method |
| Title / Description | Shown at checkout (per channel and locale) |
| Logo | Payment method logo shown at checkout |
| Merchant ID / API Key / Webhook Signing Secret | Production credentials |
| Sandbox | Toggle between sandbox and production credentials |
| Test Merchant ID / Test API Key / Test Webhook Signing Secret | Sandbox credentials |
| Page Config UUID | Optional UUID when using multiple Hosted Checkout page configurations |

## Testing

### Sandbox test transaction

1. Create a free developer account on the [Clover Developer Dashboard](https://docs.clover.com/dev/docs/gdp-create-global-developer-account) — a sandbox test merchant is provisioned automatically.
2. Configure the sandbox credentials in the admin (sandbox enabled) and set `APP_URL` to an HTTPS URL (use a tunnel such as ngrok for local development).
3. Check out and pay with the test card:
   - Card number: `6011 3610 0000 6668`
   - Expiry: any future date
   - CVV: any 3 digits
   - ZIP: any 5 digits

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

And bind the test case in `tests/Pest.php`:

```php
use Webkul\Clover\Tests\CloverTestCase;

uses(CloverTestCase::class)->in('../vendor/ashrafic/bagisto-clover/tests');
```

```bash
./vendor/bin/pest --testsuite="Clover Unit Test"
./vendor/bin/pest --testsuite="Clover Feature Test"
```

## Notes

- Hosted Checkout charges in the merchant's Clover currency — it must match your store's base currency.
- Discounts are sent as a negative line item; verify with a sandbox transaction if your account applies cart-level discounts.
- Webhook requests without a configured signing secret are rejected (401) — configure the secret before enabling webhooks.

## License

[MIT](LICENSE)
