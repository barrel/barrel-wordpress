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

echo -e "${YELLOW}Checking which database should be used...${DEFAULT}"

LIVE_TAG=$(git ls-remote --tags pantheon | grep "pantheon_live_")
LIVE_EXISTS="$?"

TEST_TAG=$(git ls-remote --tags pantheon | grep "pantheon_test_")
TEST_EXISTS="$?"

if [ "$LIVE_EXISTS" -eq "0" ]; then
    DATA_ENVIRONMENT="live"
elif [ "$TEST_EXISTS" -eq "0" ]; then
    DATA_ENVIRONMENT="test"
else
    DATA_ENVIRONMENT="dev"
fi

echo "${YELLOW}Looks like the most recent data can be found in the <$DATA_ENVIRONMENT> environment.${DEFAULT}"

echo "${YELLOW}Setting Pantheon '$ENVIRONMENT' to git mode...${DEFAULT}"
terminus connection:set $PANTHEON_SITE_ID.$ENVIRONMENT git
if [[ "$?" -ne 0 ]]; then
    exit 1
fi
echo $DONE

echo "${YELLOW}Pushing to '$ENVIRONMENT' on Pantheon...${DEFAULT}"
git push pantheon master
if [[ "$?" -ne 0 ]]; then
    exit 1
fi
echo $DONE

echo "${YELLOW}Deploying release to 'test' on Pantheon...${DEFAULT}"
terminus env:deploy --sync-content -- $PANTHEON_SITE_ID.test
if [[ "$?" -ne 0 ]]; then
    exit 1
fi
echo $DONE

exit 0