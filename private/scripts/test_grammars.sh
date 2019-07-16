#!/bin/bash

####################################################################
## This script is responsible for performing linting and syntax
## checks on js, css, and php files.
##
## - PHP: uses php-cli and checks all php files changed since master
## - CSS: uses stylelint with stylelint-config-standard config
## - JS : uses standardjs for code style enforcement
## - ECL: uses editorconfig to check that files match the rules
## - BUILD: uses node to install and run theme build process
##
## Note: Tests can be run locally by running `npm test` command. 
##
## Required Environment Variables:
## - $THEME_NAME (`export THEME_NAME="theme-name"`)
####################################################################

ERRORS=0
ROOT_PATH=$(git rev-parse --show-toplevel)
SCRIPT_PATH="`dirname \"$0\"`"

# Terminal colors
source $SCRIPT_PATH/colors.sh
cd $ROOT_PATH

echo "${YELLOW}Performing PHP syntax check...${DEFAULT}"
git diff --diff-filter=ACMR --name-only origin/master -- '*.php' | xargs -L1 php -d short_open_tag=Off -l
if [[ "$?" -ne 0 ]]; then
    echo "${RED}PHP syntax check failed!${DEFAULT}"
    ERRORS=$(($ERRORS+1))
fi
echo $OK

# handle arguments
for i in "$@"; do
case $i in
    -t=*|--themename=*)
    THEME_NAME="${i#*=}"
    shift # past argument=value
    ;;
    *)
    echo "Unknown option: ${i#*=}"
    # unknown option
    ;;
esac
done

WP_CONTENT="wp-content"
THEMES_DIR="./$WP_CONTENT/themes"

if [ "$THEME_NAME" == "" ]; then 
    if [ -d "$WP_CONTENT" ]; then
        # assume theme is the same as project
        THEME_NAME=$(basename $(pwd))
        echo "${YELLOW}Checking to see if '$THEME_NAME' exists...${DEFAULT}"
        if ! [ -d "$THEMES_DIR/$THEME_NAME" ]; then 
            echo "${BLUE}Hmm... Something is missing. What is the Theme Name?${DEFAULT}"
            read THEME_NAME
        else
            echo "${YELLOW}Theme '$THEME_NAME' exists...${DEFAULT}"
        fi
        export THEME_NAME="$THEME_NAME"
    fi
fi
THEME_LOCATION="$THEMES_DIR/$THEME_NAME"

if [[ `pwd` == *"$THEME_LOCATION"* ]]; then
    echo "${YELLOW}Theme path detected!${DEFAULT}"
else
    echo "${YELLOW}Changing directory to theme path: $THEME_LOCATION/ ${DEFAULT}"
    cd $THEME_LOCATION
    if [[ "$?" -ne 0 ]]; then
        echo "${RED}Theme path is invalid!${DEFAULT}"
        ERRORS=$(($ERRORS+1))
    fi
fi
echo $OK

echo "${YELLOW}Performing JSON syntax check...${DEFAULT}"
npm run test:json_lint
if [[ "$?" -ne 0 ]]; then
    echo "${RED}JSON syntax check failed!${DEFAULT}"
    ERRORS=$(($ERRORS+1))
fi
echo $OK

echo "${YELLOW}Installing theme dependencies...${DEFAULT}"
npm ci
if [[ "$?" -ne 0 ]]; then
    echo "${RED}Dependency installation failed!${DEFAULT}"
    ERRORS=$(($ERRORS+1))
fi
echo $OK

echo "${YELLOW}Testing js against standardjs...${DEFAULT}"
npm run test:js_lint
if [[ "$?" -ne 0 ]]; then
    echo "${RED}Conformance to standardjs failed!${DEFAULT}"
    ERRORS=$(($ERRORS+1))
fi
echo $OK

echo "${YELLOW}Testing css against stylelint...${DEFAULT}"
npm run test:css_lint
if [[ "$?" -ne 0 ]]; then
    echo "${RED}Conformance to stylelint failed!${DEFAULT}"
    ERRORS=$(($ERRORS+1))
fi
echo $OK

echo "${YELLOW}Testing files against editorconfig...${DEFAULT}"
npm run test:editorconfig
if [[ "$?" -ne 0 ]]; then
    echo "${RED}Conformance to editorconfig failed!${DEFAULT}"
    ERRORS=$(($ERRORS+1))
fi
echo $OK

echo "${YELLOW}Tallying sum of failures...${DEFAULT}"
if [[ "$ERRORS" -gt "0" ]]; then 
    echo "${RED}There were $ERRORS errors encountered! Please review the errors above.${DEFAULT}"
    exit $ERRORS
fi

echo "${GREEN}Grammar and sanity checks complete!${DEFAULT}"
exit 0