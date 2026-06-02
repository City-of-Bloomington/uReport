#!/usr/bin/env bash
set -euo pipefail

DB_HOST="${DB_HOST:-db}"
DB_PORT="${DB_PORT:-3306}"
DB_NAME="${DB_NAME:-ureport}"
DB_USER="${DB_USER:-ureport}"
DB_PASS="${DB_PASS:-ureport}"

TIMEOUT="${DB_WAIT_TIMEOUT:-60}"
START=$(date +%s)

echo "Waiting for MySQL at ${DB_HOST}:${DB_PORT}..."

MYSQL_VERSION="$(mysql --version)"

if echo "${MYSQL_VERSION}" | grep -qi mariadb; then
    SSL_FLAG="--skip-ssl"
else
    SSL_FLAG="--ssl-mode=DISABLED"
fi

until mysql \
    -h"${DB_HOST}" \
    -P"${DB_PORT}" \
    -u"${DB_USER}" \
    -p"${DB_PASS}" \
    "${DB_NAME}" \
    ${SSL_FLAG} -e "SELECT 1";
do
    NOW=$(date +%s)

    if [ $((NOW - START)) -ge "${TIMEOUT}" ]; then
        echo "Timed out waiting for MySQL database"
        exit 1
    fi
    echo "MySQL not ready yet... sleeping 2s"
    sleep 2
done

echo "MySQL is ready!"
