#!/bin/bash

####################################################################
## This test script is used to validate the syntax of each .json file
## contained within the theme directory provided using the
## json_decode() function of PHP. 
####################################################################

SCRIPT_PATH="`dirname \"$0\"`"
INVALID_FILES=""

# Terminal colors
source $SCRIPT_PATH/colors.sh

## Check if module name was provided before moving on
if [ -z ${THEME_NAME+x} ]; then
    echo "${BLUE}Hmm... Something is missing. What is the Theme Name?${DEFAULT}"
    read THEME_NAME
fi

THEME_LOCATION="wp-content/themes/$THEME_NAME"

if [[ `pwd` == *"$THEME_LOCATION"* ]]; then
    echo "${YELLOW}Theme path detected!${DEFAULT}"
else
    echo "${YELLOW}Changing directory to theme path: $THEME_LOCATION/ ${DEFAULT}"
    cd $THEME_LOCATION
    if [[ "$?" -ne 0 ]]; then
        echo "${RED}Theme path is invalid!${DEFAULT}"
        exit 2
    fi
fi
echo $OK

echo "${YELLOW}Validating json files in the $THEME_NAME theme${DEFAULT}"
while read -r line; do
    echo "Testing $line"
    php -r "if ( ! json_decode(file_get_contents('$line')) ) { exit(1); }"
    if [ $? -ne 0 ]; then
        INVALID_FILES+="$line\n"
    fi
done <<< "$(find . -type f -name '*.json' -not -path './node_modules/*')"

if [ -z "$INVALID_FILES" ]; then
    echo "${GREEN}All JSON files look ok.${DEFAULT}"
    exit
else
    echo "${RED}JSON Syntax errors were found in the $THEME_NAME theme. Please check the syntax of the following files and fix any errors.${DEFAULT}\n"
    echo "INVALID JSON FILES:${INVALID_FILES}"
    exit 1;
fi