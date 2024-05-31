<?php
$task = isset($argv[1])? $argv[1]:'';

include __DIR__.'/util2.php';

if($task=='edit') {
	create_keystorefile();
	edit_build_gradle();
}
