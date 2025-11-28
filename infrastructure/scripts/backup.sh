#!/bin/bash

# CSS Socios - Automated Backup Script
# This script creates backups of the database and user uploads

set -e

# Configuration
PROJECT_DIR="/var/www/css"
BACKEND_DIR="$PROJECT_DIR/backend"
BACKUP_DIR="/var/backups/css"
S3_BUCKET="css-backups"  # Change to your S3 bucket name
RETENTION_DAYS=30  # Keep backups for 30 days

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

# Timestamp for backup files
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
DATE=$(date +"%Y-%m-%d")

echo "🔒 CSS Socios - Backup automatique"
echo "==================================="
echo "Date: $DATE"
echo ""

# 1. Database Backup
echo "💾 Sauvegarde de la base de données..."
DB_BACKUP_FILE="$BACKUP_DIR/db_$TIMESTAMP.sql.gz"
mysqldump -u root css_database | gzip > "$DB_BACKUP_FILE"
echo "✓ Base de données sauvegardée: $DB_BACKUP_FILE"

# 2. Storage Backup (user uploads, etc.)
echo ""
echo "📁 Sauvegarde des fichiers uploads..."
STORAGE_BACKUP_FILE="$BACKUP_DIR/storage_$TIMESTAMP.tar.gz"
tar -czf "$STORAGE_BACKUP_FILE" -C "$BACKEND_DIR" storage/app/public
echo "✓ Fichiers sauvegardés: $STORAGE_BACKUP_FILE"

# 3. Environment configuration backup
echo ""
echo "⚙️  Sauvegarde de la configuration..."
ENV_BACKUP_FILE="$BACKUP_DIR/env_$TIMESTAMP.txt"
cp "$BACKEND_DIR/.env" "$ENV_BACKUP_FILE"
echo "✓ Configuration sauvegardée: $ENV_BACKUP_FILE"

# 4. Upload to S3 (optional - uncomment if using AWS S3)
# echo ""
# echo "☁️  Upload vers S3..."
# aws s3 cp "$DB_BACKUP_FILE" "s3://$S3_BUCKET/database/"
# aws s3 cp "$STORAGE_BACKUP_FILE" "s3://$S3_BUCKET/storage/"
# aws s3 cp "$ENV_BACKUP_FILE" "s3://$S3_BUCKET/config/"
# echo "✓ Backups uploadés sur S3"

# 5. Remove old backups (older than RETENTION_DAYS)
echo ""
echo "🧹 Nettoyage des anciens backups (> $RETENTION_DAYS jours)..."
find "$BACKUP_DIR" -name "*.sql.gz" -mtime +$RETENTION_DAYS -delete
find "$BACKUP_DIR" -name "*.tar.gz" -mtime +$RETENTION_DAYS -delete
find "$BACKUP_DIR" -name "*.txt" -mtime +$RETENTION_DAYS -delete
echo "✓ Anciens backups supprimés"

# Calculate backup sizes
DB_SIZE=$(du -h "$DB_BACKUP_FILE" | cut -f1)
STORAGE_SIZE=$(du -h "$STORAGE_BACKUP_FILE" | cut -f1)

echo ""
echo "==================================="
echo "✅ Backup terminé avec succès!"
echo "==================================="
echo ""
echo "Fichiers créés:"
echo "  - Base de données: $DB_BACKUP_FILE ($DB_SIZE)"
echo "  - Storage: $STORAGE_BACKUP_FILE ($STORAGE_SIZE)"
echo "  - Config: $ENV_BACKUP_FILE"
echo ""
echo "Emplacement: $BACKUP_DIR"
echo ""

# Log backup completion
echo "$(date '+%Y-%m-%d %H:%M:%S') - Backup completed successfully" >> "$BACKUP_DIR/backup.log"
