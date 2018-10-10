#!/bin/bash

####################################################################
## This script will remove a multidev environment and associated branch 
## from a provided pantheon environment
##
## This script should be run from within the theme directory using the npm script
## `npm run remove-multidev`
## 
####################################################################

# Variables - Need to be updated per project
# PANTHEON_SITE_ID, defined in environment variables
TARGET=$(echo $CI_COMMIT_REF_NAME | cut -d'/' -f2)
ENVIRONMENT=$(echo ${TARGET:0:11} | tr '[:upper:]' '[:lower:]') 

# Terminal colors
DEFAULT=$(tput setaf 7 -T)
RED=$(tput setaf 1 -T)
GREEN=$(tput setaf 2 -T)
YELLOW=$(tput setaf 3 -T)
BLUE=$(tput setaf 4 -T)

# Parameters 
for i in "$@"; do
case $i in
    -m=*|--multidev=*)
    ENVIRONMENT="${i#*=}"
    shift # past argument=value
    ;;
    --help)
    echo "Utility Usage:"
    echo "--"
    echo "This script can be run from within the theme using npm"
    echo "npm run remove-multidev"
    echo "--\n"
    echo "Arguments:"
    echo "-m | --multidev - Multidev name: -n=the-module"
    shift # past argument with no value
    ;;
    *)
	echo "Unknown option: ${i#*=}"
          # unknown option
    ;;
esac
done

# Check for environment variable before moving forward 
# @see https://stackoverflow.com/questions/3601515/how-to-check-if-a-variable-is-set-in-bash
if [ -z ${ENVIRONMENT+x} ]; then
    echo "${YELLOW}Hmm... Looks like you didn't provide a multidev name. Which multidev would you like to remove?${DEFAULT}"
    read ENVIRONMENT
fi

# Check if provided multidev exists
EXISTS=$(terminus multidev:list ${PANTHEON_SITE_ID} | grep "${ENVIRONMENT}")
STATUS=$?

if [ $STATUS -ne 0 ]; then
    echo "${RED}Error:${DEFAULT} Multidev <${ENVIRONMENT}> doesn't appear to exist on the ${PANTHEON_SITE_ID} pantheon account. Check your multidev name and try again."
    echo "${YELLOW}Exiting...${DEFAULT}"
    exit 1
fi

echo "Deleting multidev environment <${YELLOW}${PANTHEON_SITE_ID}${DEFAULT}>.<${YELLOW}${ENVIRONMENT}${DEFAULT}>..."
terminus multidev:delete ${PANTHEON_SITE_ID}.${ENVIRONMENT} --yes
MD_DELETED_STATUS=$?

if [ $MD_DELETED_STATUS -eq 0 ]; then
    echo "Attempting to remove remote branch pantheon/${ENVIRONMENT}..."
    git push -d pantheon ${ENVIRONMENT}
fi

echo "All finished here... goodbye."

exit