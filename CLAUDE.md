# Icon Venue & Suites — booking system

Laravel 12 app that runs bookings, clients and payments for Icon Venue & Suites. Guests browse venues and suites and check availability and price on the public site, then **contact the business to book**. There is no online booking or payment. Staff create the booking in the admin panel, record and verify payments, and the booking moves through its statuses.

Rules in `.claude/rules/` load automatically: senior Laravel standards, strict MVC, modular services, the docs workflow, the Definition of Done and test naming.

## Non-negotiables
- **Plan → approve → document.** Every plan lists its BDD scenarios. Once a plan is approved, the decision entry goes in `docs/decisions/` *before* any code (`docs-workflow.md`).
- **A feature is done when its BDD test passes.** That means a `.feature` file plus a Dusk test, written by the `bdd-qa-tester` agent (`definition-of-done.md`).
- **New code follows MVC and service modules. Legacy fat controllers stay as they are** unless a refactor is approved (`docs/architecture.md`).
- **Tests never touch the dev database `iconvenue`.** PHPUnit is forced onto in-memory SQLite (`phpunit.xml` `force="true"` on both `<env>` and `<server>`, plus a guard in `tests/TestCase.php`). Dusk runs only in `dusk-app` against `iconvenue_testing`, guarded in `tests/DuskTestCase.php`. On 2026-11-06 a test run inside `app` wiped the dev database, which is why these guards exist. Keep them.

## Actors
| Actor | Can do |
|---|---|
| **Guest** (public) | Browse venues and suites, view details, packages and add-ons, check availability, get a price estimate, contact via phone, email, WhatsApp, Messenger or Google Form |
| **Staff** (`role_id` 2) | Dashboard, create and manage bookings (confirm, cancel, complete, calendar), record, verify and reject payments, view clients, browse venues and toggle their availability |
| **Admin** (`role_id` 1) | Everything Staff can do, plus CRUD for venues, suites and packages, add-ons, staff accounts, carousel, settings and reports |

Route groups in `routes/web.php`: public, guest (`/login`), `auth` + `admin.` prefix (staff and admin), and `AdminMiddleware` (admin only). Seeded logins: `admin@iconvenue.com` / `admin123`, `staff@iconvenue.com` / `staff123`.

## Domain rules
- **Bookable spaces** live in the `venues_and_suites` table (model `Venue`, with `type` = `venue` or `suite`). Pricing comes from `price_per_day` and the `price_morning`, `price_afternoon` and `price_evening` slot prices. **Packages** (`VenuePackage`) have their own time-based prices.
- **Time slots:** guests can pick several. Picking all three becomes Full Day pricing. Morning + Evening without Afternoon isn't allowed. A full-day booking blocks the whole date. Today can be booked, but slots that have already passed are blocked (`PublicController` calendar data). Multi-day bookings use `number_of_days`.
- **Add-ons** (`VenueAddon`) track stock and attach to bookings through `booking_addons`. Bookings can also carry discounts.
- **Booking status:** pending → confirmed → completed, or cancelled. **Payment status:** unpaid → partial → paid, based on verified payments.
- **Booking reference:** `IVS-YYYY-XXXX` (`bookings:generate-references` backfills missing ones).
- **Emails** live in `app/Mail/` (booking status, payment status, reminder). Scheduled in `routes/console.php`: `bookings:send-reminders` daily at 09:00 (about 24h before the event), `bookings:complete-expired` every minute.
- The end-user flows are described in `USER_GUIDE.md`. Use it as the source for BDD scenarios.

## Stack
- PHP 8.3 in Docker (8.2+ required), Laravel 12, MariaDB 10.11, Mailpit.
- Frontend: Blade, with **Tailwind, Font Awesome 6.4 and jQuery 3.6 loaded from CDNs** in `resources/views/layouts/app.blade.php`. Vite and Tailwind 4 are installed but only `welcome.blade.php` uses `@vite`.
- Reuse for images: `App\Helpers\ImageHelper` and `App\Traits\HandlesImageUploads`. Upload files go to the `public` disk, served at `public/storage`.

## Commands (Docker)
| Task | Command |
|---|---|
| Start the stack (starts Docker if it's off) | `/run-dev` (add `--testing` for Dusk, `--tools` for phpMyAdmin, `--build` to rebuild) |
| Artisan | `docker compose exec app php artisan <cmd>` |
| PHPUnit | `docker compose exec app php artisan test` (in-memory SQLite) |
| BDD / Dusk | `docker compose exec dusk-app php artisan dusk [--filter=…]` |
| Format | `docker compose exec app vendor/bin/pint --dirty` |
| Re-seed dev DB | `docker compose exec app php artisan db:seed` (the entrypoint seeds only once, gated by `storage/.seeded`) |

URLs: app http://localhost:8000, admin http://localhost:8000/login, Mailpit http://localhost:8025, phpMyAdmin :8080 (`--tools`), Selenium noVNC :7900 (password `secret`). MariaDB on host port 3307. Credentials are in `docker-compose.yml`.

## Troubleshooting
- Images missing: `docker compose exec app php artisan storage:link`.
- Permission errors: `chmod -R 775 storage bootstrap/cache`.
- Vite exits with `Cannot find module @rollup/rollup-linux-…`: `docker compose rm -sf vite && docker volume rm iconvenue-suites_node_modules`, then `/run-dev`.
- `php artisan serve` drops the container env, which is why the Dockerfile runs `php -S` directly. Keep it that way.

## Where things are
- Docs: `docs/README.md` (index and decision template), `docs/architecture.md`, `docs/testing/bdd.md`.
- QA agent: `.claude/agents/bdd-qa-tester.md`. Hooks: `.claude/settings.json` (docs reminder after plan approval, BDD gate on Stop).
- `README.md` is written for people and is partly out of date. It describes XAMPP setup, lists email notifications and a calendar view as "future" (both exist), and links to docs that don't exist (`SETUP_INSTRUCTIONS.md`, `QUICK_REFERENCE.md`, `PROJECT_OVERVIEW.md`, `SYSTEM_FLOW.md`). Trust this file and the code over it.
