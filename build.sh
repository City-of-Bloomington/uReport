#!/bin/bash
APPNAME=ureport
DIR=`pwd`
BUILD=$DIR/build

declare -a dependencies=(msgfmt node-sass node npm composer)
for i in "${dependencies[@]}"; do
    command -v $i > /dev/null 2>&1 || { echo "$i not installed" >&2; exit 1; }
done

if [ ! -d $BUILD ]
	then mkdir $BUILD
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
rsync -rl --exclude-from=$DIR/buildignore --delete $DIR/ $BUILD/$APPNAME
cd $BUILD
tar czf $APPNAME.tar.gz $APPNAME
