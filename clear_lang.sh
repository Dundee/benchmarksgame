#!/bin/bash

if [ $# -lt 1 ]; then
	echo "Usage: " $0 "lang"
	exit 1
fi

command="find bencher/ -name '*.${1}_dat'"
bash -c "$command"

echo -en "\e[1;31m"
echo -n "Really delete? [y/n]"
echo -en "\e[0m"
read r

if [ "$r" == "y" ]; then
	bash -c "$command -delete"
fi

