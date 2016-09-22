<?php

@session_start();
@error_reporting(7);
@ini_set('display_errors', true);
@ini_set('html_errors', false);

define('DATALIFEENGINE', true);
define('ROOT_DIR', '../..');
define('ENGINE_DIR', '..');

include ENGINE_DIR.'/data/config.php';

if ($config['http_home_url'] == "") {

	$config['http_home_url'] = explode("engine/ajax/referer.del.php", $_SERVER['PHP_SELF']);
	$config['http_home_url'] = reset($config['http_home_url']);
	$config['http_home_url'] = "http://".$_SERVER['HTTP_HOST'].$config['http_home_url'];

}

require_once ENGINE_DIR.'/classes/mysql.php';
require_once ENGINE_DIR.'/data/dbconfig.php';

require_once ROOT_DIR.'/language/'.$config['langs'].'/referer.lng';

$config['charset'] = ($lang['charset'] != '') ? $lang['charset'] : $config['charset'];

require_once ENGINE_DIR.'/inc/functions.inc.php';
require_once ENGINE_DIR.'/modules/sitelogin.php';


@header("HTTP/1.0 200 OK");
@header("HTTP/1.1 200 OK");
@header("Cache-Control: no-cache, must-revalidate, max-age=0");
@header("Expires: 0");
@header("Pragma: no-cache");
@header("Content-type: text/css; charset=".$config['charset']);

$update = @file_get_contents("http://www.getdle.com/extras/referer/version.txt"); // редактирования данной строки запрещено!

if($update > $langms['version']) $info = $langms['ref_upd_01'];
elseif($update == $langms['version']) $info = $langms['ref_upd_02'];
elseif($update == "") { $update = "--"; $info = $langms['ref_upd_03']; }
elseif($update < $langms['version']) $info = $langms['ref_upd_04'];

die("<div style=\"background: lightyellow;border:1px dotted rgb(190,190,190);padding: 5px;margin-top: 7px;margin-right: 10px;\">Последняя версия: ".$update." (".$info.")</div>");

?>