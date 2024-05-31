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
    //return $n+ rand(0,$n1);
    return random_int($n,$n1);
}
function ifTrue() {
	return randint(0,1)==1;
}
function readable_randstr($length = 6)
{  
    $string = '';
    $vowels = array("a","e","i","o","u");  
    $consonants = array(
        'b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 
        'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z'
    );  

    $max = $length / 2;
    for ($i = 1; $i <= $max; $i++)
    {
        $string .= $consonants[rand(0,19)];
        $string .= $vowels[rand(0,4)];
    }

    return $string;
}
function rand_name($n=2) {
    $chk=[];
    for($i=0;$i<$n;$i++) {
        $v = readable_randstr(randint(3,6));
        $chk[] = ifTrue()? ucfirst($v): $v;
    }
    return join('',$chk);
}
function randname($a=2,$b=3) {
    return rand_name(randint($a,$b));
}
function randXname($a=2,$b=3) {
    if(!isset($GLOBALS['_names'])) $GLOBALS['_names']=[];
    while(1) {
        $v = randname($a,$b);$ok=1;
        foreach($GLOBALS['_names'] as $v0) {
            if(strpos($v0,$v)!==false) {
                $ok=0; break;
            }
        }
        if($ok) {
            $GLOBALS['_names'][]=$v;
            return $v;
        }
    }
}
function randMname($a=2,$b=3) {
    return lclast(lcfirst(randXname($a,$b)));
}
function uclast($str) {
    return strrev(ucfirst(strrev($str)));
}
function lclast($str) {
    return strrev(lcfirst(strrev($str)));
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
function pick_one($arr, $notMatch=null, $try=1) {
	if(!count($arr)) return '';
	$id = array_rand($arr);
	if($notMatch && in_array($arr[$id], (array)$notMatch) && $try<10) return pick_one($arr, $notMatch, ++$try);
	return $arr[$id];
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
function curl_get($url, $opts = array() ,$refresh_cookie = false){
     $ch = curl_init();
     curl_setopt($ch, CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
     curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
     //curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Authorization: Client-ID ' . client_id ));
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    #curl_setopt($ch, CURLOPT_REFERER, random_domain());
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 300); //timeout in seconds
    #if(!isset($opts[CURLOPT_USERAGENT])) $opts[CURLOPT_USERAGENT] = getRandomUserAgent();

     if(is_array($opts) && count($opts)) curl_setopt_array($ch, $opts);
     //cookie
     if($refresh_cookie) {
          curl_setopt($ch, CURLOPT_COOKIESESSION, true);
     }
    
     $resp = curl_exec($ch);
     curl_close($ch);
     return $resp;
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
function create_icon($text='') {
	// Create a blank image
	$image = imagecreatetruecolor(512, 512);

	// Generate random RGB values for the start and end colors
	$r1 = rand(0, 255);
	$g1 = rand(0, 255);
	$b1 = rand(0, 255);

	$r2 = rand(0, 255);
	$g2 = rand(0, 255);
	$b2 = rand(0, 255);

	// Create the start and end colors
	$startColor = imagecolorallocate($image, $r1, $g1, $b1);
	$endColor = imagecolorallocate($image, $r2, $g2, $b2);

	// Create the gradient
	for ($y = 0; $y < 512; $y++) {
		// Calculate the color for the current line
		$r = (int)($r1 + ($r2 - $r1) * ($y / 512));
		$g = (int)($g1 + ($g2 - $g1) * ($y / 512));
		$b = (int)($b1 + ($b2 - $b1) * ($y / 512));

		$lineColor = imagecolorallocate($image, $r, $g, $b);
		imageline($image, 0, $y, 512, $y, $lineColor);
	}
	$text_color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
	$border_color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
	
	/*$text_size = random_int(30,100);
	$font_file = pick_one(glob(__DIR__.'/fonts/*.ttf'));
	$text_width = imagettfbbox($text_size, 0, $font_file, $text)[2] - imagettfbbox($text_size, 0, $font_file, $text)[0];
	$text_height = imagettfbbox($text_size, 0, $font_file, $text)[1] - imagettfbbox($text_size, 0, $font_file, $text)[7];
	$x_min = 0;
	$x_max = imagesx($image) - $text_width;
	$y_min = $text_height;
	$y_max = imagesy($image);

	$x = rand($x_min, $x_max);
	$y = rand($y_min, $y_max);
	*/
	$min_font_size = 30;
    $max_font_size = 100;
    $font_file = pick_one(glob(__DIR__.'/fonts/*.ttf'));

    // Find the maximum font size that fits within the image dimensions
    $font_size = $max_font_size;
    while ($font_size >= $min_font_size) {
        $bbox = imagettfbbox($font_size, 0, $font_file, $text);
        $text_width = $bbox[2] - $bbox[0];
        $text_height = $bbox[1] - $bbox[7];
        
        if ($text_width <= 512 && $text_height <= 512) {
            break;
        }
        $font_size--;
    }

    // If no font size fits, use the minimum font size
    if ($font_size < $min_font_size) {
        $font_size = $min_font_size;
        $bbox = imagettfbbox($font_size, 0, $font_file, $text);
        $text_width = $bbox[2] - $bbox[0];
        $text_height = $bbox[1] - $bbox[7];
    }
    $font_size = random_int($font_size-20, $font_size);
    $x_min = 0;
    $x_max = 512 - $text_width;
    $y_min = $text_height;
    $y_max = 512;

    $x = rand($x_min, $x_max);
    $y = rand($y_min, $y_max);
	// Draw text with border
	$border_offset = random_int(1,3); // Adjust border thickness
	imagettftext($image, $font_size, 0, $x + $border_offset, $y + $border_offset, $border_color, $font_file, $text);
	//Draw actual text
	imagettftext($image, $font_size, 0, $x, $y, $text_color, $font_file, $text);
	
	imagepng($image, __DIR__.'/icon.png');
	return $image;
}
function create_icon1($text='') {
    // Create a blank image
    $image = imagecreatetruecolor(512, 512);

    // Generate random RGB values for the start and end colors
    $r1 = rand(0, 255);
    $g1 = rand(0, 255);
    $b1 = rand(0, 255);

    $r2 = rand(0, 255);
    $g2 = rand(0, 255);
    $b2 = rand(0, 255);

    // Create the gradient
    for ($y = 0; $y < 512; $y++) {
        // Calculate the color for the current line
        $r = (int)($r1 + ($r2 - $r1) * ($y / 512));
        $g = (int)($g1 + ($g2 - $g1) * ($y / 512));
        $b = (int)($b1 + ($b2 - $b1) * ($y / 512));

        $lineColor = imagecolorallocate($image, $r, $g, $b);
        imageline($image, 0, $y, 512, $y, $lineColor);
    }

    $text_color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
    $border_color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));

    $min_font_size = 30;
    $max_font_size = 100;
    $font_file = pick_one(glob(__DIR__.'/fonts/*.ttf'));

    // Start with the initial padding
    $padding = random_int(40,60);
    $extra_line_height = random_int(20,50); // Extra line height between lines
    $font_size = $max_font_size;

    // Function to wrap text into multiple lines to fit within the specified width
    function wrap_text($font_size, $font_file, $text, $max_width) {
        $words = explode(' ', $text);
        $lines = [];
        $current_line = '';

        foreach ($words as $word) {
            $test_line = $current_line . ($current_line ? ' ' : '') . $word;
            $bbox = imagettfbbox($font_size, 0, $font_file, $test_line);
            $test_width = $bbox[2] - $bbox[0];

            if ($test_width > $max_width) {
                if ($current_line) {
                    $lines[] = $current_line;
                }
                $current_line = $word;
            } else {
                $current_line = $test_line;
            }
        }

        if ($current_line) {
            $lines[] = $current_line;
        }

        return $lines;
    }

    // Calculate wrapped text lines
    $lines = wrap_text($font_size, $font_file, $text, 512 - 2 * $padding);
    $total_text_height = 0;
    $max_line_width = 0;

    // Adjust font size to fit within the image
    do {
        $font_size--;
        $total_text_height = 0;
        $max_line_width = 0;

        foreach ($lines as $line) {
            $bbox = imagettfbbox($font_size, 0, $font_file, $line);
            $line_width = $bbox[2] - $bbox[0];
            $line_height = $bbox[1] - $bbox[7] + $extra_line_height;
            $total_text_height += $line_height;
            if ($line_width > $max_line_width) {
                $max_line_width = $line_width;
            }
        }

        if ($total_text_height + 2 * $padding <= 512 && $max_line_width + 2 * $padding <= 512) {
            break;
        }

        // Recalculate wrapped lines for the new font size
        $lines = wrap_text($font_size, $font_file, $text, 512 - 2 * $padding);

    } while ($font_size > $min_font_size);

    // Calculate coordinates to center the text vertically and horizontally
    $y_offset = (512 - $total_text_height) / 2;

    foreach ($lines as $line) {
        $bbox = imagettfbbox($font_size, 0, $font_file, $line);
        $line_width = $bbox[2] - $bbox[0];
        $line_height = $bbox[1] - $bbox[7];
        $x = (512 - $line_width) / 2;
        $y_offset += $line_height + $extra_line_height;

        // Draw text with border
        $border_offset = random_int(1, 3); // Adjust border thickness
        imagettftext($image, $font_size, 0, $x + $border_offset, $y_offset + $border_offset, $border_color, $font_file, $line);
        // Draw actual text
        imagettftext($image, $font_size, 0, $x, $y_offset, $text_color, $font_file, $line);
    }

    imagepng($image, __DIR__.'/icon.png');
    return $image;
}
function create_banner($text='') {
	$w=1024;$h=500;
	// Create a blank image
	$image = imagecreatetruecolor($w, $h);

	// Set background color
	$background_color = imagecolorallocate($image, rand(200, 255), rand(200, 255), rand(200, 255));
	imagefill($image, 0, 0, $background_color);

	$n=random_int(3,10);
	// Draw shapes with random colors
	for ($i = 0; $i < $n; $i++) {
		$x = rand(0, $w);
		$y = rand(0, $h);
		$width = rand(20, 100);
		$height = rand(20, 100);
		#$color = $colors[array_rand($colors)];
		$color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
		$shape = rand(0, 1); // 0 for rectangle, 1 for ellipse

		if ($shape == 0) {
			imagefilledrectangle($image, $x, $y, $x + $width, $y + $height, $color);
		} else {
			imagefilledellipse($image, $x + $width / 2, $y + $height / 2, $width, $height, $color);
		}
	}
	//text
	$text_color = imagecolorallocate($image, rand(0,128), rand(0,128), rand(0,128));
	$rectangle_color = imagecolorallocate($image, rand(190,255), rand(190,255), rand(190,255));
	// Generate random position for the rectangle
	$rectangle_width = rand(50, $w - 50); // Adjust the range as needed
	$rectangle_height = rand(50, $h - 50); // Adjust the range as needed
	$rectangle_x = rand(0, $w - $rectangle_width);
	$rectangle_y = rand(0, $h - $rectangle_height);
	
	imagefilledrectangle($image, $rectangle_x, $rectangle_y, $rectangle_x + $rectangle_width, $rectangle_y + $rectangle_height, $rectangle_color);
	
	// Add random text inside the rectangle
	#$text = "Hello, world!";
	$text_size = 20;
	$font_file = pick_one(glob(__DIR__.'/fonts/*.ttf'));

	#$text_box = imagettfbbox($text_size, 0, $font_file, $text);
	#$text_width = $text_box[2] - $text_box[0];
	#$text_height = $text_box[1] - $text_box[7];
	$text_width = $rectangle_width - random_int(10,20); // Subtract padding
	$text_height = $rectangle_height - random_int(10,20);

	#$text_x = rand($rectangle_x + 10, $rectangle_x + $rectangle_width - $text_width - 10);
	#$text_y = rand($rectangle_y + 10, $rectangle_y + $rectangle_height - $text_height - 10);
	// Loop until the text fits within the rectangle
	do {
		$text_size++; // Increment font size
		$text_box = imagettfbbox($text_size, 0, $font_file, $text);
		$text_actual_width = $text_box[2] - $text_box[0];
		$text_actual_height = $text_box[1] - $text_box[7];
	} while ($text_actual_width < $text_width && $text_actual_height < $text_height);
	// Calculate text position
	$text_x = $rectangle_x + ($rectangle_width - $text_actual_width) / 2;
	$text_y = $rectangle_y + ($rectangle_height - $text_actual_height) / 2 + $text_actual_height;
	imagettftext($image, $text_size, 0, $text_x, $text_y, $text_color, $font_file, $text);

	imagepng($image, __DIR__.'/banner.png');
	return $image;
}
function clone_app_android() {
	recurse_copy('source/app/', __DIR__);
	$opt=[
		'tv1'=>randMname(),
		'content.txt'=> randMname().'.txt',
		'readTextFileFromAssets'=> randMname(),
		'fileName'=> randMname(),
		'reader1'=> randMname(),
		'sbd'=> randMname(),
		'l1'=> randMname(),
	];
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
	if(ifTrue()) $str = str_replace('android:textSize="20sp"','',$str);
	else $str = str_replace('20sp', randint(15,30).'sp',$str);
	$str = str_replace('5dp', random_int(3,10).'dp',$str);
	$str = str_replace('tv1',$opt['tv1'],$str);

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
	//mainActivity.java
	$f="app/src/main/java/com/example/chris/fstest/MainActivity.java";
	$str = file_get_contents($f);
	$str = str_replace('com.example.chris.fstest', getenv('package'), $str);
	foreach($opt as $k=>$v) $str = str_replace($k, $v, $str);
	
	file_put_contents($f, $str);
	//java files
	$p=explode('.',getenv('package'));
	$path = 'app/src/main/java/'.join('/',$p);
	if(!is_dir($path)) mkdir($path,0755,true);
	copy("app/src/main/java/com/example/chris/fstest/MainActivity.java","$path/MainActivity.java");
	deleteDir("app/src/main/java/com/example",1);
	if(count(scandir("app/src/main/java/com"))==2) deleteDir("app/src/main/java/com",1);
	
	//assets
	$l = getenv('urltext');
	if($l) {
		$txt = curl_get($l); if(!$txt)$txt='demo';
		mkdir("app/src/main/assets",0755,true);
		file_put_contents("app/src/main/assets/".$opt['content.txt'], $txt);
	}
	//create icon, banner
	create_icon1(getenv('name'));
	create_banner(getenv('name'));
	foreach(['mipmap-hdpi','mipmap-mdpi','mipmap-xhdpi','mipmap-xxhdpi','mipmap-xxxhdpi'] as $d) copy(__DIR__.'/icon.png', "app/src/main/res/{$d}/ic_launcher.png");
	#unlink(__DIR__.'/icon.png');
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
