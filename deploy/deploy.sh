#!/usr/bin/env bash
# =====================================================================
# Auto-Deploy Script — สำหรับ VPS / cPanel ที่มี SSH access
# วิธีใช้: bash deploy.sh
# =====================================================================
set -euo pipefail

# ───────────── Configuration ─────────────
APP_NAME="Expense Memo System"
APP_DIR="${APP_DIR:-$(cd "$(dirname "$0")/.." && pwd)}"
UPLOAD_DIR="$APP_DIR/storage/uploads"
BACKUP_DIR="$APP_DIR/storage/backups"
GIT_BRANCH="${GIT_BRANCH:-main}"

echo "=============================================="
echo "  Deploying: $APP_NAME"
echo "  Path:      $APP_DIR"
echo "  Branch:    $GIT_BRANCH"
echo "  Time:      $(date)"
echo "=============================================="

cd "$APP_DIR"

# ───────────── Step 1: Backup database ก่อน ─────────────
if [ -f "$APP_DIR/deploy/backup.sh" ]; then
    echo ""
    echo "🔵 Step 1/5 — Backing up database..."
    bash "$APP_DIR/deploy/backup.sh"
fi

# ───────────── Step 2: Pull latest code ─────────────
if [ -d "$APP_DIR/.git" ]; then
    echo ""
    echo "🔵 Step 2/5 — Pulling latest code..."
    git fetch --all
    git reset --hard "origin/$GIT_BRANCH"
else
    echo ""
    echo "🟡 Step 2/5 — Skip git (not a git repo)"
fi

# ───────────── Step 3: Permissions ─────────────
echo ""
echo "🔵 Step 3/5 — Setting permissions..."
mkdir -p "$UPLOAD_DIR" "$BACKUP_DIR"
chmod -R 755 "$APP_DIR"
chmod -R 775 "$UPLOAD_DIR" "$BACKUP_DIR"
find "$APP_DIR" -type f -name "*.php" -exec chmod 644 {} \;
find "$APP_DIR" -type f -name "*.sh"  -exec chmod 755 {} \;
[ -f "$APP_DIR/.htaccess" ] && chmod 644 "$APP_DIR/.htaccess"

# ───────────── Step 4: Run migration (idempotent) ─────────────
if [ -f "$APP_DIR/deploy/db-migrate.php" ] && [ -f "$APP_DIR/config/database.php" ]; then
    echo ""
    echo "🔵 Step 4/5 — Running migrations..."
    php "$APP_DIR/deploy/db-migrate.php"
fi

# ───────────── Step 5: Clear cache & verify ─────────────
echo ""
echo "🔵 Step 5/5 — Clearing cache & verifying..."

# OPcache reset (ถ้ามี opcache_reset)
php -r 'function_exists("opcache_reset") && opcache_reset();' || true

# Health check
if php -l "$APP_DIR/public/index.php" > /dev/null 2>&1 || php -l "$APP_DIR/index.php" > /dev/null 2>&1; then
    echo "✅ Syntax check passed"
else
    echo "❌ Syntax check failed!"
    exit 1
fi

echo ""
echo "=============================================="
echo "  ✅ Deploy Complete!"
echo "=============================================="
echo "  Time elapsed: $SECONDS sec"
echo ""
echo "Next steps:"
echo "  - Visit your domain"
echo "  - Check: https://yourdomain.com/login"
echo "=============================================="
