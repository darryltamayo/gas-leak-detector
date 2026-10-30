#!/bin/bash
#
# Fix File Permissions for AC Controller System
# Run this via SSH after uploading to server
#
# Usage: bash fix-permissions.sh
#

echo "=========================================="
echo "Fixing File Permissions"
echo "=========================================="
echo ""

# Get current directory
BASEDIR=$(pwd)
echo "Working directory: $BASEDIR"
echo ""

# Fix directory permissions (755 = readable/executable by all, writable by owner)
echo "Setting directory permissions to 755..."
find . -type d -exec chmod 755 {} \;

# Fix file permissions (644 = readable by all, writable by owner)
echo "Setting file permissions to 644..."
find . -type f -exec chmod 644 {} \;

# Make storage directories writable (775)
echo "Setting storage directories to 775 (writable)..."
chmod 775 storage/cache/ 2>/dev/null || echo "  Note: storage/cache not found"
chmod 775 storage/logs/ 2>/dev/null || echo "  Note: storage/logs not found"

# Make .env more secure (600 = only owner can read/write)
echo "Securing .env file (600)..."
chmod 600 .env 2>/dev/null || echo "  Note: .env not found"

# Make shell scripts executable
echo "Making scripts executable..."
chmod +x fix-permissions.sh 2>/dev/null
chmod +x cron/execute_schedules.php 2>/dev/null

echo ""
echo "=========================================="
echo "Permission Fix Complete!"
echo "=========================================="
echo ""
echo "Summary:"
echo "  ✓ All directories: 755"
echo "  ✓ All files: 644"
echo "  ✓ storage/cache/: 775 (writable)"
echo "  ✓ storage/logs/: 775 (writable)"
echo "  ✓ .env: 600 (secure)"
echo ""
echo "Next steps:"
echo "1. Run test: https://your-domain.com/public/test-system.php"
echo "2. Check for permission warnings"
echo ""
