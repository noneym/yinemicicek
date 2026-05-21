#!/usr/bin/env bash
set -e

cd /var/www/html

# 1) Bootstrap .env from .env.example if none mounted
if [ ! -f .env ]; then
    cp -n .env.example .env || true
fi

# 2) Generate APP_KEY if missing
if ! grep -qE '^APP_KEY=base64:' .env; then
    php artisan key:generate --force
fi

# 3) Ensure runtime dirs exist & are writable
mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache

# 4) Make storage symlink if missing
[ -L public/storage ] || php artisan storage:link || true

# 5) Wait briefly for DB and run migrations (non-fatal if unreachable)
echo "[entrypoint] Running migrations (best-effort)…"
for i in 1 2 3 4 5; do
    if php artisan migrate --force 2>&1; then
        break
    fi
    echo "[entrypoint] migrate attempt $i failed, retrying in 3s…"
    sleep 3
done

# 6) Cache config/routes/views for production speed
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
