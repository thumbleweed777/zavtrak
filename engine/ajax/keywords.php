<?php
/*
=====================================================
 DataLife Engine Nulled by M.I.D-Team
-----------------------------------------------------
 http://www.mid-team.ws/
-----------------------------------------------------
 Copyright (c) 2004,2009 SoftNews Media Group
=====================================================
 Данный код защищен авторскими правами
=====================================================
 Файл: keywords.php
-----------------------------------------------------
 Назначение: Генерация ключевых слов
=====================================================
*/
@session_start();
@error_reporting(7);
@ini_set('display_errors', true);
@ini_set('html_errors', false);

define('DATALIFEENGINE', true);
define('ROOT_DIR', '../..');
define('ENGINE_DIR', '..');

include ENGINE_DIR.'/data/config.php';

if ($config['http_home_url'] == "") {

	$config['http_home_url'] = explode("engine/ajax/keywords.php", $_SERVER['PHP_SELF']);
	$config['http_home_url'] = reset($config['http_home_url']);
	$config['http_home_url'] = "http://".$_SERVER['HTTP_HOST'].$config['http_home_url'];

}

require_once ENGINE_DIR.'/classes/mysql.php';
require_once ENGINE_DIR.'/data/dbconfig.php';
require_once ROOT_DIR.'/language/'.$config['langs'].'/adminpanel.lng';

$config['charset'] = ($lang['charset'] != '') ? $lang['charset'] : $config['charset'];

require_once ENGINE_DIR.'/inc/functions.inc.php';
require_once ENGINE_DIR.'/classes/parse.class.php';

$parse = new ParseFilter();
$full_story = $parse->BB_Parse($parse->process(convert_unicode($_REQUEST['full_txt'], $config['charset'])), false);
$short_story = $parse->BB_Parse($parse->process(convert_unicode($_REQUEST['short_txt'], $config['charset'])), false);

$metatags = create_metatags ($short_story.$full_story);

$metatags['description'] = trim($metatags['description']);
$metatags['keywords'] = trim($metatags['keywords']);

@header("Content-type: text/css; charset=".$config['charset']);

if ($_REQUEST['key'] == 1) echo $metatags['description'];
else echo $metatags['keywords'];

?>