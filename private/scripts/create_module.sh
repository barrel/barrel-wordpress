#!/bin/bash

####################################################################
## This create module script is for creating an empty module
## This script can be run from within the theme directory using npm (see package.json)
## npm run create-module -- -n=$MODULE_NAME
## 
## Assumptions:
## The module name passed to this script is all lower-case and hyphenated:
## example-module-name
##
## TODO:
## - Update the string santization variable to camelCase the module name
## - Add options to not include certain files
## - Add an initial readme.md file
####################################################################

# Variables
MODULE_PATH="./modules"

# handle arguments
for i in "$@"; do
case $i in
    -n=*|--name=*)
    MODULE_NAME="${i#*=}"
    shift # past argument=value
    ;;
    --help)
    echo "Utility Usage:"
    echo "This script can be run from anywhere within the theme directory using npm"
    echo "--"
    echo "npm run create-module -- -n=MODULE_NAME"
    shift # past argument with no value
    ;;
    *)
	echo "Unknown option: ${i#*=}"
          # unknown option
    ;;
esac
done

## Update Module Path
MODULE_DIRECTORY="$MODULE_PATH/$MODULE_NAME"
MODULE_FILE="$MODULE_DIRECTORY/$MODULE_NAME"
SANITIZED_MODULE_NAME=`echo "$MODULE_NAME" | sed 's/[\._-]//g'`

# Create Directory
mkdir -p -- "$MODULE_PATH/$MODULE_NAME"

# Javascript File
if [[ ! -e "$MODULE_FILE.js" ]]; then
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
if [[ ! -e "$MODULE_FILE.php" ]]; then
cat <<EOF >$MODULE_FILE.php
<section class="$MODULE_NAME" data-module="$MODULE_NAME"></section>
EOF
fi

# CSS File
if [[ ! -e "$MODULE_FILE.css" ]]; then
cat <<EOF >$MODULE_FILE.css
/* styles for $MODULE_NAME */
EOF
fi

# README File
if [[ ! -e "$MODULE_DIRECTORY/README.md" ]]; then
cat <<EOF >$MODULE_DIRECTORY/README.md
# Summary
Use this file to explain some of the less obvious aspects about this module.
For example, maybe this module will expect some specific arguments, or it uses another
module for its output. Using markdown syntax, you should use this file to explain the things 
that might not be intuitive about this module.
EOF
fi

exit