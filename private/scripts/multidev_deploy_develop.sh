#!/bin/bash

# Note the $CI_COMMIT_REF_NAME is the source branch of the operation
ENV="develop"
MATCH=$(terminus multidev:list --format=list --fields=id $PANTHEON_SITE_ID | grep $ENV)

echo "Checking if ENV '$ENV' exists ..."
if [ "$MATCH" != "$ENV" ]
then 
    echo "Multidev not found. Creating $ENV from dev ..."
    terminus multidev:create $PANTHEON_SITE_ID.dev $ENV
    terminus remote:wp $PANTHEON_SITE_ID.$ENV -- theme activate $THEME_NAME
	printf "\ndone\n"
fi

echo "Setting Pantheon '$ENV' to git mode ..."
terminus connection:set $PANTHEON_SITE_ID.$ENV git
printf "\ndone\n"

echo "Pushing to '$ENV' on Pantheon ..."
git push -f pantheon HEAD:$ENV
printf "\ndone\n"

echo "Installing theme build tools ..."
cd ./wp-content/themes/$THEME_NAME && npm i
printf "\ndone\n"

echo "Building theme ..."
npm run build
printf "\ndone\n"

# Just checking if there are changes, surely there's more to consider
CHANGED=$(git status --porcelain)
if [ -n "$CHANGED" ]
then
    echo "Changes detected. Committing ..."
    git commit -am "Process scripts and styles"
    echo "Pushing ..."
    git push pantheon HEAD:$ENV --verbose
	printf "\ndone\n"
else
	echo "No changes ..."
	printf "\ndone\n"
fi
