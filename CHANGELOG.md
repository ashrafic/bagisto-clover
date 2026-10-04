# Changelog

All notable changes to this package are documented in this file.

## v3.0.0

- **Decoupled order settlement from cart clearing** (the webhook now settles first whenever it arrives first):
  - The webhook creates and settles the order idempotently when no order exists yet — a payment is recorded even if the customer's browser never returns, without needing the recovery cron for that case
  - The success return always clears the cart in the customer's own request — including the stale guest session binding when the webhook already deactivated the cart (the v1.x failure mode, now a supported path)
  - The return resolves its checkout session through the session-bound cart even when the cart is no longer active, and briefly waits for a webhook-confirmed order to appear before creating one itself — no duplicates either way
- Diagnostics page moved under the Bagisto admin URL prefix: `/{admin-url}/clover/diagnostics`

## v2.0.5

- **Fixed cart not clearing on recovery**: when the success return reuses an order that was already created (e.g. an earlier attempt crashed after committing the order), the still-active cart is now deactivated in the customer's request again

## v2.0.4

- Diagnostics page now shows the last complete error entries (message + full stack trace) instead of only clover-matching lines

## v2.0.3

- Added an admin diagnostics page (`/clover/admin/diagnostics`, any logged-in admin): shows the channel's sandbox/token configuration state, the checkout sessions audit trail and the recent Clover log entries — so payment failures can be diagnosed from the admin panel without server access

## v2.0.2

- Cart discounts are no longer sent as a negative line item (undocumented in Clover's hosted checkout API and a likely gateway rejection); the discount is now folded into the item lines so every price stays positive and the line item sum matches the cart grand total exactly
- Clover API failures during session creation are now logged with the full gateway response, making declined/failed payments debuggable from `storage/logs`

## v2.0.1

- **Fixed the success-page regression of v2.0.0**: the webhook no longer creates orders or deactivates carts — strict IPN semantics. When it raced ahead of the customer's browser (the common case), the customer landed on the cart page instead of the order success page. Now the webhook only confirms the payment (and settles an already-created order); the customer's return always creates the order, deactivates the cart naturally and shows the success page
- Added `clover:settle-abandoned` command for paid sessions whose customer never returned to the store (schedule it, e.g. every 15 minutes)

## v2.0.0

- **Restructured to Bagisto's canonical redirect-payment flow** (same as PayPal Standard): the order is created and the cart is deactivated in the customer's own success-return request, so the cart clears naturally — including the browser session binding. The webhook now behaves like Bagisto's PayPal IPN: it settles an existing order (status, invoice, transaction) and only creates the order itself when the customer never made it back to the store
- No cart/session manipulation happens outside the customer's request anymore

## v1.1.0

- **Idempotency hardening**: order processing now reuses an existing order for the cart instead of ever creating a duplicate — protects against webhook/redirect races and partial webhook failures
- The success return and webhook endpoints catch `\Throwable`, so an unexpected error can never surface as a raw 500 to the customer; failures are logged and degrade to a readable redirect
- Regression tests for the un-interpolated `{CHECKOUT_SESSION_ID}` placeholder and the partial-failure scenario

## v1.0.9

- Renamed the admin field labels to match Clover's own terminology: **API Token** and **API Test Token** (stored field names unchanged — existing configuration keeps working)

## v1.0.8

- Code style fix on the controller

## v1.0.7

- Default checkout logo now follows the Laravel asset publishing standard: `php artisan vendor:publish --tag=clover` copies it to `public/vendor/clover/images/`; the admin-uploaded logo still takes precedence

## v1.0.6

- Default checkout logo is now served by the package itself (`/clover/logo`) — host apps no longer need to copy any asset into the shop theme; the admin-uploaded logo still takes precedence

## v1.0.5

- Scoped all feature test assertions to the data each test creates — the suite now passes on stores with existing Clover orders and sessions

## v1.0.4

- Test suite is now hermetic: existing channel configuration is backed up before each test and restored afterwards, so tests pass on stores that already have Clover configured

## v1.0.3

- Webhook route is now stateless (throttled, no session/CSRF middleware) — installs no longer need a CSRF exemption in the host app
- README: clarified post-install behavior and the exact install recipe

## v1.0.2

- Corrected commit authorship across the release history

## v1.0.1

- Declared the `php: ^8.3` requirement

## v1.0.0

- Initial release: Clover Hosted Checkout integration for Bagisto 2.x
- Redirect checkout flow with signed webhook verification and idempotent redirect fallback
- Automatic order, invoice and order transaction creation
- Sandbox/production credentials per channel, admin UI in 22 locales, Pest test suite
