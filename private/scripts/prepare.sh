#!/bin/bash

####################################################################
## This prepare script is usually for finishing a release or hotfix.
##
## Assumptions: the hotfix/release branch has already been created.
##   The theme's node_modules directory is up to date (`npm i` has been run)
## To start the hotfix/release, run with -s=yes
##
##
## From the hotfix/v0.0.0 or release/v0.0.0 branch, this script:
## - Updates the Changelog (automatically with commit messages)
## - Opens vim to interactively confirm and complete the CHANGELOG
## - Bumps the npm version
## - Commits the above in a single commit
## - Runs the finish from gitflow command
####################################################################

THEME_NAME=""
SEM="minor"
FLOW="hotfix"
START="no"
SCRIPT_PATH="`dirname \"$0\"`"

# Terminal colors
source $SCRIPT_PATH/colors.sh

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

WP_CONTENT="wp-content"
THEMES_DIR="./$WP_CONTENT/themes"

if [ "$THEME_NAME" == "" ]; then 
    if [ -d "$WP_CONTENT" ]; then
        # assume theme is the same as project
        THEME_NAME=$(basename $(pwd))
        echo "${YELLOW}Checking to see if '$THEME_NAME' exists...${DEFAULT}"
        if ! [ -d "$THEMES_DIR/$THEME_NAME" ]; then 
            echo "${BLUE}Hmm... Something is missing. What is the Theme Name?${DEFAULT}"
            read THEME_NAME_UD
            export THEME_NAME="$THEME_NAME_UD"
        else
            echo "${YELLOW}Theme '$THEME_NAME' exists...${DEFAULT}"
        fi
    fi
fi

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
printf "\n%s" "Current working directory is: ${YELLOW}$CWD${DEFAULT}"

# theme path
THEME_PATH="./wp-content/themes/$THEME_NAME"
printf "\nChanging directory to theme path: ${YELLOW}$THEME_PATH${DEFAULT}"
cd $THEME_PATH
CWD=$(pwd)
printf "\nCurrent working directory is now: ${YELLOW}$CWD${DEFAULT}\n"

if hash jq 2>/dev/null; then
    CURR_VERSION="v"$(cat package.json | jq .version -r)
else
    echo "${RED}The jq utility was not detected, please install jq for more reliable version name detection.${DEFAULT}"
    echo "Attempting to get version numer by tag..."
    # get current version, assumes prior git tag
    CURR_VERSION=$(git tag --sort v:refname | grep "^v" | tail -1)
    echo "${YELLOW}The latest versioned git-tag $CURR_VERSION was found. Is this correct?${DEFAULT}"
fi

# get next version with npm, unless you find a clever regex that works
NEXT_VERSION=$(eval $AUTO_INC_VERSION_WITH_NPM)
git reset --hard HEAD

printf "\n%s\n\n" "Current version is: ${YELLOW}$CURR_VERSION${DEFAULT}"
printf "%s\n\n" "Next version is: ${YELLOW}$NEXT_VERSION${DEFAULT}"
printf "%s\n\n" "Running: npm version ${YELLOW}$SEM${DEFAULT}"

# Remove the "v"
ALT_CURR_VERSION=${CURR_VERSION:1}
ALT_NEXT_VERSION=${NEXT_VERSION:1}

# install dependencies from npm
read -r -p "Install locked dependencies? [y/N] " response
case "$response" in
    [yY][eE][sS]|[yY]) 
    printf "\nInstalling dependencies..."
    npm ci
    if [[ "$?" -ne 0 ]]; then
        echo "${RED}Failed to install build files!${DEFAULT}"
        exit 1
    fi
    printf "\n\n$DONE\n\n"
esac

# Initiate git flow start
GITFLOW_INIT=$(git flow init -d)
if [ "$START" == "yes" ]; then
    git flow $FLOW start $NEXT_VERSION
fi

# check to process styles and scripts before continuing
read -r -p "Do you want to build and commit scripts/styles? [y/N] " response
case "$response" in
    [yY][eE][sS]|[yY]) 
    printf "\nBuilding scripts and styles..."
    npm run build
    printf "\nCommitting styles and scripts..."
    git add --all
    git commit -am "Process scripts/styles"
    printf "\n\n$DONE\n\n"
esac

# Add new line to changelog
printf "\nUsing git messages for CHANGELOG...\n"

# Comparison branch
BRANCH="master"

printf "\nTarget Branch Comparison: $BRANCH...\n"
printf "\nTarget GitFlow Operation: $FLOW...\n\n"

DATE=$(date +%Y-%m-%d)
COMMIT_MSG_AS_CHANGE=$(git log --format="%s" --no-merges $BRANCH.. | sed -E 's/^(.*)/\- \1 \\/')

if [ "$PLATFORM" == "macos" ]; then
sed -i '' "3i\\
\\
## $ALT_NEXT_VERSION - $DATE\\
### CHANGED:\\
$COMMIT_MSG_AS_CHANGE
" ../../../CHANGELOG.md
else
sed -i "3i\\
\\
## $ALT_NEXT_VERSION - $DATE\\
### CHANGED:\\
$COMMIT_MSG_AS_CHANGE " ../../../CHANGELOG.md
fi

## Finalize changelog updates
read -r -p "Finalize the CHANGELOG and continue? [y/N] " response
case "$response" in
    [yY][eE][sS]|[yY]) 
    vim "+4 $A" ../../../CHANGELOG.md
esac

# replace current version with new one in style.css
printf "\nReplacing $ALT_CURR_VERSION with $ALT_NEXT_VERSION version"
sed -i "" -e "s/$ALT_CURR_VERSION/$ALT_NEXT_VERSION/g" style.css

printf "\nNext version is: ${YELLOW}"
eval $AUTO_INC_VERSION_WITH_NPM
printf "${DEFAULT}\n"

read -r -p "Commit versioning changes? [y/N] " response
case "$response" in
    [yY][eE][sS]|[yY]) 
    printf "\nProceeding with package ${GREEN}$NEXT_VERSION${DEFAULT}, "
    printf "last version was ${YELLOW}$CURR_VERSION${DEFAULT}"
	git commit -am "Update changelog and bump versions" 
    printf "\n\n$DONE\n\n"
esac

read -r -p "Finish up with gitflow? [y/N] " response
case "$response" in
    [yY][eE][sS]|[yY]) 
    printf "\nFinishing up with gitflow command: ${BLUE}git flow $FLOW finish $NEXT_VERSION${DEFAULT}\n"
    export GIT_MERGE_AUTOEDIT=no
    git flow $FLOW finish -m "Tag $NEXT_VERSION" $NEXT_VERSION
    unset GIT_MERGE_AUTOEDIT
    printf "\n\n$DONE\n\n"
esac
exit
