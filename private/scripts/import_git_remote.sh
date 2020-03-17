#!/bin/bash

####################################################################
## This script is responsible for importing a git remote specific
## to deploying on Pantheon and setting up the default machine user.
## The private key is injected using the $SSH_PRIVATE_KEY env var.
##
## Required Environment Variables:
## - $SSH_PRIVATE_KEY
## - $TERMINUS_TOKEN
## - $PANTHEON_SITE_ID
####################################################################

SCRIPT_PATH="`dirname \"$0\"`"

source $SCRIPT_PATH/colors.sh

echo "${YELLOW}Verifying git user details...${DEFAULT}"
git config --global user.email
git config --global user.name
printf "\n%s\n" "$DONE"

echo "${YELLOW}Authorizing with Pantheon...${DEFAULT}"
terminus auth:login --machine-token=$TERMINUS_TOKEN

# Pantheon git remote
REMOTE=$(terminus connection:info --format=list --fields=git_url $PANTHEON_SITE_ID.dev)

if [ $? -gt 0 ]
then
	echo "${RED}There was a problem authorizing with Pantheon.${DEFAULT}"
	exit 1
fi

echo $DONE

echo "${YELLOW}Checking to make sure the Pantheon git remote exists...${DEFAULT}"
git remote | grep pantheon
if test $? != 0;
then
	echo "${YELLOW}Didn't find the Pantheon git remote. Adding it now...${DEFAULT}"
	git remote add pantheon $REMOTE

	if [ $? -gt 0 ]
	then
		echo "${RED}There was a problem adding the Pantheon git remote.${DEFAULT}"
		exit 1
	fi
else
	echo "Found Pantheon git remote. Testing to make sure Pantheon Repository URL is correct..."
	git remote -v | grep $REMOTE
	if test $? != 0
	then
		echo "Hmm, looks like the pantheon remote url doesn't match. Updating with set-url now..."
		git remote set-url pantheon $REMOTE
	fi
fi
echo $DONE

# clean up any cached commits per ci branch
git fetch origin && git reset --hard $CI_COMMIT_SHA && git clean -f -d