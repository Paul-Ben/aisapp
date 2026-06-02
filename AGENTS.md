# AGENTS.md

## Stack
- Laravel 12, PHP 8.2+
- Tailwind CSS 3.x (`package.json` pins `^3.1.0`). README incorrectly says 4.x. `@tailwindcss/vite` is in `devDependencies` but **not used**; `vite.config.js` uses `laravel-vite-plugin` only.
- Bootstrap 5 used **only** on the public landing page (`resources/views/welcome.blade.php` + `resources/views/layouts/landing.blade.php`). All authenticated views are Tailwind.
- Alpine.js for client interactivity (`resources/js/app.js` boots it).
- Spatie Laravel Permission for roles, Laravel Breeze for auth, Maatwebsite Excel for result uploads.

## Setup Commands
```bash
composer setup      # install → copy .env → key:generate → migrate --force → npm install → npm run build
composer dev        # Runs server, queue:listen, pail, vite concurrently (kill-others on any exit)
composer test       # Clears config then runs php artisan test
```

- `composer setup` runs `migrate --force` but **does not seed**. To get the 6 test users (see Roles), run `php artisan migrate --seed` after setup, or seed explicitly with `php artisan db:seed --class=RoleSeeder`.
- `.env.example` defaults to SQLite (`DB_CONNECTION=sqlite`). MySQL is supported by switching env vars.

## Testing
- Pest 3.x. `tests/Pest.php` binds Feature tests to `TestCase` + `RefreshDatabase`.
- Tests run on **SQLite in-memory** (`phpunit.xml`). `BCRYPT_ROUNDS=4` for speed.
- Useful invocations:
  - `php artisan test --filter=TestName` — single test
  - `php artisan test --testsuite=Feature` or `--testsuite=Unit`
  - `vendor/bin/pint` — code style (Laravel Pint; no composer script wraps it)

## Roles & Access
Six roles in `database/seeders/RoleSeeder.php`: `superadmin`, `finance_officer`, `admin`, `exam_officer`, `staff`, `proprietor`. Each role ships with a seeded test user (`<role>@example.com` / `password`).

- Middleware alias `role` is registered in `bootstrap/app.php` and points to `app/Http/Middleware/RoleMiddleware.php` (Laravel 12 style; do **not** edit a Kernel.php — there isn't one).
- `/dashboard` is a redirector that fans out to the role's dashboard.
- Route prefix groups: `/superadmin`, `/admin`, `/finance`, `/exam`, `/staff`, `/student`, `/parent`.

### Watch out: `User::hasRole` is shadowed
`App\Models\User` (`app/Models/User.php:55`) defines its **own** `hasRole($role)` that only compares the string `users.role` column. This shadows Spatie's `HasRoles` trait method of the same name. Consequences:
- `RoleMiddleware` and the `/dashboard` redirector use this string-column check, **not** Spatie's pivot table.
- The `users.role` column has a `default('staff')` from `2026_04_22_233308_add_role_to_users_table.php`, so brand-new users without the column explicitly set will pass `role:staff` middleware by default and 403 on every other role.
- `RoleSeeder` calls `syncRoles([...])` (Spatie) but does **not** write to the `users.role` string column. Do not assume Spatie roles alone satisfy the middleware — set the string column too when creating users via code, or rework the override.

## Key Routes
- `/` — public landing (`welcome.blade.php`)
- `/newsletter`, `/academic-calendar`, `/girls-hairstyles` — public content views (note: route is `academic-calendar`, **not** `term-calendar`)
- `/dashboard` — authenticated redirector
- `/profile` — Breeze profile editor
- `/admin/*` — admin area (users, staff, classes, students, subjects, result-config, academic sessions, plus uploads for calendar/hairstyles/newsletter)
- `/staff/*` — staff area (assigned classes, bulk result entry via Excel template or manual)
- `/student/*`, `/parent/*`, `/finance/*`, `/exam/*`, `/superadmin/*` — role dashboards (mostly stub views in `resources/views/dashboards/`)

## Frontend Build
```bash
npm run dev    # vite dev server with HMR (refresh: true in vite.config.js)
npm run build  # production build
```
Vite inputs: `resources/css/app.css` (3 lines: `@tailwind base/components/utilities`) and `resources/js/app.js`.

## Database
- Two migration name styles coexist: Laravel defaults (`0001_01_01_*` for users/cache/jobs/sessions) and project-specific `2026_04_*` for domain tables. Both run in filename order.
- Migrations create a `role` string column on `users` (default `'staff'`) **and** the Spatie `roles`/`permissions`/`model_has_roles` etc. pivot tables. Both are populated by `RoleSeeder`.
- `app/Models/SchoolClass.php` maps to the `classes` table (`protected $table = 'classes'`). Don't assume Laravel's default pluralization.
- Default cache/session/queue stores are `database`; tests override to `array`/`sync`.

## Architecture Map (high-signal only)
- `app/Models/` — 13 models. Domain entities (Staff, Student, SchoolClass, Subject, Result, ResultConfig, GradeScale, ClassCategory, AcademicSession, AcademicCalendar, GirlsHairstyle, Newsletter) plus `User`.
- `app/Http/Controllers/` — top-level for general + `Admin/` subnamespace for admin-only resources (Staff, Class, Graduate, ResultConfig, Subject).
- `app/Exports/`, `app/Imports/` — Maatwebsite Excel template + import for result uploads.
- `app/Http/Requests/` — Form Request classes.
- `resources/views/dashboards/` — role-specific dashboard Blade views.
- `resources/views/components/` — shared Blade components (incl. `landing.*` for the public navbar/footer).
- `frontdesign/` — original design reference HTML/imagery. **Not** part of the build; ignore for app code.
- `routes/web.php` — single file, all web routes (no `routes/admin.php` etc).

## Conventions That Differ From Defaults
- Routes for the `admin.` group use a mix of resource controllers and bespoke actions (uploads, template downloads, bulk promote/graduate).
- Several controllers use `DB::beginTransaction()` + try/catch with `back()->withErrors(...)` and `withInput()` for failure paths.
- `Staff` and `Student` are domain models linked to `User` by FK (`Staff::user_id`, `Student` likewise); not the other way round. `User::staff()` is a `hasOne(Staff::class, 'user_id')` accessor used by staff controllers (`Auth::user()->staff`).
- `AcademicSession::getActive()` is the only "active session" lookup; result entry requires one to exist (`StaffResultEntryController` will redirect with error otherwise).
- `RoleMiddleware` also enforces `users.is_active = true` and logs the user out if inactive.
