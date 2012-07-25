#!/bin/sh
version=`cat VERSION`
name="oneapi-$version.tar"
tar -cvf $name examples.php examples oneapi tests.php tests
bzip2 -v $name
echo "Written to $name.bz2"
