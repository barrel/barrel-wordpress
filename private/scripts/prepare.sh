#!/bin/bash

####################################################################
## This prepare script is usually for finishing a release or hotfix.
##
## Assumptions: the hotfix/release branch has already been created.
## To start the hotfix/release, run with -s=yes
##
## From the hotfix/v0.0.0 or release/v0.0.0 branch, this script:
## - Updates the Changelog (automatically with commit messages)
## - Opens vim to interactively confirm and complete the CHANGELOG
## - Bumps the npm version
## - Commits the above in a single commit
## - Runs the finish from gitflow command
####################################################################

THEME_NAME="barrel-base"
SEM="patch"

# Terminal colors
DEFAULT=$(tput setaf 7)
RED=$(tput setaf 1)
GREEN=$(tput setaf 2)
YELLOW=$(tput setaf 3)
BLUE=$(tput setaf 4)

# handle arguments
for i in "$@"; do
case $i in
    -v=*|--versiontype=*)
    SEM="${i#*=}"
    shift # past argument=value
    ;;
    -t=*|--themename=*)
    THEME_NAME="${i#*=}"
    shift # past argument=value
    ;;
    --help)
    echo "Utility Usage:"
    echo "--"
    echo "prepare.sh -v=MAJOR|MINOR|PATCH -t=THEME_NAME"
    shift # past argument with no value
    ;;
    *)
	echo "Unknown option: ${i#*=}"
          # unknown option
    ;;
esac
done

# this is to always be run from the root of the project
CWD=$(pwd)
echo "Current working directory is: $CWD"

# get current version
CURRENT_VERSION=$(git tag --sort v:refname | grep "^v" | tail -1)
printf "Current version is: ${BLUE}$CURRENT_VERSION${DEFAULT}"

# theme path
THEME_PATH="./wp-content/themes/$THEME_NAME"
printf "\nTheme path is: ${YELLOW}$THEME_PATH${DEFAULT}"
printf "\nChanging directory to ${BLUE}$THEME_PATH${DEFAULT}"
cd $THEME_PATH
CWD=$(pwd)
printf "\nCurrent working directory is now: ${BLUE}$CWD${DEFAULT}\n"
git stash
printf "Running: npm version ${YELLOW}$SEM${DEFAULT}\n"

NEXT_VERSION=$(npm version $SEM)
ALT_VERSION=${CURRENT_VERSION:1}

git stash

printf "\nEditing the CHANGELOG.md -- ${YELLOW}need to add new block manually.${DEFAULT}"

# path to changelog assumes always in root
vim ../../../CHANGELOG.md

# replace below with a sed find/replace of current version with new one
printf "\nReplacing $ALT_VERSION with ${NEXT_VERSION:1} version in style.css"
sed -i "" -e "s/$ALT_VERSION/${NEXT_VERSION:1}/g" style.css
printf "\nNext version is: ${YELLOW}"
npm version $SEM
printf "${DEFAULT}\n"
read -r -p "Finish and commit? [y/N] " response
case "$response" in
    [yY][eE][sS]|[yY]) 
    printf "\nProceeding with package ${GREEN}$NEXT_VERSION${DEFAULT}, "
    printf "last version was ${YELLOW}$CURRENT_VERSION${DEFAULT}"
    printf "\n\n${GREEN}done.${DEFAULT}\n\n${RED}Goodbye.${DEFAULT}\n\n"
	git commit -am "Update changelog and bump versions" 
    ;;
    *)
    printf "\n${RED}Exiting. Goodbye.${DEFAULT}\n\n"
    git reset --hard HEAD
    exit 0
    ;;
esac


exit