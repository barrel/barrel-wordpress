#!/bin/bash

SCRIPT_PATH="`dirname \"$0\"`"

# Terminal colors
source $SCRIPT_PATH/../colors.sh

TARGET=$(echo $CI_COMMIT_REF_NAME | cut -d'/' -f2)
ENV=$(echo ${TARGET:0:11} | tr '[:upper:]' '[:lower:]') 
ENVIRONMENT="${ENV%-}"
ENV_URL="https://$ENVIRONMENT-$PANTHEON_SITE_ID.pantheonsite.io"
echo "ENV_URL=$ENV_URL" >> deploy.env
