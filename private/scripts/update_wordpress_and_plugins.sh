#!/bin/bash

####################################################################
## This script is used to update WordPress upstream, and plugins.
####################################################################

SCRIPT_PATH="`dirname \"$0\"`"

# Terminal colors
source $SCRIPT_PATH/colors.sh

read -r -p "Fetch and update develop, master branches? [y/N] " response
case "$response" in
    [yY][eE][sS]|[yY]) 
    printf "\n${YELLOW}Fetching...${DEFAULT}\n"

    # git up-to-date
    git stash
    git fetch --all
    git checkout master
    git pull origin master
    git checkout develop
    git pull origin develop

    printf "\n\n$DONE\n\n"
esac

read -r -p "Setup gitflow? [y/N] " response
case "$response" in
    [yY][eE][sS]|[yY]) 
    printf "\n${YELLOW}Setting up gitflow feature branch...${DEFAULT}\n"

    # as hotfix or as feature branch (add upstream or dev-only)
    GITFLOW_BRANCH="feature"

    # init git flow
    GITFLOW_STATUS=$(git flow config)
    if [ "$?" -gt 0 ]; then
      git flow init -d
    fi

    # start gitflow, assumes git repo where origin is truth
    git flow $GITFLOW_BRANCH start updates

    printf "\n\n$DONE\n\n"
esac

read -r -p "Update WordPress upstream? [y/N] " response
case "$response" in
    [yY][eE][sS]|[yY]) 
    printf "\n${YELLOW}Updating WordPress from upstream...${DEFAULT}\n"

    # update wordpress from upstream, assuming no conflicts
    git pull git://github.com/pantheon-systems/WordPress.git master

    printf "\n\n$DONE\n\n"
esac

# @TODO - this could be broken by conflicts, which
# would need to be resolved and then committed.
# like `git mergetool` and `git merge --continue`

read -r -p "Update plugins and themes using Lando? [y/N] " response
case "$response" in
    [yY][eE][sS]|[yY]) 

    # start lando or look at a pantheon env
    lando start

    printf "\n${YELLOW}Updating plugins...${DEFAULT}\n"

    # get plugins with updates
    PLUGIN_UPDATES=($(lando wp plugin list --update=available --fields=name --format=csv | tail -n +2))
    for i in "${PLUGIN_UPDATES[@]}"; do
        PLUGIN=$(echo $i| tr -dc '[:alnum:]\-' | tr '[:upper:]' '[:lower:]')
        printf "\n${YELLOW}%s${DEFAULT}\n" "Updating $PLUGIN"

        lando wp plugin update $PLUGIN
        git add ./wp-content/plugins/$PLUGIN
        GIT_MESSAGE="Update $PLUGIN plugin"
        git commit -m "$GIT_MESSAGE"
        sleep 5
    done
    wait

    printf "\n\n$DONE\n\n"

    printf "\n${YELLOW}Updating themes...${DEFAULT}\n"

    # get themes with updates
    THEME_UPDATES=($(lando wp theme list --update=available --fields=name --format=csv | tail -n +2))
    for i in "${THEME_UPDATES[@]}"; do
        THEME=$(echo $i| tr -dc '[:alnum:]\-' | tr '[:upper:]' '[:lower:]')
        printf "\n${YELLOW}%s${DEFAULT}\n" "Updating $THEME"

        lando wp theme update $THEME
        git add ./wp-content/themes/$THEME
        GIT_MESSAGE="Update $THEME theme"
        git commit -m "$GIT_MESSAGE"
        sleep 5
    done
    wait

    printf "\n\n$DONE\n\n"
esac

if [ "$GITFLOW_BRANCH" == "feature" ]; then 
    printf "\n${YELLOW}Finishing up gitflow feature branch...${DEFAULT}\n"
    git flow $GITFLOW_BRANCH finish updates
    printf "\n\n$DONE\n\n"
fi

# finish with prepare script and test (for regressions, visual or functional)
printf "\n${GREEN}%s${DEFAULT}\n" "Update procedure complete!"

printf "\n${YELLOW}%s${DEFAULT}\n" "Run the prepare script and test!"
printf "\n\n$DONE\n\n"
exit 0