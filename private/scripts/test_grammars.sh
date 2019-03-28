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

# Terminal colors
source ./private/scripts/colors.sh

ERRORS=0

echo "${YELLOW}Performing PHP syntax check...${DEFAULT}"
git diff --diff-filter=ACMR --name-only origin/master -- '*.php' | xargs -L1 php -d short_open_tag=Off -l
if [[ "$?" -ne 0 ]]; then
    echo "${RED}PHP syntax check failed!${DEFAULT}"
    ERRORS=$(($ERRORS+1))
fi
echo $OK

echo "${YELLOW}Performing JSON syntax check...${DEFAULT}"
bash private/scripts/test_json.sh
if [[ "$?" -ne 0 ]]; then
    echo "${RED}JSON syntax check failed!${DEFAULT}"
    ERRORS=$(($ERRORS+1))
fi
echo $OK

echo "${YELLOW}Changing directory to theme path...${DEFAULT}"
cd ./wp-content/themes/$THEME_NAME
if [[ "$?" -ne 0 ]]; then
    echo "${RED}Theme path is invalid!${DEFAULT}"
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
standard
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