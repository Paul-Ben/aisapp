# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

AIS is a school management portal (Alven International Schools): Laravel 12 / PHP 8.2+, Blade + Tailwind 3 + Alpine.js, Spatie Permission, Breeze auth, Maatwebsite Excel, DomPDF, Paystack for online fee payment. `AGENTS.md` holds overlapping notes; keep the two consistent when changing shared facts.

## Commands

```bash
composer setup                       # install, .env, key:generate, migrate --force, npm install, build (does NOT seed)
php artisan migrate --seed           # seed the 6 role users: <role>@example.com / password
composer dev                         # concurrently: artisan serve, queue:listen, pail, vite
composer test                        # config:clear + php artisan test (Pest 3)
php artisan test --filter=TestName   # single test (also: tests/Feature/FeeItemTest.php)
vendor/bin/pint                      # code style (no composer script wraps it)
npm run dev | npm run build          # vite
```

- Tests run on in-memory SQLite (`phpunit.xml`); `tests/Pest.php` applies `RefreshDatabase` to `tests/Feature` only. Dev default DB is also SQLite; cache/session/queue default to `database`.
- Paystack config lives in `config/services.php` (`PAYSTACK_PUBLIC_KEY`, `PAYSTACK_SECRET_KEY`, `PAYSTACK_BASE_URL`, `PAYSTACK_CALLBACK_URL`). `PaystackService` throws `InvalidArgumentException` when the secret key is empty; controllers catch that and show "not configured".
- The README says Tailwind 4; it is actually Tailwind 3 via PostCSS. `@tailwindcss/vite` is installed but unused. Bootstrap 5 is used only on the public landing page (`layouts/landing.blade.php`, `welcome.blade.php`); authenticated views are Tailwind.
- `frontdesign/` is design reference material, not part of the build.

## Roles and access (important gotcha)

Roles: `superadmin`, `admin`, `finance_officer`, `exam_officer`, `staff`, `proprietor`, plus `student`/`parent` route groups (stub dashboards; no seeded users).

- `App\Models\User::hasRole()` ([User.php:55](app/Models/User.php#L55)) **overrides Spatie's** and just compares the `users.role` string column. `RoleMiddleware` (alias `role`, registered in `bootstrap/app.php`; there is no Http Kernel) and `DashboardController::redirect` both use this string column, not Spatie's pivot tables. `RoleSeeder` calls Spatie `syncRoles`, so when creating users in code, set `users.role` explicitly. The column defaults to `'staff'`.
- `RoleMiddleware` also logs out users with `is_active = false`.
- Each role's routes are `Route::middleware(['auth','role:X'])->prefix(X)->name('X.')` groups in `routes/web.php` (the only web routes file besides Breeze's `routes/auth.php`). `superadmin` has its own small group; `DashboardController` sends superadmin to `admin.dashboard`, whose routes require `role:admin`.

## Architecture

**Academic term is the pivot for both results and fees.** `AcademicSession::getActive()` returns the single active session (which carries `session` and `term`). `Result` and `Payment` rows are keyed by `(student_id, academic_year_id, term)` where `academic_year_id` is an `academic_sessions.id`. Result entry and all fee collection refuse to proceed without an active session.

**Results flow** (`StaffResultEntryController`, `Imports/ResultUploadImport`, `Exports/ResultUploadTemplate`): staff see only classes assigned to them via the `class_staff` pivot (`Auth::user()->staff`), pick a subject, then either download an Excel template and upload it, or save manually. Scoring/grading rules come from `ResultConfig` and `GradeScale`. Subjects are assigned to classes by admin (`SubjectManagementController`).

**Fees and payments** (three entry points converge on the `payments` table):
- `Finance\FeeManagementController` — CRUD for `FeeItem`; a fee is assigned to specific classes (`fee_item_class`) and/or class categories (`fee_item_class_category`). A student is charged a fee if their class or their class's category is assigned and the fee is active.
- `Finance\FeeCollectionController` — finance officer manually records payments; `Finance\RevenueTrackingController` reports.
- `OnlinePaymentController` (public, `/pay-online/*`, no auth; student looked up by admission number + payer email) → `Services\Paystack\PaystackService` initialize → redirect to Paystack → `callback` **always re-verifies** by reference and reads student/fee/session from the transaction metadata. Amounts are sent in kobo (`amount * 100`). Online payments store `gateway*` columns and a receipt (HTML + DomPDF) is available at `pay-online/receipt/{payment}`. One payment per (student, fee, session, term) is the duplicate guard.

**Students/classes:** `SchoolClass` maps to the `classes` table (non-default table name; the pivot FK is `class_id`). Classes belong to `ClassCategory`. `StudentController` also handles Excel bulk upload, bulk promote/demote/graduate; graduates are tracked via `graduation_session_id` (`Admin\GraduateController`). `Staff` and `Student` are domain records linked to `User` by `user_id` FK (`User::staff()` is a `hasOne`).

**Public content:** newsletter, academic calendar and girls' hairstyles are single-record file uploads managed by admin, displayed on public pages (route is `/academic-calendar`, not `term-calendar`).

## Conventions

- Controllers are grouped by role area (`Admin/`, `Finance/`) but many admin controllers live at the top level; admin routes are mostly hand-written per-action, not `Route::resource` (exceptions: `students`, finance `fees`).
- Write paths commonly use `DB::beginTransaction()` + try/catch returning `back()->withErrors(...)->withInput()`.
- Migrations mix Laravel defaults (`0001_01_01_*`) with dated `2026_*` domain migrations; they run in filename order.
- Feature tests exist for fees, payments, online payments, revenue and result preview (`tests/Feature/`); Paystack is exercised through `Http::fake`-style faking rather than live calls.
