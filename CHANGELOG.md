# Changelog

All notable changes to this package are documented in this file.

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
