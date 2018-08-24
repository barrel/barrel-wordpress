#!/bin/bash

####################################################################
## This create module script is for creating an empty module
## This script can be run from within the theme directory using npm (see package.json)
##
## npm run create-module -- -n=$MODULE_NAME --e="$EXCLUDED FILES"
## 
## Assumptions:
## The module name passed to this script is all lower-case and hyphenated:
## example-module-name
##
## TODO:
## - Update the string santization variable to camelCase the module name
## - Refactor file creation logic into a singular function
####################################################################

# Variables
MODULE_PATH="./modules"

# Terminal colors
DEFAULT=$(tput setaf 7)
RED=$(tput setaf 1)
GREEN=$(tput setaf 2)
YELLOW=$(tput setaf 3)
BLUE=$(tput setaf 4)

# handle arguments
for i in "$@"; do
case $i in
    -n=*|--name=*)
    MODULE_NAME="${i#*=}"
    shift # past argument=value
    ;;
    -e=*|--exclude=*)
    EXCLUDE="${i#*=}"
    shift # past argument=value
    ;;
    --help)
    echo "\n\nUtility Usage:"
    echo "This script can be run from anywhere within the theme directory using npm"
    echo "npm run create-module -- -n=MODULE_NAME -e=\"EXLCUDED FILES\"\n"
    echo "--\n"
    echo "Arguments:"
    echo "-n | --name - Module name: -n=the-module"
    echo "-e | --exclude - Files to exclude: -e=\"js php\"\n\n"

    shift # past argument with no value
    exit
    ;;
    *)
	echo "Unknown option: ${i#*=}"
          # unknown option
    exit
    ;;
esac
done

## Check if module name was provided before moving on
if [ -z ${MODULE_NAME+x} ]; then
    echo "${YELLOW}Hmm... Looks like you didn't set a module name yet. What would you like to name this module?${DEFAULT}"
    read MODULE_NAME
fi

## Update Module Path
MODULE_DIRECTORY="$MODULE_PATH/$MODULE_NAME"
MODULE_FILE="$MODULE_DIRECTORY/$MODULE_NAME"
SANITIZED_MODULE_NAME=`echo "$MODULE_NAME" | sed 's/[\._-]//g'`

# Create Directory
mkdir -p -- "$MODULE_PATH/$MODULE_NAME"

# Javascript File
if [[ $EXCLUDE = *"js"* ]]; then
    echo "${YELLOW}Exluding javascript file from new module, $MODULE_NAME${DEFAULT}"
elif [[ -e "$MODULE_FILE.js" ]]; then
    echo "${YELLOW}$MODULE_NAME.js already exists in the module directory, so we won't create a new one.${DEFAULT}"
else
cat <<EOF >$MODULE_FILE.js
/**
* Initializes the site's $SANITIZED_MODULE_NAME module.
* @constructor
* @param {Object} el - The site's $SANITIZED_MODULE_NAME container element.
*/
function $SANITIZED_MODULE_NAME (el) {
    this.el = el
}

export default $SANITIZED_MODULE_NAME
EOF
fi

# PHP File
if [[ $EXCLUDE = *"php"* ]]; then
    echo "${YELLOW}Exluding php file from new module, $MODULE_NAME${DEFAULT}"
elif [[ -e "$MODULE_FILE.php" ]]; then
    echo "${YELLOW}$MODULE_NAME.php already exists in the module directory, so we won't create a new one.${DEFUALT}"
else
cat <<EOF >$MODULE_FILE.php
<section class="$MODULE_NAME" data-module="$MODULE_NAME"></section>
EOF
fi

# CSS File
if [[ $EXCLUDE = *"css"* ]]; then
    echo "${YELLOW}Exluding css file from new module, $MODULE_NAME${DEFAULT}"
elif [[ -e "$MODULE_FILE.css" ]]; then
    echo "${YELLOW}$MODULE_NAME.css already exists in the module directory, so we won't create a new one.${DEFAULT}"
else
cat <<EOF >$MODULE_FILE.css
/* styles for $MODULE_NAME */
EOF
fi

# README File
if [[ -e "$MODULE_DIRECTORY/README.md" ]]; then
    echo "${YELLOW}README.md already exists in the module directory, so we won't create a new one.${DEFAULT}"
else
cat <<EOF >$MODULE_DIRECTORY/README.md
# Summary
Replace the contents of this file to explain some of the less obvious aspects about this module.
Be sure to update the summary and explain a little bit about why this module exists (what is it used for?).

# Inclusion
This module is used on the following templates and sections:

- Module Name
- Template Name
  - Notes about how it might be included

# Parameters
This modules accepts the follwing arguments:
- \`\$argument\` (type = string): Argument description
- \`\$required\` (type = string) - required: Argument description
EOF
fi

echo "${YELLOW}All finished! Your module should now be located in $MODULE_DIRECTORY (relative to the theme's directory).${DEFAULT}"

exit