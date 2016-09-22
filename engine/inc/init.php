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
 Файл: init.php (decoded and nulled by MadMan)
-----------------------------------------------------
 Назначение: Инициализация
=====================================================
*/

require_once (ENGINE_DIR . '/inc/functions.inc.php');

function check_login($username, $md5_password, $post = true) {
	global $member_id, $db, $user_group, $lang;
	
	if( $username == "" or preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $username ) or $md5_password == "" ) return false;
	
	$result = false;
	
	if( $post ) {
		
		$username = $db->safesql( $username );
		$md5_password = md5( $md5_password );
		
		$member_id = $db->super_query( "SELECT * FROM " . USERPREFIX . "_users WHERE name='$username' and password='$md5_password'" );
		
		if( $member_id['user_id'] and $user_group[$member_id['user_group']]['allow_admin'] and $member_id['banned'] != 'yes' ) $result = TRUE;
		else $member_id = array ();
	
	} else {
		
		$username = intval( $username );
		$md5_password = md5( $md5_password );
		
		$member_id = $db->super_query( "SELECT * FROM " . USERPREFIX . "_users WHERE user_id='$username'" );
		
		if( $member_id['password'] == $md5_password and $user_group[$member_id['user_group']]['allow_admin'] and $member_id['banned'] != 'yes' ) $result = TRUE;
		else $member_id = array ();
	
	}
	
	if( $result ) {
		
		if( !allowed_ip( $row['allowed_ip'] ) ) {
			
			$member_id = array ();
			$result = false;
			set_cookie( "dle_user_id", "", 0 );
			set_cookie( "dle_name", "", 0 );
			set_cookie( "dle_password", "", 0 );
			set_cookie( "dle_hash", "", 0 );
			@session_destroy();
			@session_unset();
			set_cookie( session_name(), "", 0 );
			
			msg( "info", $lang['index_msge'], $lang['ip_block'] );
		
		}
	}
	
	return $result;
}

extract( $_REQUEST, EXTR_SKIP );
require_once (ENGINE_DIR . '/data/config.php');

if( $config['http_home_url'] == "" ) {
	
	$config['http_home_url'] = explode( $config['admin_path'], $_SERVER['PHP_SELF'] );
	$config['http_home_url'] = reset( $config['http_home_url'] );
	$config['http_home_url'] = "http://" . $_SERVER['HTTP_HOST'] . $config['http_home_url'];
	$auto_detect_config = true;

}

require_once (ENGINE_DIR . '/classes/mysql.php');
require_once (ENGINE_DIR . '/data/dbconfig.php');
require_once (ROOT_DIR . '/language/' . $config['langs'] . '/adminpanel.lng');

$config['charset'] = ($lang['charset'] != '') ? $lang['charset'] : $config['charset'];

check_xss();

$Timer = new microTimer( );
$Timer->start();
if( $_SESSION['dle_log'] > 5 ) die( "Hacking attempt!" );

$is_loged_in = FALSE;
$member_id = array ();
$result = "";
$username = "";
$cmd5_password = "";
$allow_login = false;

$PHP_SELF = $_SERVER['PHP_SELF'];
$_IP = $db->safesql( $_SERVER['REMOTE_ADDR'] );

require_once (ENGINE_DIR . '/skins/default.skin.php');

if( isset( $_POST['action'] ) ) $action = $_POST['action']; else $action = $_GET['action'];
if( isset( $_POST['mod'] ) ) $mod = $_POST['mod']; else $mod = $_GET['mod'];

//################# Определение групп пользователей
$user_group = get_vars( "usergroup" );

if( ! $user_group ) {
	$user_group = array ();
	
	$db->query( "SELECT * FROM " . USERPREFIX . "_usergroups ORDER BY id ASC" );
	
	while ( $row = $db->get_row() ) {
		
		$user_group[$row['id']] = array ();
		
		foreach ( $row as $key => $value ) {
			$user_group[$row['id']][$key] = $value;
		}
	
	}
	set_vars( "usergroup", $user_group );
	$db->free();
}
//#################


//################# Определение категорий
$cat_info = get_vars( "category" );

if( ! is_array( $cat_info ) ) {
	$cat_info = array ();
	
	$db->query( "SELECT * FROM " . PREFIX . "_category ORDER BY posi ASC" );
	while ( $row = $db->get_row() ) {
		
		$cat_info[$row['id']] = array ();
		
		foreach ( $row as $key => $value ) {
			$cat_info[$row['id']][$key] = stripslashes( $value );
		}
	
	}
	set_vars( "category", $cat_info );
	$db->free();
}

if( count( $cat_info ) ) {
	foreach ( $cat_info as $key ) {
		$cat[$key['id']] = $key['name'];
		$cat_parentid[$key['id']] = $key['parentid'];
	}
}

if( $_REQUEST['action'] == "logout" ) {
	
	set_cookie( "dle_user_id", "", 0 );
	set_cookie( "dle_name", "", 0 );
	set_cookie( "dle_password", "", 0 );
	set_cookie( "dle_skin", "", 0 );
	set_cookie( "dle_newpm", "", 0 );
	set_cookie( "dle_hash", "", 0 );
	set_cookie( session_name(), "", 0 );
	
	@session_unset();
	@session_destroy();
	
	if( $config['extra_login'] ) auth();
	
	msg( "info", $lang['index_msge'], $lang['index_exit'] );
}

if( $check_referer ) {
	
	if( $_SERVER['HTTP_REFERER'] == '' and $_REQUEST['subaction'] != 'dologin' ) $allow_login = true;
	elseif( clean_url( $_SERVER['HTTP_REFERER'] ) == clean_url( $_SERVER['HTTP_HOST'] ) ) $allow_login = true;

} else {
	
	$allow_login = true;

}

if( $allow_login ) {
	
	if( $config['extra_login'] ) {
		
		if( ! isset( $_SERVER['PHP_AUTH_USER'] ) || ! isset( $_SERVER['PHP_AUTH_PW'] ) ) auth();
		$username = $_SERVER['PHP_AUTH_USER'];
		$cmd5_password = md5( $_SERVER['PHP_AUTH_PW'] );
		$post = true;
	
	} elseif( intval( $_SESSION['dle_user_id'] ) > 0 ) {
		
		$username = $_SESSION['dle_user_id'];
		$cmd5_password = $_SESSION['dle_password'];
		$post = false;
	
	} elseif( intval( $_COOKIE['dle_user_id'] ) > 0 ) {
		
		$username = $_COOKIE['dle_user_id'];
		$cmd5_password = $_COOKIE['dle_password'];
		$post = false;
	
	}
	
	if( $_REQUEST['subaction'] == 'dologin' ) {
		
		$username = $_POST['username'];
		$cmd5_password = md5( $_POST['password'] );
		$post = true;
	
	}

}

if( check_login( $username, $cmd5_password, $post ) ) {
	$is_loged_in = true;
	$_SESSION['dle_log'] = 0;
	$dle_login_hash = md5( strtolower( $_SERVER['HTTP_HOST'] . $member_id['name'] . $cmd5_password . $config['key'] . date( "Ymd" ) ) );
	
	if( ! $_SESSION['dle_user_id'] and $_COOKIE['dle_user_id'] ) {
		
		$_SESSION['dle_user_id'] = $_COOKIE['dle_user_id'];
		$_SESSION['dle_password'] = $_COOKIE['dle_password'];
	}

} else {
	
	$_SESSION['dle_log'] = intval( $_SESSION['dle_log'] ) + 1;
	$dle_login_hash = "";
	
	if( $_REQUEST['subaction'] == 'dologin' ) {
		
		$result = "<font color=red>" . $lang['index_errpass'] . "</font>";
	
	} else
		$result = "";
	
	if( $config['extra_login'] ) auth();
	
	$is_loged_in = false;
}

if( $is_loged_in and ! $_SESSION['dle_xtra'] and $config['extra_login'] ) {
	$_SESSION['dle_xtra'] = true;
	$_REQUEST['subaction'] = 'dologin';
}

###########################
if( $is_loged_in and $_REQUEST['subaction'] == 'dologin' ) {
	
	$_SESSION['dle_user_id'] = $member_id['user_id'];
	$_SESSION['dle_password'] = $cmd5_password;
	
	set_cookie( "dle_user_id", $member_id['user_id'], 365 );
	set_cookie( "dle_password", $cmd5_password, 365 );
	
	$time_now = time() + ($config['date_adjust'] * 60);
	
	if( $config['log_hash'] ) {
		
		$salt = "abchefghjkmnpqrstuvwxyz0123456789";
		$hash = '';
		srand( ( double ) microtime() * 1000000 );
		
		for($i = 0; $i < 9; $i ++) {
			$hash .= $salt{rand( 0, 33 )};
		}
		
		$hash = md5( $hash );
		
		set_cookie( "dle_hash", $hash, 365 );
		
		$_COOKIE['dle_hash'] = $hash;
		$member_id['hash'] = $hash;
		
		$db->query( "UPDATE " . USERPREFIX . "_users set hash='" . $hash . "', lastdate='{$time_now}', logged_ip='" . $_IP . "' WHERE user_id='{$member_id['user_id']}'" );
	
	} else
		$db->query( "UPDATE " . USERPREFIX . "_users set lastdate='{$time_now}', logged_ip='" . $_IP . "' WHERE user_id='{$member_id['user_id']}'" );

}

if( $is_loged_in and $config['log_hash'] and (($_COOKIE['dle_hash'] != $member_id['hash']) or ($member_id['hash'] == "")) ) {
	
	$is_loged_in = FALSE;
}

if( $is_loged_in and $config['ip_control'] == '1' and ! check_netz( $member_id['logged_ip'], $_IP ) and $_REQUEST['subaction'] != 'dologin' ) $is_loged_in = FALSE;

if( ! $is_loged_in ) {
	
	$member_id = array();
	set_cookie( "dle_user_id", "", 0 );
	set_cookie( "dle_name", "", 0 );
	set_cookie( "dle_password", "", 0 );
	set_cookie( "dle_hash", "", 0 );
	$_SESSION['dle_user_id'] = 0;
	$_SESSION['dle_password'] = "";
	
	if( $config['extra_login'] ) auth();
}

header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT" );
header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Cache-Control: post-check=0, pre-check=0", false );
header( "Pragma: no-cache" );
?>