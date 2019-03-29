#!/bin/bash

THEME_NAME="barrel-base"
DOMAIN="http://barrel-wordpress.lndo.site/"
DIMENSION="1200"
API="https://s0.wp.com/mshots/v1/$DOMAIN?w=$DIMENSION"
echo "Downloading $API..."
curl $API -o ./wp-content/themes/$THEME_NAME/screenshot.png -#