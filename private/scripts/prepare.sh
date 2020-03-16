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

# Get helper functions
SCRIPT_PATH="`dirname \"$0\"`"

# Terminal colors
source $SCRIPT_PATH/colors.sh

MAN_PAGE=$(
cat <<EOF

${BOLD}NAME${RESET}
     prepare.sh -- prepare a release

${BOLD}SYNOPSIS${RESET}
     prepare.sh [-v | --versiontype] [-t | --themename] [-f | --gitflow] [-s | --start] [-y | --auto]

${BOLD}DESCRIPTION${RESET}
     In the synopsis form, the prepare utility steps through build process/files, changelog updates, version bumps, gitflow operations, and deployment procedures.

     The following options are available:

     -v    The semantic version type, which is one of 'major', 'minor', or 'patch'.

     -t    The theme name if not the same as the root directory project handle.

     -f    The gitflow operation to be performed, which is typically 'release' or 'hotfix'

     -s    Whether to start gitflow or not. Default is to assume already on a release or hotfix branch. The script will always complete gitflow.

     -y    Whether to automate all tasks in the affirmative.

${BOLD}EXIT STATUS${RESET}
     The prepare utility exits 0 on success, and >0 if an error occurs. When automated, any failure will result in >0 exit code.
EOF
)

# Without any options, print man page
if [ $# -eq 0 ]; then
    echo "$MAN_PAGE"
    exit 0
fi

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
    -s=*|--start=*|-s)
    START="${i#*=}"
    shift # past argument=value
    ;;
    -y=*|--auto=*|-y)
    ASSUME="yes"
    shift # past argument=value
    ;;
    --help)
    echo "$MAN_PAGE"
    shift # past argument with no value
    exit 0
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
printf "\n%s\n\n" "Current working directory is: ${YELLOW}$CWD${DEFAULT}"

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
git checkout -B master origin/master
git checkout -B develop origin/develop

printf "\n%s\n\n" "Current version is: ${YELLOW}$CURR_VERSION${DEFAULT}"
printf "%s\n\n" "Next version is: ${YELLOW}$NEXT_VERSION${DEFAULT}"
printf "%s\n\n" "Running: npm version ${YELLOW}$SEM${DEFAULT}"

# Remove the "v"
ALT_CURR_VERSION=${CURR_VERSION:1}
ALT_NEXT_VERSION=${NEXT_VERSION:1}

# Initiate git flow start
GITFLOW_INIT=$(git flow init -d)
if [ "$START" == "yes" ]; then
    git flow $FLOW start $NEXT_VERSION
fi

# Add new line to changelog
printf "\nUsing git messages for CHANGELOG...\n"

# Comparison branch
BRANCH="master"

printf "\nTarget Branch Comparison: $BRANCH...\n"
printf "\nTarget GitFlow Operation: $FLOW...\n\n"

DATE=$(date +%Y-%m-%d)
# TODO: move to commit msg from messages tagged #changelog or something
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

if [ "$ASSUME" == "yes" ]; then

    echo "Skipping editor mode..."

else

    read -r -p "Finalize the CHANGELOG and continue? [y/N] " response
    case "$response" in
        [yY][eE][sS]|[yY])
        vim "+4 $A" ../../../CHANGELOG.md
        ;;
        *)
        printf "\nMust finalize changes. Exiting..."
        exit 1
        ;;
    esac

fi

# replace current version with new one in style.css
printf "\nReplacing $ALT_CURR_VERSION with $ALT_NEXT_VERSION version"
if [ "$PLATFORM" == "macos" ]; then
    sed -i "" -e "s/$ALT_CURR_VERSION/$ALT_NEXT_VERSION/g" style.css
else
    sed -i -e "s/$ALT_CURR_VERSION/$ALT_NEXT_VERSION/g" style.css
fi
printf "\nNext version is: ${YELLOW}"
eval $AUTO_INC_VERSION_WITH_NPM
printf "${DEFAULT}\n"

if [ "$ASSUME" == "yes" ]; then

    # STEP: Versioning Update (package.json, style.css, CHANGELOG.md)
    echo "Auto-committing version updates..."
    git add --all
    git commit -m "Update changelog and bump versions" 
    printf "\n\n$DONE\n\n"

    # STEP: Install dependencies from npm
    echo "Installing dependencies..."
    npm ci
    printf "\n\n$DONE\n\n"

    # STEP: Build styles and scripts before
    echo "Building scripts and styles..."
    npm run build
    printf "\n\n$DONE\n\n"
    echo "Committing styles and scripts..."
    git add --all
    git commit -am "Process scripts/styles"
    printf "\n\n$DONE\n\n"

    # STEP: Complete gitflow operation
    echo "Running gitflow <$FLOW> now..."
    export GIT_MERGE_AUTOEDIT=no
    git flow $FLOW finish -m "Tag $NEXT_VERSION" $NEXT_VERSION
    unset GIT_MERGE_AUTOEDIT
    printf "\n\n$DONE\n\n"

    # STEP: Push develop and master branches to origin git remote
    printf "\n${YELLOW}Synchronizing git remotes...${DEFAULT}\n"
    git checkout develop && git push origin develop
    git checkout master && git push origin master
    printf "\n\n$DONE\n\n"

    # STEP: Push [version] tags to origin git remote
    printf "\n${YELLOW}Pushing git tags...${DEFAULT}\n"
    git push origin $NEXT_VERSION
    printf "\n\n$DONE\n\n"

else

    # STEP: Versioning Update (package.json, style.css, CHANGELOG.md)
    read -r -p "Commit versioning changes? [y/N] " response
    case "$response" in
        [yY][eE][sS]|[yY]) 
        printf "\nProceeding with package ${GREEN}$NEXT_VERSION${DEFAULT}, "
        printf "last version was ${YELLOW}$CURR_VERSION${DEFAULT}"
        git commit -am "Update changelog and bump versions" 
        printf "\n\n$DONE\n\n"
    esac

    # STEP: Install dependencies from npm
    read -r -p "Install locked dependencies? [y/N] " response
    case "$response" in
        [yY][eE][sS]|[yY]) 
        printf "\nInstalling dependencies..."
        npm ci
        if [[ "$?" -ne 0 ]]; then
            echo "${RED}Failed to install build files using package-lock!${DEFAULT}"
            exit 1
        fi
        printf "\n\n$DONE\n\n"
    esac

    # STEP: Build styles and scripts before
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

    # STEP: Complete gitflow operation
    read -r -p "Finish up with gitflow? [y/N] " response
    case "$response" in
        [yY][eE][sS]|[yY]) 
        printf "\nFinishing up with gitflow command: ${BLUE}git flow $FLOW finish $NEXT_VERSION${DEFAULT}\n"
        export GIT_MERGE_AUTOEDIT=no
        git flow $FLOW finish -m "Tag $NEXT_VERSION" $NEXT_VERSION
        unset GIT_MERGE_AUTOEDIT
        printf "\n\n$DONE\n\n"
    esac

    # STEP: Push develop and master branches and [version] tags to origin git remote
    read -r -p "Push develop, master branches and version tag? [y/N] " response
    case "$response" in
        [yY][eE][sS]|[yY]) 
        printf "\n${YELLOW}Synchronizing git remotes...${DEFAULT}\n"
        git checkout develop && git push origin develop
        git checkout master && git push origin master
        printf "\n\n$DONE\n\n"

        printf "\n${YELLOW}Pushing git tags...${DEFAULT}\n"
        git push origin $NEXT_VERSION
        printf "\n\n$DONE\n\n"
    esac

    # Deploy to test environment on Pantheon
    read -r -p "Deploy to test? [y/N] " response
    case "$response" in
        [yY][eE][sS]|[yY]) 
        printf "\n${YELLOW}Deploying to Pantheon <test> environment...${DEFAULT}\n"
        # assumes pantheon deploy tags are local
        t=$(git tag --sort v:refname | grep _test_ | tail -1)
        p="pantheon_test_"
        v=${t:${#p}}
        p=$p$(($v+1))
        git tag $p && git push pantheon $p
    esac

fi

exit
