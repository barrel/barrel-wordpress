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
    echo -e "${BLUE}Hmm... Looks like this is not being run in GitLab CI. What's the original branch name?${DEFAULT}"
    read CI_COMMIT_REF_NAME
fi

if [ -z ${PANTHEON_SITE_ID+x} ]; then
    echo -e "${BLUE}Hmm... Something is missing. What is the Pantheon Site Name?${DEFAULT}"
    read PANTHEON_SITE_ID
fi

if [ -z ${THEME_NAME+x} ]; then
    echo -e "${BLUE}Hmm... Something is missing. What is the Theme Name?${DEFAULT}"
    read THEME_NAME
fi

DONE="${GREEN}...done${DEFAULT}\n"
TARGET=$(echo $CI_COMMIT_REF_NAME | cut -d'/' -f2)
ENV=$(echo ${TARGET:0:11} | tr '[:upper:]' '[:lower:]') 

echo -e "${YELLOW}Checking which database should be used...${DEFAULT}"

MATCH=$(terminus multidev:list --format=list --fields=id $PANTHEON_SITE_ID | grep $ENV)
MATCH_EXISTS="$?"

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

echo -e "Looks like the most recent data can be found in the <$DATA_ENVIRONMENT> environment.\n"
echo -e "${YELLOW}Checking if ENV '$ENV' exists...${DEFAULT}"

if [[ "$MATCH_EXISTS" -ne 0 ]]
then 
    echo -e "Multidev not found.\n"
    echo -e "${YELLOW}Creating $ENV from the <$DATA_ENVIRONMENT> environment...${DEFAULT}"
    terminus multidev:create $PANTHEON_SITE_ID.$DATA_ENVIRONMENT $ENV
    if [[ "$?" -ne 0 ]]; then
        exit 1
    fi
    echo -ne $DONE

    echo -e "${YELLOW}Activating the theme...${DEFAULT}"
    terminus remote:wp $PANTHEON_SITE_ID.$ENV -- theme activate $THEME_NAME
    if [[ "$?" -ne 0 ]]; then
        exit 1
    fi
    echo -ne $DONE
else
    echo -e "Looks like the $ENV environment exists.\n"
fi

echo -e "${YELLOW}Setting Pantheon '$ENV' to git mode...${DEFAULT}"
terminus connection:set $PANTHEON_SITE_ID.$ENV git
if [[ "$?" -ne 0 ]]; then
    exit 1
fi
echo -ne $DONE

echo -e "${YELLOW}Pushing to '$ENV' on Pantheon...${DEFAULT}"
git push -f pantheon HEAD:$ENV
if [[ "$?" -ne 0 ]]; then
    exit 1
fi
echo -ne $DONE

echo -e "${YELLOW}Installing theme build tools...${DEFAULT}"
cd ./wp-content/themes/$THEME_NAME && npm i
if [[ "$?" -ne 0 ]]; then
    exit 1
fi
echo -ne $DONE

echo -e "${YELLOW}Building theme...${DEFAULT}"
npm run build
if [[ "$?" -ne 0 ]]; then
    exit 1
fi
echo -ne $DONE

# Just checking if there are changes, surely there's more to consider
CHANGED=$(git status --porcelain)
if [ -n "$CHANGED" ]
then
    echo -e "${YELLOW}Changes detected. Committing...${DEFAULT}"
    git commit -am "Process scripts and styles"
    if [[ "$?" -ne 0 ]]; then
        exit 1
    fi

    echo -e "${YELLOW}Pushing...${DEFAULT}"
    git push pantheon HEAD:$ENV --verbose
    if [[ "$?" -ne 0 ]]; then
        exit 1
    fi
    echo -ne $DONE
else
	echo "${YELLOW}No changes...${DEFAULT}"
    echo -ne $DONE
fi
