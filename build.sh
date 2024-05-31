#!/bin/bash

echo -e "\x1b[32m> install deps\x1b[0m\n"
npm install -g cordova
cordova -v
#cordova=$(pwd)/node_modules/.bin/cordova

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
echo -e "\x1b[32m> create app $name|$package\x1b[0m\n";
cordova create app $package "$name"
cd app
cordova platform add android@12.0.0
[ ! -d platforms/android/gradle ] && cp -r ../android/gradle platforms/android

echo -e "\x1b[32m> add plugin\x1b[0m\n"
php task1.php sh_add_plugins
bash plugin.sh

echo -e "\x1b[32m> build aab\x1b[0m\n"
kspass=$(cat ../key.pass)
cordova build android --release -- --packageType=bundle > build.txt 2>&1
if [[ $(cat build.txt) == *"BUILD FAILED"* ]]; then
  echo -e "\x1b[31m build failed \x1b[0m\n";cat build.txt;
  curl -s -X POST "https://api.telegram.org/$TELE_TOKEN/sendMessage" -d "chat_id=$TELE_CHATID" -d "text=build-failed-of-$accid";
  exit
fi

echo -e "\x1b[32m> sign aab\x1b[0m\n"
cd platforms/android/app/build/outputs/bundle/release
echo $kspass|jarsigner -verbose -sigalg SHA1withRSA -digestalg SHA1 -keystore $ksfile app-release.aab $accid > build.txt 2>&1
rm -f app-release1.aab
/usr/local/lib/android/sdk/build-tools/33.0.0/zipalign -v 4 app-release.aab app-release1.aab > build.txt 2>&1
rm -f app-release.aab
[ -f app-release1.aab ] && mv app-release1.aab app-release.aab            

echo -e "\x1b[32m> send result\x1b[0m\n"
if [ -f app-release.aab ]; then
  zip $accid-$package-demo-wv.zip app-release.aab #icon.png
  curl -s -X POST "https://api.telegram.org/$TELE_TOKEN/sendMessage" -d "chat_id=$TELE_CHATID" -d "text=success-build-aab-of-$accid";
  curl -F chat_id="$TELE_CHATID" -F document=@$accid-$package-demo-wv.zip "https://api.telegram.org/$TELE_TOKEN/sendDocument"
else
  curl -s -X POST "https://api.telegram.org/$TELE_TOKEN/sendMessage" -d "chat_id=$TELE_CHATID" -d "text=failed-build-aab-of-$accid";
fi
