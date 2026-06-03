#!/bin/bash
set -e

VERSION=$(cat /var/www/html/crm/VERSION | tr -d "[:space:]")

echo "Compiling main screen.scss..."
cd /var/www/html/crm/public/css
sass screen.scss "screen-${VERSION}.css"

echo "Compiling COB theme SCSS..."
mkdir -p /var/www/html/crm/data/Themes/COB/public/css
cd /var/www/html/crm/data/Themes/COB/public/css
sass screen.scss "screen-${VERSION}.css"

echo "Copying JS files..."
for f in $(find /var/www/html/crm/public -name '*.js' ! -name '*-*.js'); do
    cp "$f" "${f%.js}-${VERSION}.js"
done

echo "Copying COB theme image assets..."
cd /var/www/html/crm/data/Themes/COB
rsync -rl ./vendor/City-of-Bloomington/factory-number-one/src/static/img/ ./public/img/

echo "Assets compiled!"
