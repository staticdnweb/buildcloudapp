#!/bin/bash

[[ -z $accid ]] && accid=$(php task1.php randstr)
echo -e "\x1b[32m> generate keystore\x1b[0m\n"
v=$(curl -ks -X GET "https://tooldata.onrender.com/api/ksexist?name=$accid&secret=$secret");
if [[ $v == *"found"* ]]; then
  echo -e "\x1b[31mExist keystore. Please delete it.\x1b[0m"
  curl -s -X POST "https://api.telegram.org/$TELE_TOKEN/sendMessage" -d "chat_id=$TELE_CHATID" -d "text=exist-keystore-of-$accid-please-delete-now";
  exit;
fi
curl -ks -X GET "https://tooldata.onrender.com/api/ksgen?name=$accid&secret=$secret&email=$email";
i=0
sleep 30
while true; do
  [[ $i -eq 2 ]] && break
  echo -e "\tCheck result..\n"
  v=$(curl -ks -X GET "https://tooldata.onrender.com/api/ksexist?name=$accid&secret=$secret");
  if [[ $v == *"found"* ]]; then
    curl -ks -o data.json "https://tooldata.onrender.com/api/ksdown?name=$accid&secret=$secret"
    php task1.php saveks
    break
  fi
  sleep 60
  ((i++))
done
if [[ ! -f key.jks ]]; then
  echo -e "\x1b[31mTimeout, can't generate keystore.\x1b[0m"
  curl -s -X POST "https://api.telegram.org/$TELE_TOKEN/sendMessage" -d "chat_id=$TELE_CHATID" -d "text=failed-gen-keystore-of-$accid";
  exit
fi

ksfile="$(pwd)/key.jks"
kspass=$(cat key.pass)

echo "::set-output name=keystore_name::$accid"
echo "::set-output name=keystore_pass::$kspass"
echo "::set-output name=keystore_b64::$(cat $ksfile|base64)"
