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
 Файл: feedback.php
-----------------------------------------------------
 Назначение: обратная связь
=====================================================
*/

if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

if( ! $user_group[$member_id['user_group']]['allow_feed'] ) {
	
	$lang['feed_error'] = str_replace( '{group}', $user_group[$member_id['user_group']]['group_name'], $lang['feed_error'] );
	msgbox( $lang['all_info'], $lang['feed_error'] );

} else {
	
	if( isset( $_POST['send'] ) ) {
		$stop = "";
		
		if( $is_logged ) {
			$name = $member_id['name'];
			$email = $member_id['email'];
		} else {
			
			$name = $db->safesql( strip_tags( $_POST['name'] ) );
			$email = $db->safesql( strip_tags( $_POST['email'] ) );
			
			$db->query( "SELECT name from " . USERPREFIX . "_users where LOWER(name) = '" . strtolower( $name ) . "' OR LOWER(email) = '" . strtolower( $email ) . "'" );
			
			if( $db->num_rows() > 0 ) {
				$stop = $lang['news_err_7'];
			}
			
			$name = strip_tags( stripslashes( $_POST['name'] ) );
			$email = strip_tags( stripslashes( $_POST['email'] ) );
		
		}
		
		$subject = strip_tags( stripslashes( $_POST['subject'] ) );
		$message = strip_tags( stripslashes( $_POST['message'] ) );
		$recip = intval( $_POST['recip'] );
		
		function check_email($value) {
# php 5.2
# 			return eregi( "^([0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-wyz][a-z](fo|g|l|m|mes|o|op|pa|ro|seum|t|u|v|z)?)$", $value );

		return preg_match( "/^([0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-wyz][a-z](fo|g|l|m|mes|o|op|pa|ro|seum|t|u|v|z)?)$/i", $value );
		}
		
		if( empty( $name ) ) {
			$stop .= $lang['feed_err_1'];
		}
		
		if( empty( $email ) ) {
			$stop .= $lang['feed_err_2'];
		} elseif( ! check_email( $email ) ) {
			$stop .= $lang['feed_err_3'];
		}
		
		if( empty( $subject ) ) {
			$stop .= $lang['feed_err_4'];
		}
		
		if( empty( $message ) ) {
			$stop .= $lang['feed_err_5'];
		}
		
		if( $_POST['sec_code'] != $_SESSION['sec_code_session'] or ! $_SESSION['sec_code_session'] ) {
			$stop .= $lang['reg_err_19'];
		}
		$_SESSION['sec_code_session'] = false;
		
		if( $stop ) {
			
			msgbox( $lang['all_err_1'], "$stop<br><br><a href=\"javascript:history.go(-1)\">$lang[all_prev]</a>" );
		
		} else {
			
			include_once ENGINE_DIR . '/classes/mail.class.php';
			$mail = new dle_mail( $config );
			
			$recipient = $db->super_query( "SELECT name, email, fullname FROM " . USERPREFIX . "_users WHERE user_id='" . $recip . "'" );
			
			if( empty( $recipient['fullname'] ) ) $recipient['fullname'] = $recipient['name'];
			
			$row = $db->super_query( "SELECT template FROM " . PREFIX . "_email where name='feed_mail' LIMIT 0,1" );
			
			$row['template'] = stripslashes( $row['template'] );
			$row['template'] = str_replace( "{%username_to%}", $recipient['fullname'], $row['template'] );
			$row['template'] = str_replace( "{%username_from%}", $name, $row['template'] );
			$row['template'] = str_replace( "{%text%}", $message, $row['template'] );
			$row['template'] = str_replace( "{%ip%}", $_SERVER['REMOTE_ADDR'], $row['template'] );
			
			$mail->from = $email;
			
			$mail->send( $recipient['email'], $subject, $row['template'] );
			
			if( $mail->send_error ) msgbox( $lang['all_info'], $mail->smtp_msg );
			else msgbox( $lang['feed_ok_1'], "$lang[feed_ok_2] " . $recipient['name'] . " $lang[feed_ok_3] <a href=\"{$config['http_home_url']}\">$lang[feed_ok_4]</a>" );
		
		}
	
	} else {
		
		$user = intval( $_GET['user'] );
		
		if( ! $user ) $db->query( "SELECT name, user_group, user_id FROM " . USERPREFIX . "_users where user_group < '4' AND allow_mail = '1' ORDER BY user_group" );
		else $db->query( "SELECT name, user_group, user_id FROM " . USERPREFIX . "_users where user_id = '$user' AND allow_mail = '1' ORDER BY user_group" );
		
		if( $db->num_rows() ) {
			$empf = "<select name=\"recip\">";
			$i = 1;
			while ( $row = $db->get_array() ) {
				$str = $row['name'] . " (" . stripslashes( $user_group[$row['user_group']]['group_name'] ) . ")";
				
				if( $i == 1 ) {
					$empf .= "<option selected=\"selected\" value=\"" . $row["user_id"] . "\">" . $str . "</option>\n";
				} else {
					$empf .= "<option value=\"" . $row["user_id"] . "\">" . $str . "</option>\n";
				}
				$i ++;
			}
			$empf .= "</select>";
			
			$db->free();
			
			$tpl->load_template( 'feedback.tpl' );
			
			$path = parse_url( $config['http_home_url'] );
			$tpl->set( '{recipient}', $empf );
			$tpl->set( '{code}', "<span id=\"dle-captcha\"><img src=\"" . $path['path'] . "engine/modules/antibot.php\" alt=\"{$lang['sec_image']}\" border=\"0\" /><br /><a onclick=\"reload(); return false;\" href=\"#\">{$lang['reload_code']}</a></span>" );
			
			if( ! $is_logged ) {
				$tpl->set( '[not-logged]', "" );
				$tpl->set( '[/not-logged]', "" );
			} else
				$tpl->set_block( "'\\[not-logged\\](.*?)\\[/not-logged\\]'si", "" );
			
			$tpl->copy_template = "<form  method=\"post\" name=\"sendmail\" onsubmit=\"if(document.sendmail.subject.value == '' || document.sendmail.message.value == ''){alert('{$lang['comm_req_f']}');return false}\"action=\"\">\n" . $tpl->copy_template . "
<input name=\"send\" type=\"hidden\" value=\"send\" />
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
		
		} else {
			msgbox( $lang['all_err_1'], $lang['feed_err_7'] );
		}
	}

}
?>