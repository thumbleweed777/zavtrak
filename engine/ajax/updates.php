<?php
/*
=====================================================
 DataLife Engine Nulled by M.I.D-Team
-----------------------------------------------------
 http://www.mid-team.ws/
-----------------------------------------------------
 Copyright (c) 2004,2009 SoftNews Media Group
=====================================================
 ������ ��� ������� ���������� �������
=====================================================
 ����: updates.php
-----------------------------------------------------
 ����������: �������� �� ������� ����� ������
=====================================================
*/

@error_reporting(7);
@ini_set('display_errors', true);
@ini_set('html_errors', false);

define('DATALIFEENGINE', true);
define('ROOT_DIR', '../..');
define('ENGINE_DIR', '..');

include ENGINE_DIR.'/data/config.php';

if ($config['http_home_url'] == "") {

	$config['http_home_url'] = explode("engine/ajax/updates.php", $_SERVER['PHP_SELF']);
	$config['http_home_url'] = reset($config['http_home_url']);
	$config['http_home_url'] = "http://".$_SERVER['HTTP_HOST'].$config['http_home_url'];

}

require_once ROOT_DIR.'/language/'.$config['langs'].'/adminpanel.lng';

$config['charset'] = ($lang['charset'] != '') ? $lang['charset'] : $config['charset'];

require_once ENGINE_DIR.'/inc/functions.inc.php';


@header("HTTP/1.0 200 OK");
@header("HTTP/1.1 200 OK");
@header("Cache-Control: no-cache, must-revalidate, max-age=0");
@header("Expires: 0");
@header("Pragma: no-cache");
@header("Content-type: text/css; charset=".$config['charset']);

$data = "��������, �� � ����� ������������ ��� ������� ���� ���������! ��� ������������� ������ ������ ��� ������� ������ ;-)";

echo $data;
?>