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
 Файл: admin.php
-----------------------------------------------------
 Назначение: админпанель
=====================================================
*/
@session_start ();
@ob_start ();
@ob_implicit_flush ( 0 );

error_reporting ( E_ALL ^ E_NOTICE );
@ini_set ( 'display_errors', true );
@ini_set ( 'html_errors', false );
@ini_set ( 'error_reporting', E_ALL ^ E_NOTICE );

define ( 'DATALIFEENGINE', true );
define ( 'ROOT_DIR', dirname ( __FILE__ ) );
define ( 'ENGINE_DIR', ROOT_DIR . '/engine' );

//#################
$check_referer = true;
//#################

require_once (ENGINE_DIR . '/inc/init.php');

if ($is_loged_in == FALSE) {
	
	echoheader ( "home", "Login" );
	
	echo <<<HTML
<form  name="login" action="" method="post"><input type="hidden" name="subaction" value="dologin">
<div style="padding-top:5px;">
<table width="100%">
    <tr>
        <td width="4"><img src="engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="engine/skins/images/tl_oo.gif"><img src="engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src="engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="engine/skins/images/tl_lb.gif"><img src="engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['m_login']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td width="55" style="padding:5px;" rowspan="2"><img src="engine/skins/images/key.png" border="0"></td>
        <td width="70" style="padding:5px;">{$lang['user_name']}</td>
        <td><input class="edit" type="text" name="username" value='' size="20">&nbsp;&nbsp;{$result}</td>
    </tr>
    <tr>
        <td style="padding:5px;">{$lang['user_pass']}</td>
        <td><input class="edit" type="password" name="password" size="20">&nbsp;&nbsp;<input type="submit" class="edit" value="{$lang['b_login']}"></td>
    </tr>
</table>
<div class="hr_line"></div>
<div class="navigation">{$lang['index_inf']}</div>
</td>
        <td background="engine/skins/images/tl_rb.gif"><img src="engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src="engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="engine/skins/images/tl_ub.gif"><img src="engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
        <td><img src="engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</table>
</div></form>
HTML;
	
	echofooter ();
	exit ();
} elseif ($is_loged_in == TRUE) {
	
	// ********************************************************************************
	// Include System Module
	// ********************************************************************************
	
if (is_array($mod)) die ( "Hacking attempt!" );

	$system_modules = array (
							'addnews' => 'user', 
							'editnews' => 'user', 
							'comments' => 'user', 
							'main' => 'user', 
							'options' => 'user', 
							'files' => 'user', 
							'editusers' => 'admin', 
							'preview' => 'user', 
							'categories' => 'admin', 
							'massactions' => 'user', 
							'help' => 'admin', 
							'wordfilter' => 'user', 
							'xfields' => 'admin', 
							'dboption' => 'admin', 
							'email' => 'admin', 
							'static' => 'admin', 
							'editvote' => 'admin', 
							'addvote' => 'admin', 
							'templates' => 'admin', 
							'newsletter' => 'admin', 
							'blockip' => 'admin', 
							'usergroup' => 'admin', 
							'dumper' => 'admin', 
							'userfields' => 'admin', 
							'banners' => 'admin', 

'taginator' => 'admin',
'videonator' => 'admin',
'edator2' => 'admin',
'edator3' => 'admin',
'referer' => 'admin',
'receptonator' => 'admin',
'rotator' => 'admin',
        'komponator' => 'admin',
		        'imageloader' => 'admin',
'static' => 'user', 
'taginator' => 'user',

							'clean' => 'admin', 
							'rss' => 'admin', 
							'iptools' => 'admin', 
							'search' => 'admin', 
							'rssinform' => 'admin', 
							'cmoderation' => 'user', 
							'googlemap' => 'admin' );
	
	if ($mod == "") {
		include (ENGINE_DIR . '/inc/main.php');
	} elseif ($system_modules[$mod]) {
		
		if ($system_modules[$mod] == "user") {
			include (ENGINE_DIR . '/inc/' . $mod . '.php');
		} elseif ($system_modules[$mod] == "admin" and $member_id['user_group'] == 1) {
			include (ENGINE_DIR . '/inc/' . $mod . '.php');
		} elseif ($system_modules[$mod] == "admin" and $member_id['user_group'] != 1) {
			msg ( "error", $lang['index_denied'], $lang['index_denied'] );
			exit ();
		} else {
			msg ( "error", $lang['index_denied'], $lang['index_denied'] );
			exit ();
		}
	} else {
		msg ( "error", $lang['index_denied'], $lang['index_denied'] );
		exit ();
	}
}

$db->close ();

GzipOut ();
?>