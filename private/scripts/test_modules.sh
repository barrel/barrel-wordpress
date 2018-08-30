#!/bin/bash

####################################################################
## This script is used to test if each module in the modules directory
## contains a markup oriented file (html,php,liquid). This test was written 
## to be run in the GitLab CI/CD 'test' pipeline. Its purpose is to better
## force proper project scaffolding and ensure that each directory in the `modules`
## directory is, indeed, a module.
##
## TODO:
## - How to test MVC infratructures that write markup directly in .js files (vue, react)
####################################################################

# Variables
MODULE_DIRECTORY="./wp-content/themes/$THEME_NAME/modules"
NO_MARKUP=()

# Terminal colors
DEFAULT=$(tput setaf 7)
RED=$(tput setaf 1)
GREEN=$(tput setaf 2)
YELLOW=$(tput setaf 3)
BLUE=$(tput setaf 4)

echo "Validating modules for theme: $THEME_NAME"

# Loop through modules directory and check if each module contains a markup file
for path in ${MODULE_DIRECTORY}/*; do
    [ -d "${path}" ] || continue
    MODULE_NAME="$(basename "${path}")"
    MODULE_FILEPATH="${MODULE_DIRECTORY}/${MODULE_NAME}"

    if [ ! -e "${MODULE_FILEPATH}/${MODULE_NAME}.php" ] && [ ! -e "${MODULE_FILEPATH}/${MODULE_NAME}.html" ] && [ ! -e "${MODULE_FILEPATH}/${MODULE_NAME}.liquid" ] ; then
        NO_MARKUP+=("${MODULE_NAME}")
    fi
done

if [ ! ${#NO_MARKUP[@]} -eq 0 ]; then
    echo "The following module(s) do not contain a markup file. Each module should output markup.\n${YELLOW}"
    printf '%s\n' "${NO_MARKUP[@]}"
    echo ${DEFAULT}
    exit 1
else
    echo "All modules passed validation, moving on ..."
fi
