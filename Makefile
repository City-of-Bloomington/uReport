APPNAME=ureport

SASS := $(shell command -v sassc 2> /dev/null)
MSGFMT := $(shell command -v msgfmt 2> /dev/null)

LANGUAGES := $(wildcard crm/language/*/LC_MESSAGES)

default: clean compile package

deps:
ifndef SASS
	$(error "pysassc is not installed")
endif
ifndef MSGFMT
	$(error "msgfmt is not installed, please install gettext")
endif

clean:
	rm -Rf build
	mkdir build

	rm -Rf crm/public/css/.sass-cache
	rm -Rf crm/data/Themes/COB/public/css/.sass-cache

compile: deps $(LANGUAGES)
	cd crm/public/css                 && sassc -mt compact -m screen.scss screen.css
	cd crm/data/Themes/COB/public/css && sassc -mt compact -m screen.scss screen.css

package:
	cd crm/data/Themes/COB && rsync -rl ./vendor/City-of-Bloomington/factory-number-one/src/static/img/ ./public/img/
	rsync -rl --exclude-from=buildignore --delete ./crm/ build/$(APPNAME)
	cd build && tar czf $(APPNAME).tar.gz $(APPNAME)

$(LANGUAGES): deps
	cd $@ && msgfmt -cv *.po
