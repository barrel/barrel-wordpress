#!/bin/bash

# as hotfix or as feature branch (add upstream or dev-only)
GITFLOW_BRANCH="feature"

# init git flow
GITFLOW_STATUS=$(git flow config)
if [ "$?" -gt 0 ]; then
  git flow init -d
fi

# start gitflow, assumes git repo where origin is truth
git stash
git fetch --all
git checkout develop
git pull origin develop
git flow $GITFLOW_BRANCH start updates

# start lando or look at a pantheon env
#lando start

# get plugins with updates
PLUGIN_UPDATES=($(lando wp plugin list --update=available --fields=name --format=csv | tail -n +2))
for i in "${PLUGIN_UPDATES[@]}"
do
:
  printf "\n%s\n" "Updating $i ..."
  lando wp plugin update $i
  git add --all
  git commit -m "Update $i plugin"
done
  

# push up to origin
#git flow $GITFLOW_BRANCH publish
#git push origin $GITFLOW_BRANCH/updates

