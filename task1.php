<?php
$task = isset($argv[1])? $argv[1]:'';

include __DIR__.'/util.php';

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
if($task=='sh_add_plugins') {
	$list = pick_cordova_plugins();
	$sh ='';
	foreach($list as $k) $sh.= "cordova plugin add $k\n";
	file_put_contents(__DIR__.'/plugin.sh', $sh);
}
if($task=='edit_ksgd') {
	clone_sample_android();
	create_keystorefile();
	edit_build_gradle();
	edit_res_values();
}
if($task=='test') {
	putenv('package=com.hoang.abc1');
	putenv('name=App');
	putenv('accid=test130');
	putenv('email=kythuat@gmail.com');
	clone_sample_android();
}
if($task=='test1') {
	foreach(['settings.gradle','gradlew.bat','gradlew','gradle.properties','build.gradle',] as $f) unlink($f);
	foreach(['gradle','app'] as $d) deleteDir($d,1);
}
