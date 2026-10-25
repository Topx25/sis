# SiS — Superior Integrated Solutions

Backend **API + CMS** for the Superior Integrated Solutions website (industrial IoT, SCADA & electrical engineering — MENA region).

Built with **Laravel 13 / PHP 8.3**, Sanctum auth, Spatie Translatable (EN/AR), and a Vite + Tailwind asset pipeline. It exposes a versioned JSON API (`/api/v1`) split into **Public** (content the marketing site consumes) and **Admin** (token-protected content management) namespaces.

## Requirements

- PHP **8.3+**
- Composer 2
- Node 20+ / npm
- A database (SQLite by default; MySQL supported)

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed      # SQLite file is created automatically
npm install
npm run build                   # or: npm run dev
php artisan serve
```

The `composer dev` script runs the server, queue, logs and Vite together.

## Configuration

All config comes from `.env` (never commit it). Key values:

| Variable | Notes |
|---|---|
| `APP_KEY` | generate with `php artisan key:generate` |
| `APP_DEBUG` | **must be `false` in production** |
| `DB_CONNECTION` | `sqlite` (default) or `mysql` + `DB_*` |
| `APP_LOCALE` / `APP_FALLBACK_LOCALE` | `en` / `ar` |

## Authentication

Admin auth uses Laravel Sanctum personal access tokens:

- `POST /api/v1/admin/login` → `{ access_token, refresh_token }` (access token expires in 60 min).
- `POST /api/v1/admin/refresh` → rotates both tokens.
- The `refresh_token` is stored **hashed (SHA-256)** server-side; only the plaintext is returned to the client once.

## API

- **Public:** `routes/api.php` under `/api/v1` — hero, services, sectors, projects, partners, clients, SEO, contact form, etc.
- **Admin:** `/api/v1/admin/*` — CRUD for every content type, token-protected.
- Postman collections: [`docs/postman/`](docs/postman/).

## Tests

```bash
php artisan test
./vendor/bin/pint        # code style
```

## Structure

```
app/Http/Controllers/Api/v1/{Public,Admin}   versioned API controllers
app/Http/{Resources,Requests,Middleware}     output shaping, validation, locale/token
app/Services                                  business logic (e.g. AuthService)
app/Models                                    Eloquent models (translatable content)
database/migrations                           schema
docs/                                         API collections & internal plans
```

## Security notes

- Never commit `.env`, database dumps (`*.sqlite`, `*.zip`), or content backups — `.gitignore` blocks the common ones.
- Rotate all Sanctum tokens (`personal_access_tokens`) and the admin password if the database is ever exposed.
