# AGENTS.md

## Stack
- Laravel 12, PHP 8.2+
- Tailwind CSS 3.x (package.json shows `^3.1.0`, NOT 4.x as README states)
- Bootstrap 5 only for the public landing page (`resources/views/welcome.blade.php`); authenticated views use Tailwind
- Alpine.js for JS interactivity
- Spatie Laravel Permission for roles

## Setup Commands
```bash
composer setup      # Runs: install → key:generate → migrate → npm install → npm run build
composer dev        # Starts PHP server, queue listener, pail logs, and Vite concurrently
composer test       # Clears config then runs Pest
```

## Testing
- Uses **Pest PHP** (`vendor/bin/pest`)
- Tests run against **SQLite in-memory** (see `phpunit.xml`)
- Run a single test: `php artisan test --filter=TestName`
- Run feature tests only: `php artisan test --testsuite=Feature`

## Roles & Access
Six roles defined in `database/seeders/RoleSeeder.php`:
`superadmin`, `finance_officer`, `admin`, `exam_officer`, `staff`, `proprietor`

- `/dashboard` redirects authenticated users to their role-specific dashboard
- Route prefixes: `/superadmin`, `/admin`, `/finance`, `/exam`, `/staff`, `/proprietor`
- Role check uses custom `RoleMiddleware` (`app/Http/Middleware/RoleMiddleware.php`)

## Key Routes
- `/` - Public landing page (`welcome.blade.php`)
- `/term-calendar`, `/girls-hairstyles`, `/newsletter` - Public view routes
- `/profile` - Authenticated profile editing

## Frontend Build
```bash
npm run dev    # Vite dev server with HMR
npm run build  # Production build
```
Vite input: `resources/css/app.css` and `resources/js/app.js` (per `vite.config.js`)

## Database
- Migrations use dated prefixes (e.g., `2026_04_23_000000_...`) instead of Laravel defaults
- `php artisan migrate --seed` seeds permissions via `RoleSeeder`
- Config supports MySQL or SQLite via `.env`