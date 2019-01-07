#!/bin/bash

#####################################################################
## This script automatically creates a merge request, setting target
## branch based on GitFlow, automatically tags it as WIP and assigns
## to the user owner of the Private Access Token.
##
## The following environment variable needs to be set:
## - GITLAB_PRIVATE_TOKEN: Go to your profile settings to create this
##   private token.
##
## Adapted from: https://about.gitlab.com/2017/09/05/how-to-automatically-create-a-new-mr-on-gitlab-with-gitlab-ci/
#####################################################################

# Extract the host where the server is running, and add the URL to the APIs
[[ $CI_PROJECT_URL =~ ^https?://[^/]+ ]] && HOST="${BASH_REMATCH[0]}/api/v4/projects/"

# Determine target branch based on gitflow
if [[ $CI_COMMIT_REF_NAME =~ ^(bugfix|feature)/ ]]; then
    TARGET_BRANCH='develop'
elif [[ $CI_COMMIT_REF_NAME =~ ^(support|hotfix)/ ]]; then
    TARGET_BRANCH='master'
fi

# The description of our new MR, we want to remove the branch after the MR has
# been closed
BODY="{
    \"id\": ${CI_PROJECT_ID},
    \"source_branch\": \"${CI_COMMIT_REF_NAME}\",
    \"target_branch\": \"${TARGET_BRANCH}\",
    \"remove_source_branch\": true,
    \"title\": \"WIP: ${CI_COMMIT_REF_NAME}\",
    \"assignee_id\":\"${GITLAB_USER_ID}\"
}";

# Require a list of all the merge request and take a look if there is already
# one with the same source branch
LISTMR=`curl --silent "${HOST}${CI_PROJECT_ID}/merge_requests?state=opened" --header "PRIVATE-TOKEN:${GITLAB_PRIVATE_TOKEN}"`;
COUNTBRANCHES=`echo ${LISTMR} | grep -o "\"source_branch\":\"${CI_COMMIT_REF_NAME}\"" | wc -l`;

# No MR found, let's create a new one
if [ ${COUNTBRANCHES} -eq "0" ]; then
    status=$(curl -X POST "${HOST}${CI_PROJECT_ID}/merge_requests" \
        --header "PRIVATE-TOKEN:${GITLAB_PRIVATE_TOKEN}" \
        --header "Content-Type: application/json" \
        --data "${BODY}" \
        --write-out %{http_code} \
        --silent \
        --output /dev/null)

    if [ "$status" -eq "201" ]; then
        echo "Opened a new merge request: WIP: ${CI_COMMIT_REF_NAME} and assigned to you";
        exit;
    else
        echo "Error with response code: ${status}"
        exit 1
    fi
fi

echo "No new merge request opened";
