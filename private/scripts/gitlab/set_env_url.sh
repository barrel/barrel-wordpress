#!/bin/bash

SCRIPT_PATH="`dirname \"$0\"`"

# Terminal colors
source $SCRIPT_PATH/../colors.sh

# Using the API to bypass the gitlab-ci limitation.
# Limitation prevents scripting to set dynamic env urls. 
# @see https://docs.gitlab.com/ce/api/enviroments.html#environments

TARGET=$(echo $CI_COMMIT_REF_NAME | cut -d'/' -f2)
ENVIRONMENT=$(echo ${TARGET:0:11} | tr '[:upper:]' '[:lower:]') 
ENVURL="https://$ENVIRONMENT-$PANTHEON_SITE_ID.pantheonsite.io"
GITLAB_API_URL="https://gitlab.com/api/v4/projects"

ENVIRONMENT_REQ_HEADER="PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN"
ENV_ID_REQ_URL="$GITLAB_API_URL/$CI_PROJECT_ID/environments"
JSON=$(curl -s --header "$ENVIRONMENT_REQ_HEADER" "$ENV_ID_REQ_URL")
CI_ENVIRONMENT_ID=$(echo $JSON | jq -r '.[]  | select(.name == "$CI_ENVIRONMENT_NAME") | .id')
ENV_SET_URL="$GITLAB_API_URL/$CI_PROJECT_ID/environments/$CI_ENVIRONMENT_ID"

echo "${YELLOW}Setting the environment URL dynamically...${DEFAULT}"
RESULT_JSON=$(curl -s --request PUT --data "external_url=$ENVURL" --header "$ENVIRONMENT_REQ_HEADER" "$ENV_SET_URL")
echo $RESULT_JSON | jq '.'
if [[ "$?" -ne 0 ]]; then
    echo "${RED}Setting the environment URL failed!${DEFAULT}"
    exit 1
fi
echo $DONE
