#!/bin/bash

####################################################################
## This script will remove all tags matching a certain regular expression
## from both local and remote repositories. After that, it will add
## an initial 0.0.1 semver tag with a specified prefix to all locations
##
####################################################################

TAG_PREFIX="v1"
INITIAL_TAG="${TAG_PREFIX}0.0.1"

printf "\nRemoving all tags begging with "${TAG_PREFIX}" from remotes..."
git push -d origin $(git tag -l "${TAG_PREFIX}*")
git push -d pantheon $(git tag -l "${TAG_PREFIX}*")

printf "\nDeleting all local tags..."
git tag | xargs -n 1 -I% git tag -d %

printf "\nFetching all remaining tags from remotes..."
git fetch --all

# printf "\nSetting initial tag ${INITIAL_TAG} and adding it to remotes..."
# git tag ${INITIAL_TAG}
# git push origin ${INITIAL_TAG}; git push pantheon ${INITIAL_TAG}

printf "\nAll done here, goodbye!\n"
exit