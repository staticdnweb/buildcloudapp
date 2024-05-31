<?php
include __DIR__.'/utils.php';

$task = isset($argv[1])? $argv[1]:'';

if($task=='pick_device') {
	$file = isset($argv[2])? $argv[2]:'';
	$str = file_get_contents($file);
	$ar=json_decode( $str,1);
	$list=[
		'PHYSICAL'=>[], 'VIRTUAL'=>[]
	];
	foreach($ar as $it) {
		if(!empty($it['form']) && stripos($it['name'],'watch')===false) {
			$list[$it['form']][] = ['name'=>$it['codename'],'ver'=>$it['supportedVersionIds']];
		}
	}
	#shuffle($ar);
	$ar = pick_more($list['PHYSICAL'], randint(1,3));
	$ar1 = pick_more($list['VIRTUAL'], randint(1,3));
	/*foreach($ar as $it) {
		#preg_match('#MODEL_NAME: (.*)#',$s,$m);
		#preg_match('#OS_VERSION_IDS: (.*)#',$s,$m1);
		$md = sprintf('model=%s,version=%s,locale=en,orientation=portrait',$it['MODEL_NAME'], $it['OS_VERSION_IDS']);
	}*/
	saveData('devices', array_merge($ar,$ar1));	//json_encode()
}
if($task=='build_sh') {
	$sh=<<<EOF
#!/bin/bash

EOF;
	$devs = getData('devices',[]);
	if(!empty($devs)) {
		$sh.= 'echo "> firebase test lab for android\n";'."\n";
		$sh.='gcloud firebase test android run --type robo --app app-release.aab ';
		foreach($devs as $it) {
			$md = sprintf('"model=%s,version=%s,locale=en,orientation=portrait"',$it['name'], pick_one($it['ver']));
			$sh.= "--device $md ";
		}
		#$sh.= "--os-version-ids=21,22,23,24,25,26,27,28,29,30,31,32,33,34 ";
		$sh.= "--timeout 90s "; //--locales=en,vi --orientations=portrait,landscape
	}
	file_put_contents(__DIR__.'/run.sh', dos2unix($sh));
	#chmod(__DIR__.'/run.sh',0755);
}
