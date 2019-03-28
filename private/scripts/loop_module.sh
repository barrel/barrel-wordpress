#!/bin/bash

####################################################################
## This script is used to loop through a module_list.csv file and create 
## modules based on that lists content. 

## File should be a simple list of module names delimited by new lines. 
##
## npm run create-modules
####################################################################

# Terminal colors
source ./private/scripts/colors.sh

if [[ -e "module_list.csv" ]]; then
    echo "${YELLOW}Found module list. Attempting to create modules...${DEFAULT}"
    while read line; do
        MODULE_NAME=$(echo $line | tr '[:upper:]' '[:lower:]' | tr " " -)
        npm run create-module -- -n=$MODULE_NAME
    done < module_list.csv
    exit 0;
else
    echo "${RED}Couldn't find the module list. You should have a module_list.csv file in the theme root. It should be a list of only module names delimited by line breaks. Please check the file and try again.${DEFAULT}"
    exit 1;
fi


