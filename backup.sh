#!/usr/bin/env bash

set -e

PROJECT_DIR="$(cd "$(dirname "$0")" && pwd)"
BACKUP_DIR="$PROJECT_DIR/backups"
DATE="$(date +%Y%m%d_%H%M%S)"

mkdir -p "$BACKUP_DIR"

echo "========================================"
echo " CHEGENIROOSTA BACKUP"
echo "========================================"

echo
echo "Project: $PROJECT_DIR"
echo "Date: $DATE"

if [ -f "$PROJECT_DIR/database/database.sqlite" ]; then

    cp "$PROJECT_DIR/database/database.sqlite" \
       "$BACKUP_DIR/database_$DATE.sqlite"

    echo "Database backup: OK"

else
    echo "Database file not found."
fi

if [ -d "$PROJECT_DIR/storage/app/public" ]; then

    tar -czf "$BACKUP_DIR/storage_$DATE.tar.gz" \
        -C "$PROJECT_DIR/storage/app" public

    echo "Storage backup: OK"

else
    echo "Storage directory not found."
fi

echo
echo "Backup files:"
ls -lh "$BACKUP_DIR"

echo
echo "========================================"
echo " BACKUP COMPLETE"
echo "========================================"
