#!/bin/bash
find ../blocks/html -name "*.inc" | xargs xgettext -j -LPHP -ktranslate:1 \
--from-code=utf-8 -o ./messages.pot \
--package-name=uReport \
--package-version=1.9 \
--copyright-holder="City of Bloomington" \
--msgid-bugs-address=ureport@googlegroups.com
