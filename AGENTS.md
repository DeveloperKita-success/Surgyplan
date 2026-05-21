# SurgyPlan — Laravel 11 + Pest

## Stack

- **Laravel 11**, PHP ^8.2, MySQL
- **Pest 3.x** (Feature tests auto-apply `RefreshDatabase` in `tests/Pest.php`)
- **Laravel Breeze** (Blade auth scaffolding)
- **Vite 5 + Tailwind CSS 3 + Alpine.js 3** (frontend)
- **Laravel Pint** (`composer run pint` formats all PHP)
- **`database` driver** for session, cache, and queue

## Commands

```bash
composer run pint           # format all PHP
php artisan migrate         # run pending migrations
php artisan migrate:fresh   # reset DB and re-run all migrations
php artisan db:seed         # seed demo users
npm run dev                 # Vite dev server (hot reload enabled)
npm run build               # production build
php artisan storage:link    # required for file uploads in local dev
```

## Seeding

`php artisan db:seed` runs `SpecialistSeeder` then `DatabaseSeeder`. Demo accounts (password: `password`):

| Email | Role |
|---|---|
| `admin@gmail.com` | admin |
| `dokter@gmail.com` | dokter |
| `perawat.uk@gmail.com` | perawat_uk |
| `perawat@gmail.com` | perawat_biasa |

## Testing

- **Pest**, not plain PHPUnit: `php vendor/bin/pest tests/Feature/SomeTest.php`
- DB defaults to MySQL in `.env`. For SQLite in-memory, uncomment in `phpunit.xml`:
  ```xml
  <env name="DB_CONNECTION" value="sqlite"/>
  <env name="DB_DATABASE" value=":memory:"/>
  ```
- **`UserFactory` does NOT set `role`** — DB default (`perawat_biasa`) applies. Create with explicit role:
  ```php
  User::factory()->create(['role' => User::ROLE_DOKTER]);
  ```
- 5 custom feature tests (`SurgeryRequestTest`, `PatientAccessTest`, `DoctorScheduleTest`, `UkSurgeryRequestTest`, `UkOperatingRoomTest`) create fixtures inline — no Factories for those models yet.

## Architecture

- **4 roles** on `users.role`: `dokter`, `perawat_uk`, `perawat_biasa`, `admin`
- **Role helpers** on `User`: `isDoctor()`, `isNurse()`, `isUkNurse()`, `isRegularNurse()`, `isAdmin()`
- **Entrypoint**: `DashboardController@index` redirects by role to `doctor.dashboard`, `nurse-uk.dashboard`, `nurse-regular.dashboard`, or `admin.dashboard`
- **Access control**: 3 middleware aliases registered in `bootstrap/app.php` — `doctor`, `nurse-uk`, `nurse-regular` — each calls `abort_unless($user->isXxx(), 403)`. No Gates/Policies.
- **No custom Artisan commands**, **no CI**, **no custom service providers** beyond `AppServiceProvider` (empty).
- Diagnosis and procedure are free-text fields (`diagnosis_text`, `procedure_text`) on `surgery_requests` (migrations 2026_05_19 dropped old `diagnoses`/`procedures` tables).
- `RoomOperationRequestController` is **completely commented out** (dead code). The guideline route is also **commented out**.
- `php artisan storage:link` needed for file uploads stored to `public` disk.
- `Vite::refresh = true` enables hot reload on backend changes.

### Routes by group

| Prefix | Middleware | Names |
|---|---|---|
| `/doctor/*` | `doctor` | `doctor.dashboard`, `doctor.schedules.*`, `doctor.reports.*` |
| `/nurse-uk/*` | `nurse-uk` | `nurse-uk.dashboard`, `nurse-uk.patients.*`, `nurse-uk.requests.*`, `nurse-uk.schedules.*`, `nurse-uk.rooms.*`, `nurse-uk.doctors.*` |
| `/nurse-regular/*` | `nurse-regular` | `nurse-regular.dashboard`, `nurse-regular.patients.*`, `nurse-regular.surgery-requests.*` |
| `/admin/*` | — | `admin.dashboard`, `admin.users.*`, `admin.specialists.*` |
| `/api/icd/search` | auth | ICD autocomplete proxy |

### View directories

```
resources/views/
├── layouts/sidebars/{admin,doctor,nurse-uk,nurse-regular}.blade.php
├── nurse-uk/{dashboard,requests,rooms,schedules,patients,doctors}/
├── nurse-regular/{dashboard,patients,surgery-requests}/
├── doctor/{dashboard,schedules,reports,patients}/
├── admin/{users,specialists}/
├── surgery-requests/   # orphaned — nurse-regular views live in nurse-regular/
└── dashboard/          # admin dashboard
```

### ICD Autocomplete

`GET /api/icd/search?type=icd10|icd9&q=...` proxies to NIH Clinical Tables API (free, no key). Alpine.js component `x-data="icdAutocomplete('icd10')"` in `resources/js/app.js`.

## Conventions

- **Indonesian** UI text, status messages, route segments
- **Dark mode** via Tailwind `class` strategy (`darkMode: 'class'`)
- **Type hints**: `@var User $user` when accessing `Auth::user()`
- **`abort_unless()` inline** in controllers — no Gates/Policies
- **Editorconfig**: 4-space indent, LF line endings
