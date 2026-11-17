# Merr Inventory

Laravel 12 with an AdminLTE 3.2 dashboard and session authentication.

## Run locally

From `example-app`:

```powershell
composer install
# If .env does not exist, copy .env.example to .env and run php artisan key:generate.
# Configure your database credentials in .env.
php artisan migrate
php artisan app:create-admin
php artisan serve
```

Open http://127.0.0.1:8000/login and use the account you created. Passwords are entered privately in the terminal. There are no default admin credentials or public registration. All provisioned users have dashboard access; separate roles are not implemented.

AdminLTE, Font Awesome, Bootstrap and jQuery load from pinned jsDelivr CDN URLs, so the UI needs internet access. No Node build is required for these Blade pages. The dashboard reads counts and recent medicines from the existing inventory tables; inventory editing is outside this authentication starter.

```powershell
php artisan test
```

Tests use an isolated in-memory SQLite database.
