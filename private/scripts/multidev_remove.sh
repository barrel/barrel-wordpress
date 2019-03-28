#!/bin/bash

####################################################################
## This script will remove a multidev environment and associated branch 
## from a provided pantheon environment
##
## This script should be run from within the theme directory using the 
## npm script: `npm run remove-multidev`
## 
####################################################################

# Variables - Need to be updated per project
# Terminal colors
source ./private/scripts/colors.sh

# CI_COMMIT_REF_NAME, defined by GitLab CI or by user
# PANTHEON_SITE_ID, can be defined in environment variables, by flag, or by user
# ENVIRONMENT, can be defined with a flag, by user, or automatically

if [ -z ${CI_COMMIT_REF_NAME+x} ]; then
    echo "${YELLOW}Hmm... Looks like this is not being run in GitLab CI, what's the original branch name?${DEFAULT}"
    read CI_COMMIT_REF_NAME
fi
TARGET=$(echo $CI_COMMIT_REF_NAME | cut -d'/' -f2)
ENVIRONMENT=$(echo ${TARGET:0:11} | tr '[:upper:]' '[:lower:]') 

# Parameters 
for i in "$@"; do
case $i in
    -m=*|--multidev=*)
    ENVIRONMENT="${i#*=}"
    shift # past argument=value
    ;;
    -n=*|--name=*)
    PANTHEON_SITE_ID="${i#*=}"
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

# Check for pantheon site name variable 
if [ -z ${PANTHEON_SITE_ID+x} ]; then
    echo "${YELLOW}Hmm... Looks like a variable was not defined. What is the Pantheon Site Name?${DEFAULT}"
    read PANTHEON_SITE_ID
fi

# Check for environment variable before moving forward 
# @see https://stackoverflow.com/questions/3601515/how-to-check-if-a-variable-is-set-in-bash
if [ -z ${ENVIRONMENT+x} ]; then
    echo "${YELLOW}Hmm... Looks like a variable was not defined. Which multidev would you like to remove?${DEFAULT}"
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
echo ""
echo "${GREEN}All finished here... goodbye.${DEFAULT}"

exit