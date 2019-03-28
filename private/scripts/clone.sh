#!/bin/bash

BASE_THEME="barrel-base"
THEME_NAME=""
WP_CONTENT="wp-content"
THEMES_DIR="./$WP_CONTENT/themes"

# handle arguments
for i in "$@"; do
case $i in
    -t=*|--themename=*)
    THEME_NAME="${i#*=}"
    shift # past argument=value
    ;;
    --help)
    echo "Utility Usage:"
    echo "--"
    echo "clone.sh -t=THEME_NAME"
    shift # past argument with no value
    ;;
    *)
	echo "Unknown option: ${i#*=}"
          # unknown option
    ;;
esac
done

if [ "$THEME_NAME" == "" ]; then 
    if [ -d "$WP_CONTENT" ]; then
        # assume theme is the same as project
        THEME_NAME=$(basename $(pwd))
    fi
    if [ "$THEME_NAME" == "" ]; then
        echo ""
        echo "Please define the variable for THEME_NAME within your environment"
        echo "or supply the theme name as an argument: -t=theme-name"
        exit
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

# this is to always be run from the root of the project
CWD=$(pwd)
echo "Current working directory is: $CWD"

echo "Copying theme files..."
cp -R $THEMES_DIR/$BASE_THEME $THEMES_DIR/$THEME_NAME

# remove lando config 
echo "Removing lando config, please re-run 'lando init --source cwd --recipe pantheon' when connecting with Pantheon..."
rm .lando.yml

# replace barrel-base
if [ "$PLATFORM" == "macos" ]; then
    sed -i "" "s/$BASE_THEME/$THEME_NAME/g" ./private/scripts/create_module.sh
    sed -i "" "s/$BASE_THEME/$THEME_NAME/g" ./private/scripts/prepare.sh
    sed -i "" "s/$BASE_THEME/$THEME_NAME/g" $THEMES_DIR/$THEME_NAME/composer.json
    sed -i "" "s/$BASE_THEME/$THEME_NAME/g" $THEMES_DIR/$THEME_NAME/lib/class-theme-init.php
    sed -i "" "s/$BASE_THEME/$THEME_NAME/g" $THEMES_DIR/$THEME_NAME/lib/helpers/wordpress.php
    sed -i "" "s/$BASE_THEME/$THEME_NAME/g" $THEMES_DIR/$THEME_NAME/modules/search-form/search-form.php
    sed -i "" "s/$BASE_THEME/$THEME_NAME/g" $THEMES_DIR/$THEME_NAME/package.json
    sed -i "" "s/$BASE_THEME/$THEME_NAME/g" $THEMES_DIR/$THEME_NAME/package-lock.json
    sed -i "" "s/$BASE_THEME/$THEME_NAME/g" $THEMES_DIR/$THEME_NAME/style.css
    sed -i "" "s/$BASE_THEME-theme/$THEME_NAME/g" $THEMES_DIR/$THEME_NAME/config.yml
else
    sed -i "s/$BASE_THEME/$THEME_NAME/g" ./private/scripts/create_module.sh
    sed -i "s/$BASE_THEME/$THEME_NAME/g" ./private/scripts/prepare.sh
    sed -i "s/$BASE_THEME/$THEME_NAME/g" $THEMES_DIR/$THEME_NAME/composer.json
    sed -i "s/$BASE_THEME/$THEME_NAME/g" $THEMES_DIR/$THEME_NAME/lib/class-theme-init.php
    sed -i "s/$BASE_THEME/$THEME_NAME/g" $THEMES_DIR/$THEME_NAME/lib/helpers/wordpress.php
    sed -i "s/$BASE_THEME/$THEME_NAME/g" $THEMES_DIR/$THEME_NAME/modules/search-form/search-form.php
    sed -i "s/$BASE_THEME/$THEME_NAME/g" $THEMES_DIR/$THEME_NAME/package.json
    sed -i "s/$BASE_THEME/$THEME_NAME/g" $THEMES_DIR/$THEME_NAME/package-lock.json
    sed -i "s/$BASE_THEME/$THEME_NAME/g" $THEMES_DIR/$THEME_NAME/style.css
    sed -i "s/$BASE_THEME-theme/$THEME_NAME/g" $THEMES_DIR/$THEME_NAME/config.yml
fi

# replace version number of latest base theme in package.json, package-lock.json, and style.css
BASE_THEME_VERSION="2.1.0"
NEW_VERSION="0.0.1"
if [ "$PLATFORM" == "macos" ]; then
    sed -i "" "s/$BASE_THEME_VERSION/$NEW_VERSION/g" $THEMES_DIR/$THEME_NAME/package.json
    sed -i "" "s/$BASE_THEME_VERSION/$NEW_VERSION/g" $THEMES_DIR/$THEME_NAME/package-lock.json
    sed -i "" "s/$BASE_THEME_VERSION/$NEW_VERSION/g" $THEMES_DIR/$THEME_NAME/style.css
else
    sed -i "s/$BASE_THEME_VERSION/$NEW_VERSION/g" $THEMES_DIR/$THEME_NAME/package.json
    sed -i "s/$BASE_THEME_VERSION/$NEW_VERSION/g" $THEMES_DIR/$THEME_NAME/package-lock.json
    sed -i "s/$BASE_THEME_VERSION/$NEW_VERSION/g" $THEMES_DIR/$THEME_NAME/style.css
fi

# re-init lando
lando init --source cwd --recipe pantheon
