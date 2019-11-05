#!/bin/bash

# construct the ARTIFACT_URL
BASE_URL="https://barrel.gitlab.io/-"
JOB_PATH="-/jobs/$CI_JOB_ID"
ARTIFACT_PATH="artifacts/private/scripts/backstopjs/backstop_data"
REPORT="$PANTHEON_SITE_ID/html_report/index.html"
ARTIFACT_URL="$BASE_URL/$CI_PROJECT_NAME/$JOB_PATH/$ARTIFACT_PATH/$REPORT"
SCRIPT_PATH="`dirname \"$0\"`"

source $SCRIPT_PATH/colors.sh

echo "${YELLOW}Starting visual regression test suite...${DEFAULT}"
cd ./private/scripts/backstopjs/
npm ci
npm start -- --site=$PANTHEON_SITE_ID
EXIT_CODE="$?"
echo "The URL for this report: \n${YELLOW}$ARTIFACT_URL${DEFAULT}"
if [[ "$EXIT_CODE" -ne 0 ]]; then
  echo "${RED}Visual regressions potentially detected!${DEFAULT}"
  exit $EXIT_CODE
else
  echo "${GREEN}Visual regressions completed, no errors detected!${DEFAULT}"
fi
