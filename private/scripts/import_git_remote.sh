#!/bin/bash

source ./private/scripts/colors.sh

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
	echo "${YELLOW}Found Pantheon git remote...${DEFAULT}"
fi
echo $DONE
