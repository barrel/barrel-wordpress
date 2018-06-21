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
SEM="minor"
FLOW="hotfix"
START="no"

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
    -f=*|--gitflow=*)
    FLOW="${i#*=}"
    shift # past argument=value
    ;;
    -s=*|--start=*)
    START="${i#*=}"
    shift # past argument=value
    ;;
    --help)
    echo "Utility Usage:"
    echo "--"
    echo "prepare.sh -v=major|minor|patch -f=hotfix|release -s=yes|no"
    shift # past argument with no value
    ;;
    *)
	echo "Unknown option: ${i#*=}"
          # unknown option
    ;;
esac
done

# Platform detection
PLATFORM='unknown'
DETECTED=$(uname | tr '[:upper:]' '[:lower:]')
if [[ "$DETECTED" == 'linux' ]]; then
   PLATFORM='linux'
elif [[ "$DETECTED" == 'darwin' ]]; then
   PLATFORM='macos'
fi

AUTO_INC_VERSION_WITH_NPM="npm version $SEM --no-git-tag"

# this is to always be run from the root of the project
CWD=$(pwd)
printf "\n%s\n\n" "Current working directory is: ${YELLOW}$CWD${DEFAULT}"

# get current version, assumes prior git tag
CURR_VERSION=$(git tag --sort v:refname | grep "^v" | tail -1)

# theme path
THEME_PATH="./wp-content/themes/$THEME_NAME"
printf "\nTheme path is: ${YELLOW}$THEME_PATH${DEFAULT}"
printf "\nChanging directory to ${BLUE}$THEME_PATH${DEFAULT}"
cd $THEME_PATH
CWD=$(pwd)
printf "\nCurrent working directory is now: ${BLUE}$CWD${DEFAULT}\n"

# get next version with npm, unless you find a clever regex that works
NEXT_VERSION=$(eval $AUTO_INC_VERSION_WITH_NPM)
git reset --hard HEAD

printf "\n%s\n\n" "Current version is: ${YELLOW}$CURR_VERSION${DEFAULT}"
printf "%s\n\n" "Next version is: ${YELLOW}$NEXT_VERSION${DEFAULT}"
printf "%s\n\n" "Running: npm version ${YELLOW}$SEM${DEFAULT}"

# Remove the "v"
ALT_CURR_VERSION=${CURR_VERSION:1}
ALT_NEXT_VERSION=${NEXT_VERSION:1}

# Initiate git flow start
if [ "$START" == "yes" ]; then
    git flow $FLOW start $NEXT_VERSION
fi

printf "\nEditing the CHANGELOG.md -- ${YELLOW}need to add new block manually.${DEFAULT}"

# path to changelog assumes always in root
vim ../../../CHANGELOG.md

# replace current version with new one in style.css
printf "\nReplacing $ALT_CURR_VERSION with $ALT_NEXT_VERSION version"
sed -i "" -e "s/$ALT_VERSION/${NEXT_VERSION:1}/g" style.css

printf "\nNext version is: ${YELLOW}"
eval $AUTO_INC_VERSION_WITH_NPM
printf "${DEFAULT}\n"

read -r -p "Finish and commit? [y/N] " response
case "$response" in
    [yY][eE][sS]|[yY]) 
    printf "\nProceeding with package ${GREEN}$NEXT_VERSION${DEFAULT}, "
    printf "last version was ${YELLOW}$CURR_VERSION${DEFAULT}"
    printf "\n\n${GREEN}done.${DEFAULT}\n\n"
	git commit -am "Update changelog and bump versions" 
    printf "\nFinish up with gitflow command ${BLUE}git flow $FLOW finish $NEXT_VERSION${DEFAULT}"
    git flow $FLOW finish $NEXT_VERSION
    exit 0
    ;;
    *)
    printf "\n${RED}Exiting. Goodbye.${DEFAULT}\n\n"
    git reset --hard HEAD
    exit 1
    ;;
esac

exit