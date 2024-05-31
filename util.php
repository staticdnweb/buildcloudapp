<?php
function randomString($length = 10, $characters=null) {
    if(!$characters) $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    if(is_numeric(substr($randomString,0,1))) $randomString = chr(rand(97,122))."{$randomString}";
    return $randomString;
}
function randint($n, $n1=60) {
	return mt_rand($n, $n1);
    #return $n+ rand(0,$n1);
}
function ifTrue() {
	return randint(0,1)==1;
}
function pick_more($arr, $n) {
	$i=0;
    shuffle($arr);$r=[];$vv=[];
    while(1) {
	    foreach($arr as /*$i=>*/$v ) {
	        if(count($vv)==$n) break;
	        if(!in_array($v,$vv)) $vv[]=$v;//$r[$v]=1;
	    }
	    if(count($vv)==$n) break;
	}
    return $vv;//array_keys($r);
}
function randomLightColor($hash=1) {
    // Generate random values for red, green, and blue (in the lighter range 128-255)
    $r = rand(128, 255);
    $g = rand(128, 255);
    $b = rand(128, 255);

    // Convert each component to a 2-digit hex string
    $rHex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
    $gHex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
    $bHex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);

    // Combine into a hex color string
    $hexColor = ($hash?'#':'') . $rHex . $gHex . $bHex;

    return $hexColor;
}
function randomDarkColor($hash=1) {
    $dt = '';
    for($o=1;$o<=3;$o++)
    {
        $dt .= str_pad( dechex( mt_rand( 0, 127 ) ), 2, '0', STR_PAD_LEFT);
    }
    return $hash? '#'.$dt : $dt;
}
function randColor() {
    //return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
}
function randomIcon($saveTo, $w=512,$h=512) {
    // randoms coords for polygons
    $coords = [];
    foreach(range(0,127) as $p){
        $coords[] = rand(0,$w);
        $coords[] = rand(0,$h);
    }

    // create image
    $image = imagecreatetruecolor($w, $h);

    // fill the background
    imagefilledrectangle($image, 0, 0, $w, $h, imagecolorallocate($image, mt_rand(0,255) , mt_rand(0,255) , mt_rand(0,255)));

    // draw some polygons
    imagefilledpolygon($image, $coords, 48, imagecolorallocate($image, mt_rand(0,255) , mt_rand(0,255) , mt_rand(0,255)));
    imagefilledpolygon($image, $coords, 24, imagecolorallocate($image, mt_rand(0,255) , mt_rand(0,255) , mt_rand(0,255)));


    #header('Content-type: image/png');
    imagepng($image, $saveTo);
}
function deleteDir($dirPath, $itself=false) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '/*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file, true);
        } else {
            unlink($file);
        }
    }
    if($itself) rmdir($dirPath);
}
function recurse_copy($src,$dst,$override=true) {
    if(!file_exists($src)) {
        echo "\033[31m 404 $src\033[0m\n";return;
    }
    $dir = opendir($src);
    if(!is_dir($dst)) @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                recurse_copy($src . '/' . $file,$dst . '/' . $file, $override);
            }
            else if($override || !file_exists($dst . '/' . $file)) {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}
//https://www.npmjs.com/search?q=keywords%3Aecosystem%3Acordova&page=10&perPage=20
function pick_cordova_plugins() {
	$list = <<<EOF
cordova-plugin-battery-status
cordova-plugin-camera
cordova-plugin-device
cordova-plugin-dialogs
cordova-plugin-file
cordova-plugin-geolocation
cordova-plugin-inappbrowser
cordova-plugin-media
cordova-plugin-media-capture
cordova-plugin-network-information
cordova-plugin-screen-orientation
cordova-plugin-splashscreen
cordova-plugin-statusbar
cordova-plugin-vibration
cordova-plugin-appsflyer-sdk
cordova-plugin-purchase
pushwoosh-pgb-plugin@8.3.26
cordova-sqlite-storage
cordova-plugin-inapppurchases
cordova-plugin-nativestorage
cordova-plugin-file-transfer
onesignal-cordova-plugin
com.adjust.sdk
cordova-plugin-exclude-files
admob-plus-cordova
cordova-plugin-fbsdk
cordova-plugin-consent
EOF;
	$list = array_filter(explode("\n", $list));
	return pick_more($list, randint(1, 3));
}
function create_keystorefile() {
	//for android only
	$f = __DIR__."/key.pass";
	if(file_exists($f)) {
		$pass = trim(file_get_contents($f));
		$f = "keystore.properties";
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
	$f = "app/build.gradle";
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
function edit_res_values() {
	$f = "app/src/main/res/values/strings.xml";
	$str = file_get_contents($f);
	if(strpos($str,'app_name')!==false) {
		$doc = new DOMDocument();
		$doc->loadXML($str);
		$xpath = new DOMXPath($doc);
		$appNameNode = $xpath->query('//string[@name="app_name"]')->item(0);
		if ($appNameNode !== null) {
			$appNameNode->nodeValue = getenv('name');
			$str = $doc->saveXML();
			file_put_contents($f, $str);
		}
	}
}
function clone_sample_android() {
	recurse_copy('source/demo/', __DIR__);
	//build.gradle
	$f="app/build.gradle";
	$str = file_get_contents($f);
	$str = str_replace('com.example.chris.fstest', getenv('package'),$str);
	#$str = str_replace('17', '24', $str);
	$str = str_replace('30',randint(33,34),$str);
	file_put_contents($f, $str);
	//layout
	$f="app/src/main/res/layout/activity_main.xml";
	$str = file_get_contents($f);
	$str = str_replace('Hello World!', randomString(),$str);
	if(ifTrue()) $str = str_replace('android:textSize="32sp"','',$str);
	else $str = str_replace('32sp', randint(18,40).'sp',$str);

	if(ifTrue()) $str = str_replace('android:textColor="#ff0000"','',$str);
	else $str = str_replace('#ff0000',randColor(),$str);

	file_put_contents($f, $str);
	//color
	$f="app/src/main/res/values/colors.xml";
	$str = file_get_contents($f);
	$str = str_replace('#3F51B5',randomDarkColor(),$str);
	$str = str_replace('#303F9F',randomDarkColor(),$str);
	$str = str_replace('#FF4081',randomDarkColor(),$str);
	file_put_contents($f, $str);
	//string
	$f="app/src/main/res/values/strings.xml";
	$str = file_get_contents($f);
	$str = str_replace('Fstest',getenv('name'),$str);
	file_put_contents($f, $str);
	
	//create icon
	randomIcon(__DIR__.'/icon.png',512,512);
	foreach(['mipmap-hdpi','mipmap-mdpi','mipmap-xhdpi','mipmap-xxhdpi','mipmap-xxxhdpi'] as $d) copy(__DIR__.'/icon.png', "app/src/main/res/{$d}/ic_launcher.png");
	unlink(__DIR__.'/icon.png');
	
	//java files
	$p=explode('.',getenv('package'));
	$path = 'app/src/main/java/'.join('/',$p);
	if(!is_dir($path)) mkdir($path,0755,true);
	copy("app/src/main/java/com/example/chris/fstest/MainActivity.java","$path/MainActivity.java");
	deleteDir("app/src/main/java/com/example",1);
	if(count(scandir("app/src/main/java/com"))==2) deleteDir("app/src/main/java/com",1);
	$str = file_get_contents("$path/MainActivity.java");
	$str = str_replace('com.example.chris.fstest', getenv('package'), $str);
	file_put_contents("$path/MainActivity.java", $str);
}
