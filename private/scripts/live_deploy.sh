#!/bin/bash

####################################################################
## This script will auto-deploy to a Pantheon site's DEV and TEST 
## environments, which includes the pantheon/master remote/branch. 
## Assumes CI variables populated and the before_script properly 
## adds a 'pantheon' remote to the git repo.
##
## Variables:
## - $PANTHEON_SITE_ID, defined in environment variables or by user
## - $ENVIRONMENT, defined statically
####################################################################

ENVIRONMENT="dev"
SCRIPT_PATH="`dirname \"$0\"`"

# Terminal colors
source $SCRIPT_PATH/colors.sh

if [ -z ${PANTHEON_SITE_ID+x} ]; then
    echo -e "${BLUE}Hmm... Something is missing. What is the Pantheon Site Name?${DEFAULT}"
    read PANTHEON_SITE_ID
fi

# assumes pantheon/master is on parity with gitlab/master and 
# assumes pantheon/environments/test is on parity with pantheon/environments/dev
echo "${YELLOW}Deploying 'test' to 'live' on Pantheon...${DEFAULT}"
terminus env:deploy -- $PANTHEON_SITE_ID.live
if [[ "$?" -ne 0 ]]; then
    exit 1
fi
echo $DONE

exit 0