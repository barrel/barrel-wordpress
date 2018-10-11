#!/bin/bash

####################################################################
## This script will deploy to a pantheon site's multidev environment 
## and associated branch. Assumes CI variables populated and the 
## before_script properly adds a 'pantheon' remote to the git repo.
##
## Variables:
## - $CI_COMMIT_REF_NAME, defined by GitLab CI or by user
## - $PANTHEON_SITE_ID, defined in environment variables or by user
## - $THEME_NAME, defined by environment variables
## - $ENV, defined automatically
####################################################################

# Terminal colors
DEFAULT=$(tput setaf 7 -T xterm)
RED=$(tput setaf 1 -T xterm)
GREEN=$(tput setaf 2 -T xterm)
YELLOW=$(tput setaf 3 -T xterm)
BLUE=$(tput setaf 4 -T xterm)

if [ -z ${CI_COMMIT_REF_NAME+x} ]; then
    echo "${YELLOW}Hmm... Looks like this is not being run in GitLab CI, what's the original branch name?${DEFAULT}"
    read CI_COMMIT_REF_NAME
fi
if [ -z ${PANTHEON_SITE_ID+x} ]; then
    echo "${YELLOW}Hmm... Something is missing. What is the Pantheon Site Name?${DEFAULT}"
    read PANTHEON_SITE_ID
fi
DONE="\n${GREEN}done${DEFAULT}\n"
TARGET=$(echo $CI_COMMIT_REF_NAME | cut -d'/' -f2)
ENV=$(echo ${TARGET:0:11} | tr '[:upper:]' '[:lower:]') 
MATCH=$(terminus multidev:list --format=list --fields=id $PANTHEON_SITE_ID | grep $ENV)
LIVE_TAG=$(git ls-remote --tags pantheon | grep "pantheon_live_")
LIVE_EXISTS="$?"
TEST_TAG=$(git ls-remote --tags pantheon | grep "pantheon_test_")
TEST_EXISTS="$?"

echo "${YELLOW}Checking which database should be used...${DEFAULT}"

if [[ "$LIVE_EXISTS" -eq 0 ]]; then
    DATA_ENVIRONMENT="live"
elif [[ "$TEST_EXISTS" -eq 0 ]]; then
    DATA_ENVIRONMENT="test"
else
    DATA_ENVIRONMENT="dev"
fi

echo "${YELLOW}Looks like the most recent data can be found in the <$DATA_ENVIRONMENT> environment.${DEFAULT}"
echo "${YELLOW}Checking if ENV '$ENV' exists..."
if [[ "$MATCH" -ne 0 ]]
then 
    echo "${YELLOW}Multidev not found. Creating $ENV from the <$DATA_ENVIRONMENT> environment...${DEFAULT}"
    terminus multidev:create $PANTHEON_SITE_ID.$DATA_ENVIRONMENT $ENV
    terminus remote:wp $PANTHEON_SITE_ID.$ENV -- theme activate $THEME_NAME
    echo -n $DONE
else
    echo "${YELLOW}Looks like the $ENV environment exists.. cloning data from <$DATA_ENVIRONMENT>...${DEFAULT}"
    terminus env:clone-content $PANTHEON_SITE_ID.$DATA_ENVIRONMENT $ENV
    echo -n $DONE
fi

echo "${YELLOW}Setting Pantheon '$ENV' to git mode...${DEFAULT}"
terminus connection:set $PANTHEON_SITE_ID.$ENV git
echo -n $DONE

echo "${YELLOW}Pushing to '$ENV' on Pantheon...${DEFAULT}"
git push -f pantheon HEAD:$ENV
echo -n $DONE

echo "${YELLOW}Installing theme build tools...${DEFAULT}"
cd ./wp-content/themes/$THEME_NAME && npm i
echo -n $DONE

echo "${YELLOW}Building theme...${DEFAULT}"
npm run build
echo -n $DONE

# Just checking if there are changes, surely there's more to consider
CHANGED=$(git status --porcelain)
if [ -n "$CHANGED" ]
then
    echo "${YELLOW}Changes detected. Committing...${DEFAULT}"
    git commit -am "Process scripts and styles"
    echo "${YELLOW}Pushing...${DEFAULT}"
    git push pantheon HEAD:$ENV --verbose
    echo -n $DONE
else
	echo "${YELLOW}No changes...${DEFAULT}"
    echo -n $DONE
fi
