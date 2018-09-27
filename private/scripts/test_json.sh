#!/bin/bash

####################################################################
## This test script is used to validate the syntax of each .json file
## contained within the theme directory provided using the
## json_decode() function of PHP. 
####################################################################

THEME_NAME="barrel-base"
INVALID_FILES=""
COUNT=0

echo "Validating json files in the ${THEME_NAME} theme"
while read -r line; do
    php -r "if ( ! json_decode(file_get_contents('$line')) ) { exit(1); }"
    if [ $? -ne 0 ]; then
        if [ $COUNT -eq 0 ]; then
            INVALID_FILES+="INVALID JSON FILES:"
        fi
        INVALID_FILES+="${line}"
        COUNT=$(($COUNT+1))
    fi
done <<< "$(rg wp-content/themes/${THEME_NAME} --files --type json)"

if [ -z "$INVALID_FILES" ]; then
    echo "All JSON files look ok. Moving on ..."
    exit
else
    echo "JSON Syntax errors were found in the ${THEME_NAME} theme. Please check the snytax of the following files and fix any errors."
    echo $INVALID_FILES
    exit 1;
fi


