#!/bin/bash

# Script to execute a curl statement and check the return code. If it failed
# then give a helpful message and exit the job with a failure code.
#
# Arguments
#   1st = (string) path and filename relative to the gitlab_templates project root.

FILENAME=$1

if [[ $DRUPALORG_CI_SERVER_URL == "" || $_CURL_TEMPLATES_REPO == "" || $_CURL_TEMPLATES_REF == "" || $FILENAME == "" ]]; then
  EXIT_CODE=-1
else
  echo "get-file-via-curl.sh executing curl --retry 3 -OLf $DRUPALORG_CI_SERVER_URL/$_CURL_TEMPLATES_REPO/-/raw/$_CURL_TEMPLATES_REF/$FILENAME"
  curl --retry 3 -OLf "$DRUPALORG_CI_SERVER_URL/$_CURL_TEMPLATES_REPO/-/raw/$_CURL_TEMPLATES_REF/$FILENAME" || EXIT_CODE=$?
fi

# If the curl failed then display useful information and exit the job.
if [[ $EXIT_CODE != "" ]]; then
  echo -e "$DIVIDER\n [ERROR] Curl failure\n" \
    "DRUPALORG_CI_SERVER_URL=$DRUPALORG_CI_SERVER_URL\n _CURL_TEMPLATES_REPO=$_CURL_TEMPLATES_REPO\n" \
    "_CURL_TEMPLATES_REF=$_CURL_TEMPLATES_REF\n FILENAME=$FILENAME\n" \
    "FULL_URL=$DRUPALORG_CI_SERVER_URL/$_CURL_TEMPLATES_REPO/-/raw/$_CURL_TEMPLATES_REF/$FILENAME\n \n" \
    "Job ending with EXIT_CODE=$EXIT_CODE $DIVIDER\n"
  exit $EXIT_CODE
fi
