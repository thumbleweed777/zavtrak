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
 Файл: register.php
-----------------------------------------------------
 Назначение: регистрация посетителя
=====================================================
*/

if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

require_once ENGINE_DIR . '/classes/parse.class.php';

$parse = new ParseFilter( );
$parse->safe_mode = true;
$parse->allow_url = false;
$parse->allow_image = false;
$stopregistration = FALSE;

if( isset( $_REQUEST['doaction'] ) ) $doaction = $_REQUEST['doaction']; else $doaction = "";
$config['reg_group'] = intval( $config['reg_group'] ) ? intval( $config['reg_group'] ) : 4;

function check_reg($name, $email, $password1, $password2, $sec_code = 1, $sec_code_session = 1) {
	global $lang, $db, $banned_info;
	$stop = "";
	
	if( $sec_code != $sec_code_session or ! $sec_code_session ) $stop .= $lang['reg_err_19'];
	if( $password1 != $password2 ) $stop .= $lang['reg_err_1'];
	if( strlen( $password1 ) < 6 ) $stop .= $lang['reg_err_2'];
	if( strlen( $name ) > 20 ) $stop .= $lang['reg_err_3'];
	if( preg_match( "/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/", $name ) ) $stop .= $lang['reg_err_4'];

# php 5.2
# 	if( (! ereg( '^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+' . '@' . '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.' . '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$', $email )) or (empty( $email )) ) $stop .= $lang['reg_err_6'];


	if( (! preg_match( '/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`a-z{|}~]+' . '@' . '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.' . '[-!#$%&\'*+\\.\/0-9=?A-Z^_`a-z{|}~]+$/', $email )) or (empty( $email )) ) $stop .= $lang['reg_err_6'];
	if( $name == "" ) $stop .= $lang['reg_err_7'];
	
	if( count( $banned_info['name'] ) ) foreach ( $banned_info['name'] as $banned ) {
		
		$banned['name'] = str_replace( '\*', '.*', preg_quote( $banned['name'], "#" ) );
		
		if( $banned['name'] and preg_match( "#^{$banned['name']}$#i", $name ) ) {
			
			if( $banned['descr'] ) {
				$lang['reg_err_21'] = str_replace( "{descr}", $lang['reg_err_22'], $lang['reg_err_21'] );
				$lang['reg_err_21'] = str_replace( "{descr}", $banned['descr'], $lang['reg_err_21'] );
			} else
				$lang['reg_err_21'] = str_replace( "{descr}", "", $lang['reg_err_21'] );
			
			$stop .= $lang['reg_err_21'];
		}
	}
	
	if( count( $banned_info['email'] ) ) foreach ( $banned_info['email'] as $banned ) {
		
		$banned['email'] = str_replace( '\*', '.*', preg_quote( $banned['email'], "#" ) );
		
		if( $banned['email'] and preg_match( "#^{$banned['email']}$#i", $email ) ) {
			
			if( $banned['descr'] ) {
				$lang['reg_err_23'] = str_replace( "{descr}", $lang['reg_err_22'], $lang['reg_err_23'] );
				$lang['reg_err_23'] = str_replace( "{descr}", $banned['descr'], $lang['reg_err_23'] );
			} else
				$lang['reg_err_23'] = str_replace( "{descr}", "", $lang['reg_err_23'] );
			
			$stop .= $lang['reg_err_23'];
		}
	}
	
	if( $stop == "" ) {
		$replace_word = array ('e' => '[eеё]', 'r' => '[rг]', 't' => '[tт]', 'y' => '[yу]', 'u' => '[uи]', 'i' => '[i1l!]', 'o' => '[oо0]', 'p' => '[pр]', 'a' => '[aа]', 's' => '[s5]', 'w' => 'w', 'q' => 'q', 'd' => 'd', 'f' => 'f', 'g' => '[gд]', 'h' => '[hн]', 'j' => 'j', 'k' => '[kк]', 'l' => '[l1i!]', 'z' => 'z', 'x' => '[xх%]', 'c' => '[cс]', 'v' => '[vuи]', 'b' => '[bвь]', 'n' => '[nпл]', 'm' => '[mм]', 'й' => '[йиu]', 'ц' => 'ц', 'у' => '[уy]', 'е' => '[еeё]', 'н' => '[нh]', 'г' => '[гr]', 'ш' => '[шwщ]', 'щ' => '[щwш]', 'з' => '[з3э]', 'х' => '[хx%]', 'ъ' => '[ъь]', 'ф' => 'ф', 'ы' => '(ы|ь[i1l!]?)', 'в' => '[вb]', 'а' => '[аa]', 'п' => '[пn]', 'р' => '[рp]', 'о' => '[оo0]', 'л' => '[лn]', 'д' => 'д', 'ж' => 'ж', 'э' => '[э3з]', 'я' => '[я]', 'ч' => '[ч4]', 'с' => '[сc]', 'м' => '[мm]', 'и' => '[иuй]', 'т' => '[тt]', 'ь' => '[ьb]', 'б' => '[б6]', 'ю' => '(ю|[!1il][oо0])', 'ё' => '[ёеe]', '1' => '[1il!]', '2' => '2', '3' => '[3зэ]', '4' => '[4ч]', '5' => '[5s]', '6' => '[6б]', '7' => '7', '8' => '8', '9' => '9', '0' => '[0оo]', '_' => '_', '#' => '#', '%' => '[%x]', '^' => '[^~]', '(' => '[(]', ')' => '[)]', '=' => '=', '.' => '[.]', '-' => '-' );
		$name = strtolower( $name );
		$search_name = strtr( $name, $replace_word );
		
		$row = $db->super_query( "SELECT COUNT(*) as count FROM " . USERPREFIX . "_users WHERE email = '$email' OR LOWER(name) REGEXP '[[:<:]]{$search_name}[[:>:]]' OR name = '$name'" );
		
		if( $row['count'] ) $stop .= $lang['reg_err_8'];
	}
	
	return $stop;

}

$row = $db->super_query( "SELECT COUNT(*) as count FROM " . USERPREFIX . "_users" );

if( $config['allow_registration'] != "yes" ) {
	
	msgbox( $lang['all_info'], $lang['reg_err_9'] );
	$stopregistration = TRUE;

} elseif( $config['max_users'] > 0 and $row['count'] > $config['max_users'] ) {
	
	msgbox( $lang['all_info'], $lang['reg_err_10'] );
	$stopregistration = TRUE;

}

if( isset( $_POST['submit_reg'] ) ) {
	
	if( $config['allow_sec_code'] == "yes" ) {
		$sec_code = $_POST['sec_code'];
		$sec_code_session = ($_SESSION['sec_code_session'] != '') ? $_SESSION['sec_code_session'] : false;
	} else {
		$sec_code = 1;
		$sec_code_session = 1;
	}
	
	$password1 = $_POST['password1'];
	$password2 = $_POST['password2'];
	$name = $db->safesql( $parse->process( htmlspecialchars( trim( $_POST['name'] ) ) ) );
	$email = $db->safesql( $parse->process( $_POST['email'] ) );
	
	$reg_error = check_reg( $name, $email, $password1, $password2, $sec_code, $sec_code_session );
	
	if( ! $reg_error ) {
		
		if( $config['registration_type'] ) {
			
			include_once ENGINE_DIR . '/classes/mail.class.php';
			$mail = new dle_mail( $config );
			
			$row = $db->super_query( "SELECT template FROM " . PREFIX . "_email where name='reg_mail' LIMIT 0,1" );
			
			$row['template'] = stripslashes( $row['template'] );
			
			$idlink = rawurlencode( base64_encode( $name . "||" . $email . "||" . md5( $password1 ) . "||" . md5( md5( $name . $email . DBHOST . DBNAME . $config['key'] ) ) ) );
			
			$row['template'] = str_replace( "{%username%}", $name, $row['template'] );
			$row['template'] = str_replace( "{%validationlink%}", $config['http_home_url'] . "index.php?do=register&doaction=validating&id=" . $idlink, $row['template'] );
			$row['template'] = str_replace( "{%password%}", $password1, $row['template'] );
			
			$mail->send( $email, $lang['reg_subj'], $row['template'] );
			
			if( $mail->send_error ) msgbox( $lang['all_info'], $mail->smtp_msg );
			else msgbox( $lang['reg_vhead'], $lang['reg_vtext'] );
			
			$_SESSION['sec_code_session'] = false;
			
			$stopregistration = TRUE;
		
		} else {
			
			$doaction = "validating";
			$_REQUEST['id'] = rawurlencode( base64_encode( $name . "||" . $email . "||" . md5( $password1 ) . "||" . md5( md5( $name . $email . DBHOST . DBNAME . $config['key'] ) ) ) );
		}
	
	} else {
		msgbox( $lang['reg_err_11'], "<ul>" . $reg_error . "</ul>" );
	}

}

if( $doaction != "validating" and ! $stopregistration ) {
	
	if( $_POST['dle_rules_accept'] == "yes" ) {
		
		@session_register( 'dle_rules_accept' );
		$_SESSION['dle_rules_accept'] = "1";
	
	}
	
	if( $config['registration_rules'] and ! $_SESSION['dle_rules_accept'] ) {
		
		$_GET['page'] = "dle-rules-page";
		include ENGINE_DIR . '/modules/static.php';
	
	} else {
		
		$tpl->load_template( 'registration.tpl' );
		
		$tpl->set( '[registration]', "" );
		$tpl->set( '[/registration]', "" );
		$tpl->set_block( "'\\[validation\\](.*?)\\[/validation\\]'si", "" );
		$path = parse_url( $config['http_home_url'] );
		
		if( $config['allow_sec_code'] == "yes" ) {
			$tpl->set( '[sec_code]', "" );
			$tpl->set( '[/sec_code]', "" );
			$tpl->set( '{reg_code}', "<span id=\"dle-captcha\"><img src=\"" . $path['path'] . "engine/modules/antibot.php\" alt=\"{$lang['sec_image']}\" border=\"0\" /><br /><a onclick=\"reload(); return false;\" href=\"#\">{$lang['reload_code']}</a></span>" );
		} else {
			$tpl->set( '{reg_code}', "" );
			$tpl->set_block( "'\\[sec_code\\](.*?)\\[/sec_code\\]'si", "" );
		}
		
		$tpl->copy_template = "<form  method=\"post\" name=\"registration\" onsubmit=\"if (!check_reg_daten()) {return false;};\" id=\"registration\" action=\"" . $config['http_home_url'] . "index.php?do=register\">\n" . $tpl->copy_template . "
<input name=\"submit_reg\" type=\"hidden\" id=\"submit_reg\" value=\"submit_reg\" />
</form>";
		
		$tpl->copy_template .= <<<HTML
<script language='JavaScript' type="text/javascript">
function reload () {

	var rndval = new Date().getTime(); 

	document.getElementById('dle-captcha').innerHTML = '<img src="{$path['path']}engine/modules/antibot.php?rndval=' + rndval + '" border="0" width="120" height="50" alt="" /><br /><a onclick="reload(); return false;" href="#">{$lang['reload_code']}</a>';

};
function check_reg_daten () {

	if(document.forms.registration.name.value == '') {

		alert('{$lang['reg_err_30']}');return false;

	}

	if(document.forms.registration.password1.value.length < 6) {

		alert('{$lang['reg_err_31']}');return false;

	}

	if(document.forms.registration.password1.value != document.forms.registration.password2.value) {

		alert('{$lang['reg_err_32']}');return false;

	}

	if(document.forms.registration.email.value == '') {

		alert('{$lang['reg_err_33']}');return false;

	}

	if (!(/[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+@[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+/.test(document.forms.registration.email.value))) {

		alert('{$lang['reg_err_33']}');return false;

	}

return true;

};
</script>
HTML;
		
		$tpl->compile( 'content' );
		$tpl->clear();
	
	}

}

if( isset( $_POST['submit_val'] ) ) {
	
	$fullname = $db->safesql( $parse->process( $_POST['fullname'] ) );
	$land = $db->safesql( $parse->process( $_POST['land'] ) );
	$icq = $db->safesql( $parse->process( $_POST['icq'] ) );
	$info = $db->safesql( $parse->BB_Parse( $parse->process( $_POST['info'] ), false ) );
	
	$image = $_FILES['image']['tmp_name'];
	$image_name = $_FILES['image']['name'];
	$image_size = $_FILES['image']['size'];
	$image_name = str_replace( " ", "_", $image_name );
	$img_name_arr = explode( ".", $image_name );
	$type = end( $img_name_arr );
	
	$user_arr = explode( "||", base64_decode( @rawurldecode( $_POST['id'] ) ) );
	$user = trim( $db->safesql( $user_arr[0] ) );
	$pass = md5( $user_arr[2] );
	
	$db->query( "SELECT * FROM " . USERPREFIX . "_users where name = '$user' AND password='$pass'" );
	
	if( ! $db->num_rows() ) $stop .= "Access Denied";
	else $row = $db->get_row();
	
	$db->free();
	
	if( is_uploaded_file( $image ) and ! $stop ) {

		if( intval( $user_group[$member_id['user_group']]['max_foto'] ) > 0 ) {
		
			if( $image_size < 100000 ) {
				
				$allowed_extensions = array ("jpg", "png", "jpe", "jpeg", "gif" );
				
				if( (in_array( $type, $allowed_extensions ) or in_array( strtolower( $type ), $allowed_extensions )) and $image_name ) {
					
					include_once ENGINE_DIR . '/inc/makethumb.php';
					
					$res = @move_uploaded_file( $image, ROOT_DIR . "/uploads/fotos/" . $row['user_id'] . "." . $type );
					
					if( $res ) {
						
						$thumb = new thumbnail( ROOT_DIR . "/uploads/fotos/" . $row['user_id'] . "." . $type );
						$thumb->size_auto( $user_group[$config['reg_group']]['max_foto'] );
						$thumb->jpeg_quality( $config['jpeg_quality'] );
						$thumb->save( ROOT_DIR . "/uploads/fotos/foto_" . $row['user_id'] . "." . $type );
						
						@unlink( ROOT_DIR . "/uploads/fotos/" . $row['user_id'] . "." . $type );
						$foto_name = "foto_" . $row['user_id'] . "." . $type;
						
						$db->query( "UPDATE " . USERPREFIX . "_users set foto='$foto_name' where name='$user'" );
					
					} else
						$stop = $lang['reg_err_12'];
				} else
					$stop = $lang['reg_err_13'];
			} else
				$stop = $lang['news_err_16'];
		} else
			$stop .= $lang['news_err_32'];

	}
	
	if( intval( $user_group[$member_id['user_group']]['max_info'] ) > 0 and strlen( $info ) > $user_group[$member_id['user_group']]['max_info'] ) $stop .= $lang['reg_err_14'];
	if( strlen( $fullname ) > 100 ) $stop .= $lang['reg_err_15'];
	if( strlen( $land ) > 100 ) $stop .= $lang['reg_err_16'];
	if( strlen( $icq ) > 20 ) $stop .= $lang['reg_err_17'];
	if( $parse->not_allowed_tags ) $stop .= $lang['news_err_34'];

	if ( preg_match( "/[\||\'|\<|\>|\"|\!|\]|\?|\$|\@|\/|\\\|\&\~\*\+]/", $fullname ) ) {

		$stop .= $lang['news_err_35'];
	}

	if ( preg_match( "/[\||\'|\<|\>|\"|\!|\]|\?|\$|\@|\/|\\\|\&\~\*\+]/", $land ) ) {

		$stop .= $lang['news_err_36'];
	}
	
	if( $stop ) {
		msgbox( $lang['reg_err_18'], $stop );
	} else {
		
		$xfieldsaction = "init";
		$xfieldsadd = true;
		include (ENGINE_DIR . '/inc/userfields.php');
		$filecontents = array ();
		
		if( ! empty( $postedxfields ) ) {
			foreach ( $postedxfields as $xfielddataname => $xfielddatavalue ) {
				if( ! $xfielddatavalue ) {
					continue;
				}
				
				$xfielddatavalue = $db->safesql( $parse->BB_Parse( $parse->process( $xfielddatavalue ), false ) );
				
				$xfielddataname = $db->safesql( $xfielddataname );
				
				$xfielddataname = str_replace( "|", "&#124;", $xfielddataname );
				$xfielddatavalue = str_replace( "|", "&#124;", $xfielddatavalue );
				$filecontents[] = "$xfielddataname|$xfielddatavalue";
			}
			
			$filecontents = implode( "||", $filecontents );
		} else
			$filecontents = '';
		
		$db->query( "UPDATE " . USERPREFIX . "_users set fullname='$fullname', info='$info', land='$land', icq='$icq', xfields='$filecontents' where name='$user'" );
		
		msgbox( $lang['reg_ok'], $lang['reg_ok_1'] );
		
		$stopregistration = TRUE;
	}
}

if( ($doaction == "validating") and (! $stopregistration) and (! $_POST['submit_val']) ) {
	
	$user_arr = explode( "||", base64_decode( rawurldecode( $_REQUEST['id'] ) ) );
	
	$regpassword = md5( $user_arr[2] );
	$name = trim( $db->safesql( htmlspecialchars( $parse->process( $user_arr[0] ) ) ) );
	$email = trim( $db->safesql( $parse->process( $user_arr[1] ) ) );
	
	if( md5( md5( $name . $email . DBHOST . DBNAME . $config['key'] ) ) != $user_arr[3] ) die( 'ID not valid!' );
	
	$reg_error = check_reg( $name, $email, $regpassword, $regpassword );
	
	if( $reg_error != "" ) {
		msgbox( $lang['reg_err_11'], $reg_error );
		$stopregistration = TRUE;
	} else {
		
		if( ($_REQUEST['step'] != 2) and $config['registration_type'] ) {
			$stopregistration = TRUE;
			$lang['confirm_ok'] = str_replace( '{email}', $email, $lang['confirm_ok'] );
			$lang['confirm_ok'] = str_replace( '{login}', $name, $lang['confirm_ok'] );
			msgbox( $lang['all_info'], $lang['confirm_ok'] . "<br /><br /><a href=\"" . $config['http_home_url'] . "index.php?do=register&doaction=validating&step=2&id=" . rawurlencode( $_REQUEST['id'] ) . "\">" . $lang['reg_next'] . "</a>" );
		} else {
			
			$add_time = time() + ($config['date_adjust'] * 60);
			$_IP = $db->safesql( $_SERVER['REMOTE_ADDR'] );
			if( intval( $config['reg_group'] ) < 3 ) $config['reg_group'] = 4;
			
			$db->query( "INSERT INTO " . USERPREFIX . "_users (name, password, email, reg_date, lastdate, user_group, info, signature, favorites, xfields, logged_ip) VALUES ('$name', '$regpassword', '$email', '$add_time', '$add_time', '" . $config['reg_group'] . "', '', '', '', '', '" . $_IP . "')" );
			$id = $db->insert_id();
			
			set_cookie( "dle_user_id", $id, 365 );
			set_cookie( "dle_password", $user_arr[2], 365 );
			
			@session_register( 'dle_user_id' );
			@session_register( 'dle_password' );
			
			$_SESSION['dle_user_id'] = $id;
			$_SESSION['dle_password'] = $user_arr[2];
		
		}
	
	}

}

if( $doaction == "validating" and ! $stopregistration ) {
	
	$tpl->load_template( 'registration.tpl' );
	
	$tpl->set( '[validation]', "" );
	$tpl->set( '[/validation]', "" );
	$tpl->set_block( "'\\[registration\\].*?\\[/registration\\]'si", "" );
	
	$xfieldsaction = "list";
	$xfieldsadd = true;
	include (ENGINE_DIR . '/inc/userfields.php');
	$tpl->set( '{xfields}', $output );
	
	$tpl->copy_template = "<form  method=\"post\" name=\"registration\" enctype=\"multipart/form-data\" action=\"" . $PHP_SELF . "\">\n" . $tpl->copy_template . "
<input name=\"submit_val\" type=\"hidden\" id=\"submit_val\" value=\"submit_val\" />
<input name=\"do\" type=\"hidden\" id=\"do\" value=\"register\" />
<input name=\"doaction\" type=\"hidden\" id=\"doaction\" value=\"validating\" />
<input name=\"id\" type=\"hidden\" id=\"id\" value=\"{$_REQUEST['id']}\" />
</form>";
	
	$tpl->compile( 'content' );
	$tpl->clear();
}

?>