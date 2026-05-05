#!/usr/bin/env bash
# =====================================================================
# Container entrypoint
# 1. รัน auto-installer (idempotent — ถ้าติดตั้งแล้วจะ skip)
# 2. Start Apache foreground
# =====================================================================
set -e

echo "════════════════════════════════════════════════"
echo "  Expense Memo System — Container Boot"
echo "════════════════════════════════════════════════"
echo "PORT: ${PORT:-8080}"
echo "APP_ENV: ${APP_ENV:-production}"
echo "AUTO_INSTALL: ${AUTO_INSTALL:-true}"
echo ""

cd /var/www/html

# Run auto-installer (creates schema, seeds data, ensures admin user)
if [ "${AUTO_INSTALL:-true}" = "true" ]; then
    echo "🔵 Running auto-installer..."
    php /var/www/html/deploy/auto-install.php || {
        echo "⚠️  Auto-install failed — continuing anyway"
        echo "    (Visit /setup.php manually if needed)"
    }
fi

# Ensure storage permissions on every boot (volume mounts can reset these)
mkdir -p /var/www/html/storage/uploads /var/www/html/storage/backups
chown -R www-data:www-data /var/www/html/storage 2>/dev/null || true
chmod -R 775 /var/www/html/storage 2>/dev/null || true

echo ""
echo "🚀 Starting Apache on port ${PORT:-8080}..."
echo "════════════════════════════════════════════════"

# Apache foreground
exec apache2-foreground
