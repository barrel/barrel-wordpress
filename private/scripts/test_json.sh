#!/bin/bash

####################################################################
## This test script is used to validate the syntax of each .json file
## contained within the theme directory provided using the
## json_decode() function of PHP. 
####################################################################

THEME_NAME="barrel-base"
INVALID_FILES=""

echo "Validating json files in the ${THEME_NAME} theme"
while read -r line; do
    php -r "if ( ! json_decode(file_get_contents('$line')) ) { exit(1); }"
    if [ $? -ne 0 ]; then
        INVALID_FILES+="${line}\n"
    fi
done <<< "$(find ./wp-content/themes/${THEME_NAME} -type f -name '*.json' -not -path './wp-content/themes/barrel-base/node_modules/*')"

if [ -z "$INVALID_FILES" ]; then
    echo "All JSON files look ok. Moving on ..."
    exit
else
    printf "\nJSON Syntax errors were found in the ${THEME_NAME} theme. Please check the snytax of the following files and fix any errors.\n"
    printf "\nINVALID JSON FILES:\n${INVALID_FILES}"
    exit 1;
fi