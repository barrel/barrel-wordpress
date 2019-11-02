#!/bin/bash

SCRIPT_PATH="`dirname \"$0\"`"
source $SCRIPT_PATH/colors.sh

echo "${YELLOW}Starting visual regression test suite...${DEFAULT}"
cd ./private/scripts/backstopjs/
npm ci
npm start -- --site=barrel-base-theme
EXIT_CODE="$?"
if [[ "$EXIT_CODE" -ne 0 ]]; then
  echo "${RED}Visual regressions potentially detected!${DEFAULT}"
  exit $EXIT_CODE
else
  echo "${GREEN}Visual regressions completed, no errors detected!${DEFAULT}"
fi