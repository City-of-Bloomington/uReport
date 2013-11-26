#!/bin/bash
BUILD=./build
DIST=./dist

if [ ! -d $BUILD ]
	then mkdir $BUILD
fi

if [ ! -d $DIST ]
	then mkdir $DIST
fi

rsync -rlv --exclude-from=./buildignore --delete ./ ./build/

tar czvf $DIST/uReport.tar.gz --transform=s/build/uReport/ $BUILD
