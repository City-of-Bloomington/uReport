#!/bin/bash
for f in $(find *.po); do
    msgfmt -cvo ${f%.po}.mo $f
done
