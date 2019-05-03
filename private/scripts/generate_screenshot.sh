#!/bin/bash

####################################################################
## This script will generate a screenshot for a known URL and theme.
####################################################################

SCRIPT_PATH="`dirname \"$0\"`"

# Terminal colors
source $SCRIPT_PATH/colors.sh

THEME_NAME=""
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

DOMAIN="http://dev-barrel-base-theme.pantheonsite.io/"
DIMENSION="1200"
API="https://s0.wp.com/mshots/v1/$DOMAIN?w=$DIMENSION"
echo "Downloading... $API"
curl $API -o ./wp-content/themes/$THEME_NAME/screenshot.png -#