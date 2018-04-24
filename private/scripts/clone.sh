#!/bin/bash

BASE_THEME="barrel-base"
THEME_NAME=""

# handle arguments
for i in "$@"; do
case $i in
    -t=*|--themename=*)
    THEME_NAME="${i#*=}"
    shift # past argument=value
    ;;
    --help)
    echo "Utility Usage:"
    echo "--"
    echo "clone.sh -t=THEME_NAME"
    shift # past argument with no value
    ;;
    *)
	echo "Unknown option: ${i#*=}"
          # unknown option
    ;;
esac
done

if [ "$THEME_NAME" == "" ]; then 
	echo ""
	echo "Please either define the variable for THEME_NAME"
	echo "or supply the theme name as an argument: -t=theme-name"
	exit
fi

# this is to always be run from the root of the project
CWD=$(pwd)
echo "Current working directory is: $CWD"

echo "Copying theme files..."
cp -R ./wp-content/themes/$BASE_THEME ./wp-content/themes/$THEME_NAME

echo "You should replace these files..."
grep -rnw wp-content/themes/$THEME_NAME/ -e "barrel-base"

# TODO
# What needs to be grep/replaced within the theme itself?