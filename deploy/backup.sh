#!/usr/bin/env bash
# =====================================================================
# Database Backup Script — รันผ่าน cron หรือ manual
# ตั้ง cron ใน cPanel: 0 2 * * * /home/user/expense-memo-system/deploy/backup.sh
# =====================================================================
set -euo pipefail

APP_DIR="${APP_DIR:-$(cd "$(dirname "$0")/.." && pwd)}"
BACKUP_DIR="$APP_DIR/storage/backups"
CONFIG="$APP_DIR/config/database.php"
KEEP_DAYS="${KEEP_DAYS:-30}"   # เก็บ backup ย้อนหลัง 30 วัน

mkdir -p "$BACKUP_DIR"

if [ ! -f "$CONFIG" ]; then
    echo "❌ Database config not found: $CONFIG"
    exit 1
fi

# Parse credentials จาก config/database.php (require php)
DB_HOST=$(php -r "\$c=require '$CONFIG'; echo \$c['host'];")
DB_PORT=$(php -r "\$c=require '$CONFIG'; echo \$c['port'];")
DB_NAME=$(php -r "\$c=require '$CONFIG'; echo \$c['database'];")
DB_USER=$(php -r "\$c=require '$CONFIG'; echo \$c['username'];")
DB_PASS=$(php -r "\$c=require '$CONFIG'; echo \$c['password'];")

TIMESTAMP=$(date '+%Y%m%d_%H%M%S')
BACKUP_FILE="$BACKUP_DIR/db_${DB_NAME}_${TIMESTAMP}.sql.gz"

echo "Backing up $DB_NAME → $BACKUP_FILE"

mysqldump \
    --host="$DB_HOST" \
    --port="$DB_PORT" \
    --user="$DB_USER" \
    --password="$DB_PASS" \
    --single-transaction \
    --quick \
    --routines \
    --triggers \
    --no-tablespaces \
    "$DB_NAME" | gzip > "$BACKUP_FILE"

echo "✅ Backup saved: $(du -h "$BACKUP_FILE" | cut -f1)"

# ลบ backup เก่ากว่า $KEEP_DAYS วัน
find "$BACKUP_DIR" -name "db_*.sql.gz" -type f -mtime +$KEEP_DAYS -delete
echo "🧹 Cleaned backups older than $KEEP_DAYS days"
