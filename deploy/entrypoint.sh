#!/usr/bin/env bash
# =====================================================================
# Container entrypoint
# 1. Substitute ${PORT} placeholders in Apache configs
# 2. รัน auto-installer (idempotent — ถ้าติดตั้งแล้วจะ skip)
# 3. Start Apache foreground
# =====================================================================
set -e

PORT="${PORT:-8080}"

echo "════════════════════════════════════════════════"
echo "  Expense Memo System — Container Boot"
echo "════════════════════════════════════════════════"
echo "PORT: ${PORT}"
echo "APP_ENV: ${APP_ENV:-production}"
echo "AUTO_INSTALL: ${AUTO_INSTALL:-true}"
echo ""

cd /var/www/html

# ─── Substitute PORT in Apache configs ───
# Apache doesn't expand env vars in config files unless explicitly defined,
# so we sed-replace placeholders at runtime
echo "🔵 Configuring Apache for port ${PORT}..."
sed -i "s|\${PORT}|${PORT}|g" /etc/apache2/ports.conf
sed -i "s|\${PORT}|${PORT}|g" /etc/apache2/sites-available/000-default.conf

# ─── Run auto-installer ───
if [ "${AUTO_INSTALL:-true}" = "true" ]; then
    echo "🔵 Running auto-installer..."
    php /var/www/html/deploy/auto-install.php || {
        echo "⚠️  Auto-install failed — continuing anyway"
        echo "    (Visit /setup.php manually if needed)"
    }
fi

# ─── Ensure storage permissions ───
mkdir -p /var/www/html/storage/uploads /var/www/html/storage/backups
chown -R www-data:www-data /var/www/html/storage 2>/dev/null || true
chmod -R 775 /var/www/html/storage 2>/dev/null || true

echo ""
echo "🚀 Starting Apache on port ${PORT}..."
echo "════════════════════════════════════════════════"

# Apache foreground
exec apache2-foreground
