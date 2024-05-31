<?php
class Gmail {
	private $token;
	//@param $cfg={accId,}
	function __construct( $token){
		$this->token = $token;
	}
	function getAccessToken() {
		return $this->token;
	}
	//$ft={max,q}
	function listMails($ft=[]) {
		$url = "https://www.googleapis.com/gmail/v1/users/me/messages/?";
		if(!empty($ft['max'])) $url.= '&maxResults='.$ft['max'];
		if(!empty($ft['q'])) $url.= '&q='.urlencode($ft['q']);

		$opt[CURLOPT_HTTPHEADER] = [
			"Authorization: Bearer ".$this->getAccessToken(),
			"Content-Type: application/json"
		];
		$res = curl_get($url,$opt);#print_r($res);
		$res = json_decode($res,true);
		return $res;
	}
	function readMail($id) {
		$url = "https://www.googleapis.com/gmail/v1/users/me/messages/{$id}";
		$opt[CURLOPT_HTTPHEADER] = [
			"Authorization: Bearer ".$this->getAccessToken(),
			"Content-Type: application/json"
		];
		$res = curl_get($url,$opt);#print_r($res);
		$res = json_decode($res,true);
		return isset($res['snippet'])? $res['snippet']:'';
	}
	function list_mails_googleplay() {
		$r = $this->listMails(['q'=>'from:(googleplay-developer-support@google.com OR no-reply-googleplay-developer@google.com OR removals@google.com OR noreply-play-console@google.com)','max'=>500]);
		$msgs =  !empty($r['messages'])? $r['messages']: [];
		$threads=[];
		foreach($msgs as $it) {
			$threads[$it['threadId']] = $it['id'];
		}
		return $threads;
	}
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

     if(is_array($opts) && count($opts)) curl_setopt_array($ch, $opts);
     //cookie
     if($refresh_cookie) {
          curl_setopt($ch, CURLOPT_COOKIESESSION, true);
     }
    
     $resp = curl_exec($ch);
     curl_close($ch);
     return $resp;
}
function curl_post($url, $opts = array(), $data = array(),$cookie=false) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    //curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Authorization: Client-ID ' . client_id ));
    curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data)? http_build_query($data): $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

    //cookie
    if($cookie) {
        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, sys_get_temp_dir().'/'.$cookie);
        curl_setopt( $ch, CURLOPT_COOKIEFILE, sys_get_temp_dir().'/'.$cookie );
    }
    if(is_array($opts) && count($opts)) curl_setopt_array($ch, $opts);
    $resp = curl_exec($ch);
     curl_close($ch);
     return $resp;
}
function telegram($msg, $token, $chatID) {
  if(strpos($msg, '[testmail]')===false) $msg = "[testmail] ".$msg;
  try {
    $url = "https://api.telegram.org/" . $token . "/sendMessage?chat_id=" . $chatID;
      $url = $url . "&text=" . urlencode(substr($msg,0,500));
      $ch = curl_init();
      $optArray = array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_CONNECTTIMEOUT=> 0,
          CURLOPT_TIMEOUT=> 120
      );
      curl_setopt_array($ch, $optArray);
      $result = curl_exec($ch);
      curl_close($ch);
      $result = json_decode($result,true);
      if(is_array($result) && $result['ok']==false && $result['description']=='Unauthorized') {
          echo "Error send to telegram";
      }
      return $result;
  }
  catch(\Throwable $e){}
}
function pick_one($arr, $notMatch=null, $try=1) {
	if(!count($arr)) return '';
	$id = array_rand($arr);
	if($notMatch && in_array($arr[$id], (array)$notMatch) && $try<10) return pick_one($arr, $notMatch, ++$try);
	return $arr[$id];
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
function dos2unix($cmd){
    $cmd = str_replace("\r", "", $cmd); //same dos2unix
    return $cmd;
}
function randint($n, $n1=60) {
    return random_int($n,$n1);
}
function saveData($k,$v=null) {
	$f = __DIR__.'/_data.json';	//sys_get_temp_dir()
	$dt = file_exists($f)? json_decode(file_get_contents($f),1): [];
	if(!is_array($dt)) $dt=[];
	if(is_array($k)) $dt = array_merge($dt,$k);
	else $dt[$k] = $v;

	file_put_contents($f, json_encode($dt));
}
function getData($k,$v='') {
	$f = __DIR__.'/_data.json';
	$dt = file_exists($f)? json_decode(file_get_contents($f),1): [];
	if(!is_array($dt)) $dt=[];
	return isset($dt[$k])? $dt[$k]: $v;
}
