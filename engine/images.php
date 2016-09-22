<?PHP
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
 Файл: images.php
-----------------------------------------------------
 Назначение: управление загруженными картинками
=====================================================
*/
@session_start ();
@error_reporting ( E_ALL ^ E_NOTICE );
@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', false );
@ini_set ( 'error_reporting', E_ALL ^ E_NOTICE );

define ( 'DATALIFEENGINE', true );
define ( 'ENGINE_DIR', dirname ( __FILE__ ) );
define ( 'ROOT_DIR', str_replace ( DIRECTORY_SEPARATOR . "engine", "", ENGINE_DIR ) );
define ( 'FOLDER_PREFIX', date ( "Y-m" ) );

extract ( $_REQUEST, EXTR_SKIP );
require ENGINE_DIR . "/data/config.php";

if ($config['http_home_url'] == "") {
	
	$config['http_home_url'] = explode ( "engine/images.php", $_SERVER['PHP_SELF'] );
	$config['http_home_url'] = reset ( $config['http_home_url'] );
	$config['http_home_url'] = "http://" . $_SERVER['HTTP_HOST'] . $config['http_home_url'];

}

require_once ENGINE_DIR . '/classes/mysql.php';
require_once ENGINE_DIR . "/data/dbconfig.php";
require_once ENGINE_DIR . "/inc/functions.inc.php";

check_xss ();

if ($_COOKIE['dle_skin']) {
	if (@is_dir ( ROOT_DIR . '/templates/' . $_COOKIE['dle_skin'] )) {
		$config['skin'] = $_COOKIE['dle_skin'];
	}
}

if ($config["lang_" . $config['skin']]) {
	
	include_once ROOT_DIR . '/language/' . $config["lang_" . $config['skin']] . '/adminpanel.lng';

} else {
	
	include_once ROOT_DIR . '/language/' . $config['langs'] . '/adminpanel.lng';

}

$config['charset'] = ($lang['charset'] != '') ? $lang['charset'] : $config['charset'];

//################# Определение групп пользователей
$user_group = get_vars ( "usergroup" );

if (! $user_group) {
	$user_group = array ();
	
	$db->query ( "SELECT * FROM " . USERPREFIX . "_usergroups ORDER BY id ASC" );
	
	while ( $row = $db->get_row () ) {
		
		$user_group[$row['id']] = array ();
		
		foreach ( $row as $key => $value ) {
			$user_group[$row['id']][$key] = $value;
		}
	
	}
	set_vars ( "usergroup", $user_group );
	$db->free ();
}

include_once ENGINE_DIR . '/modules/sitelogin.php';

if (! $is_logged) {
	
	die ( "<br><br><br><br><center>$lang[err_notlogged]</center>" );

}

if (! $user_group[$member_id['user_group']]['allow_image_upload'] and $member_id['user_group'] != 1) {
	
	die ( "<br><br><br><br><center>$lang[err_noupload]</center>" );

}

$_REQUEST['news_id'] = (intval ( $_REQUEST['add_id'] )) ? intval ( $_REQUEST['add_id'] ) : '0';
$_REQUEST['action'] = "quick";
$_REQUEST['author'] = $member_id['name'];

$action = "quick";
$author = $member_id['name'];

require_once ENGINE_DIR . '/inc/files.php';
?>