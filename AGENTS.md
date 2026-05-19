# SurgyPlan — Laravel 11 + Pest

## Stack

- **Laravel 11**, PHP ^8.2, MySQL
- **Pest 3.x** (`tests/Feature` auto-applies `RefreshDatabase`)
- **Laravel Breeze** (authentication scaffolding)
- **Vite + Tailwind CSS 3 + Alpine.js** (frontend)
- **Laravel Pint** (PHP code style)

## Commands

```bash
composer run pint           # format all PHP code
php artisan migrate         # run pending migrations
php artisan migrate:fresh   # reset DB and re-run all migrations
npm run dev                 # start Vite dev server
npm run build               # production build
```

## Testing

- Framework: **Pest** (not plain PHPUnit)
- Single file: `php vendor/bin/pest tests/Feature/SomeTest.php`
- All feature tests auto-apply `RefreshDatabase` (configured in `tests/Pest.php`)
- DB defaults to MySQL in `.env`. For testing with SQLite in memory, uncomment in `phpunit.xml`:
  ```xml
  <env name="DB_CONNECTION" value="sqlite"/>
  <env name="DB_DATABASE" value=":memory:"/>
  ```
- **UserFactory does NOT set `role`** — the DB default (`perawat_biasa`) applies. Create users with a specific role in tests:
  ```php
  User::factory()->create(['role' => User::ROLE_DOKTER]);
  ```
- Only Breeze-generated tests exist so far (`tests/Feature/Auth/`, `ProfileTest.php`, `ExampleTest.php`).

## Architecture

- **3 roles** (`role` column on `users`): `dokter`, `perawat_uk`, `perawat_biasa`
- **Role helpers** on `User`: `isDoctor()`, `isNurse()`, `isUkNurse()`, `isRegularNurse()`
- **Entrypoint**: `DashboardController@index` redirects by role (`dashboard.doctor`, `dashboard.nurse.uk`, `dashboard.nurse.regular`)
- **Access control**: controllers call `abort_unless()` inline — no Gates/Policies
- **16 models** in `app/Models/`: surgery requests, preoperative checklists, UK verification, scheduling, etc.
- `Diagnoses` and `Procedures` tables were **dropped** (migrations 2026_05_19). Diagnosis and procedure are now free-text fields (`diagnosis_text`, `procedure_text`) on `surgery_requests`.
- Session, cache, and queue all use the `database` driver by default.
- Auto-loading: `App\` → `app/`, `Database\Factories\` → `database/factories/`, `Database\Seeders\` → `database/seeders/`, `Tests\` → `tests/`

## View Paths

```
resources/views/
├── room-operation-requests/create.blade.php   # main form (uses data-toggle-target/data-toggle-id pattern)
├── dashboard/{doctor,nurse-uk,nurse-regular}.blade.php
├── data_pasients/{index,show,edit}.blade.php
├── guidelines/index.blade.php
├── auth/                                       # Breeze auth views
├── layouts/, components/, profile/, etc.
```

- Route names: `patients.{index,show,edit,update,destroy}`, `guidelines.{index,store,destroy}`, `nurse.regular.room-operation.{create,store}`

## File Uploads

- **Consent/medical files**: stored to `room-operation-requests/` on the `public` disk. Validation: `mimes:pdf,jpg,jpeg,png|max:5120`
- **Guideline files**: stored to `guidelines/` on the `public` disk. Validation: `mimes:pdf|max:10240`
- Remember to run `php artisan storage:link` in local dev.

## Key Conventions

- **Dark mode** via Tailwind `class` strategy (`darkMode: 'class'` in `tailwind.config.js`)
- **Type hints** — controller methods annotate `@var User $user` when accessing `Auth::user()`
- **Indonesian language** — UI text, status messages, and route segments are in Bahasa Indonesia
