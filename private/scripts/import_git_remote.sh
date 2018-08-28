#!/bin/bash

echo "Verifying git user details ..."
git config --global user.email
git config --global user.name
printf "\ndone\n"

echo "Authorizing with Pantheon ..."
terminus auth:login --machine-token=$TERMINUS_TOKEN

# Pantheon git remote
REMOTE=$(terminus connection:info --format=list --fields=git_url $PANTHEON_SITE_ID.dev)

if [ $? -gt 0 ]
then
	echo "There was a problem authorizing with Pantheon."
	exit 1
fi

printf "\ndone\n"

echo "Checking to make sure the Pantheon git remote exists ..."
git remote | grep pantheon
if test $? != 0;
then
	echo "Didn't find the Pantheon git remote. Adding it now ..."
	git remote add pantheon $REMOTE

	if [ $? -gt 0 ]
	then
		echo "There was a problem adding the Pantheon git remote."
		exit 1
	fi
else
	echo "Found Pantheon git remote ..."
fi


printf "\ndone\n"
