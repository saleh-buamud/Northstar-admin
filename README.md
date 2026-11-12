# Northstar Admin

A bilingual Laravel admin workspace for product, category, user, role, and permission management. Dashboard totals and the revenue chart are derived from persisted records; recording a sale atomically updates product stock.

## Requirements

- PHP 8.3+
- Composer
- Node.js and npm
- MySQL or PostgreSQL (SQLite is used by the test suite)

## Setup

Use the existing project `.env` and database connection. Then run:

```powershell
composer install
npm install
php artisan migrate
php artisan storage:link
npm run build
php artisan serve
```

Open `/register` on a fresh database to create the first administrator. This setup page is closed after the first account is created. Alternatively, set `ADMIN_NAME`, `ADMIN_EMAIL`, and a strong `ADMIN_PASSWORD` (at least 12 characters) in `.env`, then run `php artisan db:seed`.

The interface defaults to English and supports Arabic/RTL. Theme preference follows the operating system until the user chooses a theme, after which it is saved in browser storage.

## Demo data

After applying the additive schema updates with `php artisan migrate`, generate repeat-safe Northstar sample records with:

```powershell
php artisan db:seed --class="Database\Seeders\DemoDataSeeder" --no-interaction
```

The dedicated seeder adds 25 categories, 150 products, 80 users, default roles and permissions when missing, recorded sales, and activity history. Product and profile images use local SVG placeholders. Existing records are not deleted or updated, and existing role-permission assignments—including Super Admin assignments—are preserved. Running the command again does not duplicate its demo records.

## Verification

```powershell
php artisan test
vendor/bin/pint --test
npm run build
```
