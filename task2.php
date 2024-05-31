<?php
$task = isset($argv[1])? $argv[1]:'';

include __DIR__.'/util1.php';

if($task=='saveks') {
	$f = __DIR__.'/data.json';
	$r = file_exists($f)? json_decode(file_get_contents($f),1): [];
	if(!empty($r['ks_b64'])) {
		$binary = base64_decode($r['ks_b64']);
		file_put_contents(__DIR__."/key.jks", $binary);
		file_put_contents(__DIR__."/key.pass", $r['ks_pass']);
	}
}
if($task=='randstr') {
	echo randomString(randint(10,30));
}
if($task=='edit') {
	clone_app_android();
	create_keystorefile();
	edit_build_gradle();
	edit_res_values();
}
if($task=='test') {
	putenv('package=com.hoang.abc1');
	putenv('name=App');
	putenv('accid=test130');
	putenv('email=kythuat@gmail.com');
	putenv('urltext=https://nopaste.net/XFOx6mdT71');
	clone_app_android();
}
if($task=='test1') {
	foreach(['settings.gradle','gradlew.bat','gradlew','gradle.properties','build.gradle','banner.png','icon.png'] as $f) @unlink($f);
	foreach(['gradle','app'] as $d) deleteDir($d,1);
}
if($task=='abc'){
	create_icon('hello');
	create_banner('hello');
}
