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
 Файл: lostpassword.php
-----------------------------------------------------
 Назначение: Восстановление забытого пароля
=====================================================
*/
if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

if( $is_logged ) {
	
	msgbox( $lang['all_info'], $lang['user_logged'] );

} elseif( intval( $_GET['douser'] ) ) {
	
	$douser = intval( $_GET['douser'] );
	$lostid = $db->safesql( $_GET['lostid'] );
	
	$dupe_lost = $db->query( "SELECT * from " . USERPREFIX . "_lostdb WHERE lostname='$douser' AND lostid='$lostid' LIMIT 0,1" );
	
	if( $db->num_rows( $dupe_lost ) > 0 ) {
		
		$row = $db->super_query( "SELECT name FROM " . USERPREFIX . "_users WHERE user_id='$douser' LIMIT 0,1" );
		
		$username = $row['name'];
		
		$salt = "abchefghjkmnpqrstuvwxyz0123456789";
		srand( ( double ) microtime() * 1000000 );
		
		for($i = 0; $i < 9; $i ++) {
			$new_pass .= $salt{rand( 0, 33 )};
		}
		
		$db->query( "UPDATE " . USERPREFIX . "_users set password='" . md5( md5( $new_pass ) ) . "', allowed_ip = '' WHERE user_id='$douser'" );
		$db->query( "DELETE FROM " . USERPREFIX . "_lostdb WHERE lostname='$douser'" );
		
		msgbox( $lang['lost_gen'], "$lang[lost_npass]<br /><br />$lang[lost_login]&nbsp;&nbsp;<b>$username</b><br />$lang[lost_pass] <b>$new_pass</b><br /><br />$lang[lost_info]" );
	
	} else {
		msgbox( $lang['all_err_1'], $lang['lost_err'] );
	}
	
	$db->free( $dupe_lost );

} elseif( isset( $_POST['submit_lost'] ) ) {
	
	if( $_POST['sec_code'] != $_SESSION['sec_code_session'] or ! $_SESSION['sec_code_session'] ) {
		
		msgbox( $lang['all_err_1'], $lang['reg_err_19'] . "<br /><br /><a href=\"javascript:history.go(-1)\">$lang[all_prev]</a>" );
	
	} else {
		
		$_SESSION['sec_code_session'] = false;
		$lostname = $db->safesql( $_POST['lostname'] );
		
# php 5.2
# 		if( ereg( '^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+' . '@' . '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.' . '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$', $lostname ) ) $search = "email = '" . $lostname . "'";
		
		if( preg_match( '/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`a-z{|}~]+' . '@' . '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.' . '[-!#$%&\'*+\\.\/0-9=?A-Z^_`a-z{|}~]+$/', $lostname ) ) $search = "email = '" . $lostname . "'";
		else $search = "name = '" . $lostname . "'";
		
		$db->query( "SELECT user_id, email, name FROM " . USERPREFIX . "_users where {$search} LIMIT 0,1" );
		
		if( $db->num_rows() > 0 ) {
			
			include_once ENGINE_DIR . '/classes/mail.class.php';
			$mail = new dle_mail( $config );
			
			$row = $db->get_row();
			$db->free();
			
			$lostmail = $row['email'];
			$userid = $row['user_id'];
			$lostname = $row['name'];
			
			$row = $db->super_query( "SELECT template FROM " . PREFIX . "_email where name='lost_mail' LIMIT 0,1" );
			
			$row['template'] = stripslashes( $row['template'] );
			
			$salt = "abchefghjkmnpqrstuvwxyz0123456789";
			srand( ( double ) microtime() * 1000000 );
			
			for($i = 0; $i < 9; $i ++) {
				$rand_lost .= $salt{rand( 0, 33 )};
			}
			
			$lostid = md5( $lostname . $lostmail . time() . $rand_lost );
			$lostlink = $config['http_home_url'] . "index.php?do=lostpassword&douser=" . $userid . "&lostid=" . $lostid;
			
			$db->query( "DELETE FROM " . USERPREFIX . "_lostdb WHERE lostname='$userid'" );
			
			$db->query( "INSERT INTO " . USERPREFIX . "_lostdb (lostname, lostid) values ('$userid', '$lostid')" );
			
			$row['template'] = str_replace( "{%username%}", $lostname, $row['template'] );
			$row['template'] = str_replace( "{%lostlink%}", $lostlink, $row['template'] );
			$row['template'] = str_replace( "{%ip%}", $_SERVER['REMOTE_ADDR'], $row['template'] );
			
			$mail->send( $lostmail, $lang['lost_subj'], $row['template'] );
			
			if( $mail->send_error ) msgbox( $lang['all_info'], $mail->smtp_msg );
			else msgbox( $lang['lost_ms'], $lang['lost_ms_1'] );
		
		} else {
			msgbox( $lang['all_err_1'], $lang['lost_err_1'] );
		}
	}

} else {
	$tpl->load_template( 'lostpassword.tpl' );
	
	$tpl->set( '{code}', "<span id=\"dle-captcha\"><img src=\"" . $path['path'] . "engine/modules/antibot.php\" alt=\"{$lang['sec_image']}\" border=\"0\" /><br /><a onclick=\"reload(); return false;\" href=\"#\">{$lang['reload_code']}</a></span>" );
	
	$tpl->copy_template = "<form  method=\"post\" name=\"registration\" action=\"?do=lostpassword\">\n" . $tpl->copy_template . "
<input name=\"submit_lost\" type=\"hidden\" id=\"submit_lost\" value=\"submit_lost\">
</form>";
	
	$tpl->copy_template .= <<<HTML
<script language='JavaScript' type="text/javascript">
function reload () {

	var rndval = new Date().getTime(); 

	document.getElementById('dle-captcha').innerHTML = '<img src="{$path['path']}engine/modules/antibot.php?rndval=' + rndval + '" border="0" width="120" height="50" alt="" /><br /><a onclick="reload(); return false;" href="#">{$lang['reload_code']}</a>';

};
</script>
HTML;
	
	$tpl->compile( 'content' );
	
	$tpl->clear();
}
?>