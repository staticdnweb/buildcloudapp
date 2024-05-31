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

#echo -e "\x1b[32m> prepare source app\x1b[0m\n"
#n=$(shuf -i 1-5 -n 1)
#if [[ true || $n -eq 1 ]]; then
#	git clone https://github.com/android/animation-samples.git
#	mv animation-samples/ActivitySceneTransitionBasic/* .
#fi

echo -e "\x1b[32m> clone & edit app $name|$package\x1b[0m\n";
php task1.php edit_ksgd

echo -e "\x1b[32m> build\x1b[0m\n";
bash gradlew bundleRelease

echo -e "\x1b[32m> send result\x1b[0m\n"
if [ -f app/build/outputs/bundle/release/app-release.aab ]; then
  cp app/build/outputs/bundle/release/app-release.aab app-release.aab
  zip $accid-$package-demo.zip app-release.aab icon.png

  curl -s -X POST "https://api.telegram.org/$TELE_TOKEN/sendMessage" -d "chat_id=$TELE_CHATID" -d "text=success-build-aab-of-$accid";
  curl -F chat_id="$TELE_CHATID" -F document=@$accid-$package-demo.zip "https://api.telegram.org/$TELE_TOKEN/sendDocument"
else
  curl -s -X POST "https://api.telegram.org/$TELE_TOKEN/sendMessage" -d "chat_id=$TELE_CHATID" -d "text=failed-build-aab-of-$accid";
fi
