#!/bin/bash

if [ $# -lt 2 ]; then
	echo "Usage:" $0 "copy_from_lang new_lang"
	exit 1
fi 

from=$1
to=$2

for dir in `ls -1 bencher/programs`; do
	echo "Entering" $dir
	for file in `ls -1 bencher/programs/$dir`; do
		re="${from}$"
		if [[ $file =~ $re ]]; then
			new=${file//$from/$to}
			echo "	" copy $file to $new
			cp bencher/programs/$dir/{$file,$new}			
		fi
	done
done
