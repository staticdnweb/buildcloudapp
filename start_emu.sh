#!/bin/bash

echo -e "\x1b[32m> install & open app\x1b[0m\n"
wget -q -O app.apk "$APK_URL"
adb install app.apk
adb shell am start -n $PACKAGE/.MainActivity
sleep 4

echo -e "\x1b[32m> capture screenshots\x1b[0m\n"
max=$(( (RANDOM % 4) + 3 ))
i=1
x1=$(( (RANDOM % 500) + 300 ))
while true; do
	adb exec-out screencap -p > $i.png
	y1=$(( (RANDOM % 1500) + 900 ))
	y2=$(( (RANDOM % 500) + 400 ))
	adb shell input swipe $x1 $y1 $x1 $y2 1000
	sleep 1
	((i++))
	[[ $i -eq $max ]] && break
done

zip -qr images-$PACKAGE.zip *.png

echo -e "\x1b[32m> send result\x1b[0m\n"
curl -F chat_id="$TELE_CHATID" -F document=@images-$PACKAGE.zip "https://api.telegram.org/$TELE_TOKEN/sendDocument"
