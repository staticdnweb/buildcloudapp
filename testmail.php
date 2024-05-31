<?php
include __DIR__.'/utils.php';

$token = isset($argv[1])? $argv[1]:'';
$email = isset($argv[2])? $argv[2]:'';
$telegram_token = isset($argv[3])? $argv[3]:'';
$telegram_chatid = isset($argv[4])? $argv[4]:'';

if($token) {
	$gm = new Gmail($token);
	$all = array_values($gm->list_mails_googleplay());
	$chk = array_chunk($all,5);
	printf("\tFound %s mails\n", count($all));
	foreach($chk as $ids) {
		$rows =[];
		#$exist = mail_exists($ids);
		#$_ids = array_diff($ids, $exist);
		foreach($ids as $id) {
			$body = $gm->readMail($id);
			if($body) telegram("Found new mail of {$email}: ".$body, $telegram_token, $telegram_chatid);
		}
	}
	
}
else {
	telegram("Empty token to check mail of {$email}.", $telegram_token, $telegram_chatid);
}