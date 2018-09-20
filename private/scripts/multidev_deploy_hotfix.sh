#!/bin/bash

ENV="hotfix"
MATCH=$(terminus multidev:list --format=list --fields=id $PANTHEON_SITE_ID | grep $ENV)
LIVE_EXISTS=$(git ls-remote --tags pantheon | grep "pantheon_live_")
TEST_EXISTS=$(git ls-remote --tags pantheon | grep "pantheon_test_")

echo "Checking which database should be used..."

if ! [ "x$LIVE_EXISTS" = "x" ]; then
    DATA_ENVIRONMENT="live"
elif ! [ "x$TEST_EXISTS" = "x" ] 
then
    DATA_ENVIRONMENT="test"
else
    DATA_ENVIRONMENT="dev"
fi

echo "Looks like the most recent data can be found in the <$DATA_ENVIRONMENT> environment ..."

echo "Checking if ENV '$ENV' exists ..."
if [ "$MATCH" != "$ENV" ]
then 
    echo "Multidev not found. Creating $ENV from the <$DATA_ENVIRONMENT?> environment ..."
    terminus multidev:create $PANTHEON_SITE_ID.$DATA_ENVIRONMENT $ENV
    terminus remote:wp $PANTHEON_SITE_ID.$ENV -- theme activate $THEME_NAME
	printf "\ndone\n"
else
    echo "Looks like the $ENV environment exists.. cloning data from <$DATA_ENVIRONMENT> ..."
    terminus env:clone-content $PANTHEON_SITE_ID.$DATA_ENVIRONMENT $ENV
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
