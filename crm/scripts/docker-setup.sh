#!/bin/bash
set -e

# --- Configuration ---
# MySQL
DB_HOST="${DB_HOST:-db}"
DB_PORT="${DB_PORT:-3306}"
DB_USER="${DB_USER:-ureport}"
DB_PASS="${DB_PASS:-ureport}"

# Solr
SOLR_HOST="${SOLR_HOST:-solr}"
SOLR_PORT="${SOLR_PORT:-8983}"
SOLR_CORE="${SOLR_CORE:-ureport}"

# App
INDEX_SCRIPT="${INDEX_SCRIPT:-crm/scripts/solr/indexSearch.php}"

# Timeout (optional)
TIMEOUT="${WAIT_TIMEOUT:-60}"  # seconds
SECONDS=0

# --- Wait for MySQL ---
echo "Waiting for MySQL at $DB_HOST:$DB_PORT..."
until mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" --ssl=0 -e "SELECT 1"; do
    if [ $SECONDS -ge $TIMEOUT ]; then
        echo "Timeout waiting for MySQL"
        exit 1
    fi
    echo "MySQL not ready yet... sleeping 2s"
    sleep 2
done
echo "MySQL is ready!"

# --- Wait for Solr ---
echo "Waiting for Solr core '$SOLR_CORE' at $SOLR_HOST:$SOLR_PORT..."
until curl -s "http://$SOLR_HOST:$SOLR_PORT/solr/$SOLR_CORE/admin/ping?wt=json" | grep -q '"status":"OK"'; do
    if [ $SECONDS -ge $TIMEOUT ]; then
        echo "Timeout waiting for Solr"
        exit 1
    fi
    echo "Solr not ready yet... sleeping 2s"
    sleep 2
done
echo "Solr is ready!"

# --- Install Composer packages ---
echo "Installing Composer packages..."
composer install --no-interaction --prefer-dist --working-dir=crm
composer install --no-interaction --prefer-dist --working-dir=crm/data/Themes/COB

# --- Run Solr indexing ---
if [ -f "$INDEX_SCRIPT" ]; then
    echo "Running Solr indexing..."
    php "$INDEX_SCRIPT"
    echo "Solr indexing complete!"
else
    echo "Index script not found: $INDEX_SCRIPT"
fi

# --- Compile CSS/JS assets ---
./crm/scripts/compile-assets.sh

# --- Start the main app command ---
exec "$@"