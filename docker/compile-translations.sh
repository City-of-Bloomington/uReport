#!/bin/bash

echo "Compiling translation files..."
find /var/www/html/crm/language -name "*.po" | while read file; do
  msgfmt "$file" -o "${file%.po}.mo"
done
