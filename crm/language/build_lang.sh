#!/bin/bash
DIR=`pwd`
for LANG in */*
do
    cd $LANG
    msgfmt -cv *.po
    cd $DIR
done