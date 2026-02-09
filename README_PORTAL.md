# Cooperative Portal (Local XAMPP)

Quick setup for local development on XAMPP (Windows).

1) Create database `ccldo_dbs` in phpMyAdmin.

2) Copy `.env.example` to `.env` and set DB values:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ccldo_dbs
DB_USERNAME=root
DB_PASSWORD=
```

3) Install PHP dependencies (from `portal/`):

```bash
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

4) To run locally with XAMPP place the `portal` folder in `htdocs` and enable Apache+MySQL.

Notes:
- Migrations include cooperatives, profiles, news, documents, audit_logs and a users extension (role + soft deletes).
- Seeders create one gov admin and one cooperative admin and 34 sample cooperatives.
- Register any additional admin users via tinker or phpMyAdmin.

Security & accessibility:
- CSRF protection and form validation are used via Laravel features.
- Blade templates include semantic structure and Bootstrap for responsive layout.
