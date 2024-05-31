<?php
function create_keystorefile() {
	//for android only
	$f = __DIR__."/key.pass";
	if(file_exists($f)) {
		$pass = trim(file_get_contents($f));
		$f = "android/keystore.properties";
		$txt = <<<EOF
storePassword=yourstorepassword
keyPassword=yourkeypassword
keyAlias=yourkeyalias
storeFile=release-keystore
EOF;
		$rpl = [
			'yourstorepassword'=> $pass,
			'yourkeypassword'=> $pass,
			'yourkeyalias'=> getenv('accid'),
			'release-keystore'=> __DIR__."/key.jks",
		];
		foreach($rpl as $k=>$v) $txt = str_replace($k,$v, $txt);
		if(!file_exists($f)) file_put_contents($f, $txt);
	}
}
function edit_build_gradle() {
	$f = "android/app/build.gradle";
	$str = file_get_contents($f);
	//keystore
	$add=<<<EOF
			debuggable false
		signingConfig signingConfigs.release
EOF;
	$str = str_replace('release {',"release {\n$add",$str);
	//version
	#$str = str_replace('versionCode 1',"versionCode {$cfg['version-code']}",$str);
	#$str = str_replace('versionName "1.0"','versionName "'.$cfg['version'].'"',$str);
	//sign
	$add=<<<EOF
def keystorePropertiesFile = rootProject.file("keystore.properties")
def keystoreProperties = new Properties()
keystoreProperties.load(new FileInputStream(keystorePropertiesFile))
EOF;
	$str = str_replace("apply plugin: 'com.android.application'", "apply plugin: 'com.android.application'\n\n$add\n",$str);
	$add=<<<EOF
signingConfigs {
    release {
        keyAlias keystoreProperties['keyAlias']
        keyPassword keystoreProperties['keyPassword']
        storeFile file(keystoreProperties['storeFile'])
        storePassword keystoreProperties['storePassword']
    }
}
EOF;
	$str = str_replace('buildTypes {',"$add\n\tbuildTypes {", $str);
	//package
	if(strpos($str,'namespace ')!==false) {
		$str = preg_replace('#namespace (\'|")(.*?)(\'|")#', 'namespace "'.getenv('package').'"',$str);
	}
	else $str = str_replace('android {', "android {\n\tnamespace '".getenv('package')."'", $str);
	$str = preg_replace('#applicationId (\'|")(.*?)(\'|")#', 'applicationId "'.getenv('package').'"',$str);

	file_put_contents($f, $str);
}
