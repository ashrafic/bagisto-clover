# Changelog

All notable changes to this package are documented in this file.

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
