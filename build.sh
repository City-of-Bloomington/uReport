#!/bin/bash
DIR=`pwd`
BUILD=./build
DIST=$BUILD/dist

if [ ! -d $BUILD ]
	then mkdir $BUILD
fi

if [ ! -d $DIST ]
	then mkdir $DIST
fi

# Compile the core CSS
cd $DIR/crm/public/css
./build_css.sh

# Compile the COB Theme
cd $DIR/crm/data/Themes/COB/public/css
./build_css.sh

cd $DIR/crm/data/Themes/COB/vendor/City-of-Bloomington/factory-number-one
./gulp

# Compile the Lanague files
cd $DIR/crm/language
./build_lang.sh

cd $DIR
rsync -rlv --exclude-from=./buildignore --delete ./ ./build/

tar czvf $DIST/uReport.tar.gz --transform=s/build/uReport/ $BUILD
