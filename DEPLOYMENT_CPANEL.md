# Koneva Laravel cPanel Deployment Checklist

This checklist is optimized for shared hosting with cPanel + MySQL.

## 1. Build and Upload

1. In local/dev machine, install dependencies:
    - `composer install --no-dev --optimize-autoloader`
2. Ensure writable directories are included:
    - `storage/`
    - `bootstrap/cache/`
3. Upload project to cPanel (outside public web root if possible), for example:
    - `/home/CPANEL_USER/koneva-laravel`
4. Point domain document root to Laravel `public/`:
    - `/home/CPANEL_USER/koneva-laravel/public`

If cPanel requires keeping current public root, copy contents of `public/` to `public_html/` and update `index.php` paths to `../koneva-laravel/vendor/autoload.php` and `../koneva-laravel/bootstrap/app.php`.

## 2. Environment

Set production env values in `.env`:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://your-domain.com`
- `DB_CONNECTION=mysql`
- `DB_HOST=localhost`
- `DB_PORT=3306`
- `DB_DATABASE=cpanel_db_name`
- `DB_USERNAME=cpanel_db_user`
- `DB_PASSWORD=strong_password`
- `SESSION_DRIVER=file`
- `CACHE_STORE=file`
- `QUEUE_CONNECTION=sync`
- `FILESYSTEM_DISK=public`
- `LOG_CHANNEL=daily`
- `LOG_LEVEL=warning`

Generate key once:

- `php artisan key:generate`

## 3. Database

1. Create MySQL database and user in cPanel.
2. Grant full privileges to that user on the app DB.
3. Run migrations:
    - `php artisan migrate --force`

## 4. Storage and Permissions

1. Create public storage symlink:
    - `php artisan storage:link`
2. Ensure these paths are writable by PHP process:
    - `storage`
    - `bootstrap/cache`

## 5. Optimization

Run after deploy:

- `php artisan config:cache`
- `php artisan route:cache`
- `php artisan view:cache`

If you change env/config/routes/views later:

- `php artisan optimize:clear`
- Re-run cache commands above.

## 6. Logging and Monitoring

- Use daily log files (`LOG_CHANNEL=daily`) to avoid oversized single logs.
- Periodically rotate/clean old logs if host limits are strict.
- Review `storage/logs/laravel.log` after each deployment.

## 7. Security Hardening

- Keep `.env` outside public web root if possible.
- Disable debug (`APP_DEBUG=false`) in production.
- Use strong DB and admin passwords.
- Keep at least 1 active superadmin account.
- Do not expose admin/superadmin creation publicly.

## 8. Post-Deploy Smoke Test

1. Open home page.
2. Login with admin and superadmin.
3. Verify these workflows:
    - Admin request triage
    - Admin deliverable upload + publish visibility
    - Superadmin admin-management guardrails
    - Billing + reporting pages
4. Confirm client sees only published + visible deliverables.

## 9. Rollback Plan

Before each deployment:

- Backup DB (phpMyAdmin export).
- Backup `storage/` (especially uploaded deliverables).

Rollback steps:

- Restore previous code snapshot.
- Restore DB dump.
- Run `php artisan optimize:clear`.

## 10. SSL + Google OAuth (Critical)

Google OAuth production callback must use HTTPS.

1. Enable SSL first in cPanel:
    - cPanel > SSL/TLS Status > Run AutoSSL
    - Verify site opens with `https://your-domain.com`
2. Force HTTPS redirect (if needed):
    - In domain docroot `.htaccess`, add redirect to HTTPS before Laravel rewrite rules.
3. Set production OAuth env:
    - `APP_URL=https://your-domain.com`
    - `GOOGLE_CLIENT_ID=...`
    - `GOOGLE_CLIENT_SECRET=...`
    - `GOOGLE_REDIRECT_URI=https://your-domain.com/auth/google/callback`
4. Register exact callback in Google Cloud Console:
    - OAuth 2.0 Client > Authorized redirect URIs
    - Add `https://your-domain.com/auth/google/callback`
5. Clear and rebuild Laravel caches after env update:
    - `php artisan optimize:clear`
    - `php artisan config:cache`
    - `php artisan route:cache`
    - `php artisan view:cache`
6. Test both buttons:
    - `Daftar dengan Google`
    - `Masuk dengan Google`

Notes:

- Callback URI must match exactly (scheme/domain/path), including HTTPS.
- If you switch between `www` and non-`www`, register both variants in Google Console and keep one canonical in `APP_URL`.
