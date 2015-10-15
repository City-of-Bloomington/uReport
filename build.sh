#!/bin/bash
DIR=`pwd`
BUILD=./build
DIST=./dist

if [ ! -d $BUILD ]
	then mkdir $BUILD
fi

if [ ! -d $DIST ]
	then mkdir $DIST

fi

# Compile the Lanague files
cd $DIR/crm/language
./build_lang.sh
cd $DIR

rsync -rlv --exclude-from=./buildignore --delete ./ ./build/

tar czvf $DIST/uReport.tar.gz --transform=s/build/uReport/ $BUILD
