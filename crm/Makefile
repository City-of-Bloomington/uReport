SHELL := /bin/bash
APPNAME=ureport

REQS := sassc msgfmt
K := $(foreach r, ${REQS}, $(if $(shell command -v ${r} 2> /dev/null), '', $(error "${r} not installed")))

LANGUAGES := $(wildcard language/*/LC_MESSAGES)
JAVASCRIPT := $(shell find public -name '*.js' ! -name '*-*.js')
VERSION := $(shell cat VERSION | tr -d "[:space:]")
COMMIT := $(shell git rev-parse --short HEAD)

default: test clean compile package

clean:
	rm -Rf ../build
	mkdir ../build

	rm -Rf public/css/.sass-cache
	rm -Rf data/Themes/COB/public/css/.sass-cache
	for f in $(shell find data/Themes -name 'screen-*.css*'); do rm $$f; done
	for f in $(shell find public/css  -name 'screen-*.css*'); do rm $$f; done
	for f in $(shell find public/js   -name '*-*.js'       ); do rm $$f; done

compile:
	cd public/css                 && sassc -t compact -m screen.scss screen-${VERSION}.css
	cd data/Themes/COB/public/css && sassc -t compact -m screen.scss screen-${VERSION}.css
	for f in ${JAVASCRIPT}; do cp $$f $${f%.js}-${VERSION}.js; done
	for f in $(LANGUAGES); do \
		msgfmt -cv $$f/errors.po -o $$f/errors.mo; \
		msgfmt -cv $$f/labels.po -o $$f/labels.mo; \
		msgfmt -cv $$f/messages.po -o $$f/messages.mo; \
	done

package:
	cd data/Themes/COB && rsync -rl ./vendor/City-of-Bloomington/factory-number-one/src/static/img/ ./public/img/
	rsync -rl --exclude-from=buildignore --delete ./ ../build/$(APPNAME)
	cd ../build && tar czf $(APPNAME).tar.gz $(APPNAME)

test:
	vendor/bin/phpstan analyse -l 5
	vendor/bin/phpunit -c src/Test/phpunit.xml --testsuite Unit
