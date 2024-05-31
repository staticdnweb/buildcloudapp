#!/bin/bash

# auth
echo "> auth"
export GOOGLE_APPLICATION_CREDENTIALS="service-account.json"
gcloud auth activate-service-account --key-file=service-account.json
projid=$(jq -r ".project_id" "service-account.json")
gcloud config set project $projid
echo "set project $projid"

echo "> enable APIs"
gcloud services enable serviceusage.googleapis.com
gcloud services enable testing.googleapis.com
gcloud services enable toolresults.googleapis.com

echo "> prepare files"
#gcloud firebase test android models list --filter=virtual > virtual.txt
gcloud firebase test android models list --format=json > devs.txt
php task.php pick_device devs.txt
php task.php build_sh

echo "> run firebase test android"
bash run.sh
