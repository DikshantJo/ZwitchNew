#!/bin/bash

# Database Backup Script for Bagisto
# This script creates a backup of your database

echo "🗄️ Starting database backup..."

# Get current date and time
DATE=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="database_backups"
BACKUP_FILE="bagisto_backup_$DATE.sql"

# Create backup directory if it doesn't exist
mkdir -p $BACKUP_DIR

# Load environment variables
if [ -f .env ]; then
    export $(cat .env | grep -v '^#' | xargs)
fi

# Create backup
echo "📦 Creating backup: $BACKUP_FILE"
mysqldump -h $DB_HOST -P $DB_PORT -u $DB_USERNAME -p$DB_PASSWORD $DB_DATABASE > $BACKUP_DIR/$BACKUP_FILE

# Check if backup was successful
if [ $? -eq 0 ]; then
    echo "✅ Backup completed successfully: $BACKUP_DIR/$BACKUP_FILE"
    
    # Compress backup
    gzip $BACKUP_DIR/$BACKUP_FILE
    echo "🗜️ Backup compressed: $BACKUP_DIR/$BACKUP_FILE.gz"
    
    # Keep only last 10 backups
    ls -t $BACKUP_DIR/*.gz | tail -n +11 | xargs -r rm
    echo "🧹 Old backups cleaned up"
else
    echo "❌ Backup failed!"
    exit 1
fi

echo "🎉 Database backup process completed!" 