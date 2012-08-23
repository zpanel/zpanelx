#!/bin/sh

BASE=/usr
CONVERTER=$BASE/bin/ffmpeg

export LD_LIBRARY_PATH=$LD_LIBRARY_PATH:$BASE/lib

# first arg is mandatory
echo "source $1 target $2"

if [ -f "$1" ] ; then
	# ok
	echo "source file ok";
else
	echo "can't find source $1";
	exit 1;
fi

if [ -f "$2" ] ; then
	# if result file already exists - do nothing
	echo "target $2 already exist";
	exit 0;
fi

# reconvert
$CONVERTER -i $1 $2

exit 0
