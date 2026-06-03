#!/bin/bash
set -e

# --- Configuration ---

# Solr
SOLR_HOST="${SOLR_HOST:-solr}"
SOLR_PORT="${SOLR_PORT:-8983}"
SOLR_CORE="${SOLR_CORE:-ureport}"

# App
INDEX_SCRIPT="${INDEX_SCRIPT:-crm/scripts/solr/indexSearch.php}"

# Timeout (optional)
TIMEOUT="${WAIT_TIMEOUT:-60}"  # seconds
SECONDS=0

CONFIG_FILE="crm/data/site_config.php"

if [ ! -f "$CONFIG_FILE" ]; then
  echo "ERROR: Missing config file: "
  echo "$CONFIG_FILE"
  exit 1
fi

# --- Wait for MySQL ---
./infra/wait-for-db.sh

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
./infra/compile-assets.sh

# --- Compile translations ---
./infra/compile-translations.sh

mkdir -p ./crm/data/media/2026/4/15
cp ./infra/abc123def4567.png ./crm/data/media/2026/4/15/abc123def4567
# --- Start the main app command ---
exec "$@"