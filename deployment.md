# Hostinger Shared Hosting Deployment Guide

This guide deploys the Laravel app securely on Hostinger by exposing only the `public/` files to the web.

## 1) Target Folder Layout

Assume your Hostinger home is `/home/USERNAME` and your subdomain document root is:

- `/home/USERNAME/public_html/subdomain/`

Deploy as:

```text
/home/USERNAME/
├── public_html/
│   ├── subdomain/                  # Web root (public only)
│   │   ├── index.php
│   │   ├── .htaccess
│   │   ├── build/ (if Vite assets are built)
│   │   └── ...other files from Laravel public/
│   └── cacc_system/                # App core (one level up from subdomain)
│       ├── app/
│       ├── bootstrap/
│       ├── config/
│       ├── database/
│       ├── resources/
│       ├── routes/
│       ├── storage/
│       ├── vendor/
│       ├── artisan
│       └── ...all non-public Laravel files
```

## 2) File Movement

1. Upload full project to a temporary location.
2. Copy **contents** of `public/` into `public_html/subdomain/`.
3. Place the rest of Laravel app files in `public_html/cacc_system/` (one level above `subdomain`).
4. Ensure `vendor/` exists there (`composer install --no-dev --optimize-autoloader` if needed).

## 3) Update `index.php` Paths

In `public_html/subdomain/index.php`, set:

- `../cacc_system/vendor/autoload.php`
- `../cacc_system/bootstrap/app.php`

This repo already includes that path configuration in `public/index.php` for this deployment layout.

## 4) Permissions

Set writable permissions for:

- `public_html/cacc_system/storage`
- `public_html/cacc_system/bootstrap/cache`

## 5) Environment Setup

Inside `public_html/cacc_system/.env` configure:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-subdomain.yourdomain.com`
- DB credentials, mail, API keys, etc.

Then run:

```bash
php artisan key:generate --force
php artisan migrate --force
```

## 6) Production Optimization

Use Composer script added in this project:

```bash
composer run optimize:production
```

It executes:

1. `php artisan optimize`
2. `php artisan config:cache`
3. `php artisan route:cache`
4. `php artisan view:cache`

## 7) HTTPS Enforcement

`public/.htaccess` has been updated to:

- force HTTPS redirects
- keep standard Laravel front-controller rewrites

If SSL is enabled in Hostinger and DNS is pointed correctly, HTTP requests will redirect to HTTPS automatically.
