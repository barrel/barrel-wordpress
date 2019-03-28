#!/bin/bash

source ./private/scripts/colors.sh

echo "${YELLOW}Hello, $USER. We're going to get you ready for dev...${DEFAULT}"

echo "${YELLOW}We expect you to have lando installed.${DEFAULT}"

LANDO_PATH=$(which lando)

if [[ "/usr/local/bin/lando" == "$LANDO_PATH" ]]; then
  echo "${YELLOW}We found lando, starting landing...${DEFAULT}"
  #lando start
else
  echo "${RED}Lando was not detected in the expected path (MacOS), please install.${DEFAULT}"
  echo "${YELLOW}Please download Lando (https://docs.devwithlando.io/) or setup your own local environment.${DEFAULT}"
  exit 1
fi

#lando pull

# assuming we cloned from gitlab
git checkout develop

# define TERMINUS_TOKEN
if [ -z ${TERMINUS_TOKEN+x} ]; then
    echo -e "${BLUE}What is your Machine Token for Terminus?${DEFAULT}"
    read TERMINUS_TOKEN
fi

# define PANTHEON_SITE_ID
if [ -z ${PANTHEON_SITE_ID+x} ]; then
    echo -e "${BLUE}What is the Pantheon site ID?${DEFAULT}"
    read PANTHEON_SITE_ID
fi

# cloned from import_git_remote.sh
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
