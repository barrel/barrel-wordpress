#!/bin/bash

SCRIPT_PATH="`dirname \"$0\"`"

# Terminal colors
source $SCRIPT_PATH/../colors.sh

# Using the API to bypass the gitlab-ci limitation.
# Limitation prevents scripting to set dynamic env urls. 
# @see https://docs.gitlab.com/ce/api/enviroments.html#environments

TARGET=$(echo $CI_COMMIT_REF_NAME | cut -d'/' -f2)
ENV=$(echo ${TARGET:0:11} | tr '[:upper:]' '[:lower:]') 
ENVIRONMENT="${ENV%-}"
ENVURL="https://$ENVIRONMENT-$PANTHEON_SITE_ID.pantheonsite.io"
GITLAB_API_URL="https://gitlab.com/api/v4/projects"
ENVIRONMENT_REQ_HEADER="PRIVATE-TOKEN: $GITLAB_PRIVATE_TOKEN"
ENV_ID_REQ_URL="$GITLAB_API_URL/$CI_PROJECT_ID/environments"

echo "${YELLOW}Retrieving list of GitLab CI Environments with '$CI_PROJECT_ID' CI Project ID...${DEFAULT}"
JSON=$(curl -s --header "$ENVIRONMENT_REQ_HEADER" "$ENV_ID_REQ_URL?search=$CI_ENVIRONMENT_NAME")
echo $DONE

echo "${YELLOW}Looking for CI Environment ID matching '$CI_ENVIRONMENT_NAME' environment name in list of environments...${DEFAULT}"
CI_ENVIRONMENT_ID=$(echo $JSON | jq -r '.[]  | select(.name == "'$CI_ENVIRONMENT_NAME'") | .id')
if [ "$CI_ENVIRONMENT_ID" == "" ]; then
    echo "${RED}The CI Environment ID could not be found, but the deployment may have succeeded!${DEFAULT}"
    exit
fi
echo $DONE

echo "${YELLOW}Setting the environment URL dynamically...${DEFAULT}"
ENV_SET_URL_RETURN=$(curl -s --request PUT --data "external_url=$ENVURL" --header "$ENVIRONMENT_REQ_HEADER" "$ENV_ID_REQ_URL/$CI_ENVIRONMENT_ID")
ENV_SET_URL_CURL_STATUS="$?"
ENV_SET_URL_STATUS=$(echo $ENV_SET_URL_RETURN | jq -r '.error')

if [ "$ENV_SET_URL_CURL_STATUS" -ne 0 ] || [ "$ENV_SET_URL_STATUS" != "null" ]; then
    echo "${RED}Setting the Environment URL failed, but the deployment may have succeeded!${DEFAULT}"
    exit
fi
echo "${BOLD}ENV URL:${RESET} $ENVURL"
echo $DONE
