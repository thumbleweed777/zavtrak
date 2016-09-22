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
 Файл: profile.php
-----------------------------------------------------
 Назначение: Профиль пользователя
=====================================================
*/

if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

include_once ENGINE_DIR . '/classes/parse.class.php';

//####################################################################################################################
//         Обновление информации о пользователе
//####################################################################################################################
if( $allow_userinfo and $doaction == "adduserinfo" ) {
	
	if( $_POST['dle_allow_hash'] == "" or $_POST['dle_allow_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User ID not valid" );
	
	}
	
	$parse = new ParseFilter( );
	$parse->safe_mode = true;
	$parse->allow_url = false;
	$parse->allow_image = false;
	
	$stop = false;
	
	$password1 = $db->safesql( $parse->process( $_POST['password1'] ) );
	$password2 = $db->safesql( $parse->process( $_POST['password2'] ) );
	$altpass = md5( $_POST['altpass'] );
	$info = $db->safesql( $parse->BB_Parse( $parse->process( $_POST['info'] ), false ) );
	$email = $db->safesql( $parse->process( $_POST['email'] ) );
	
	$fullname = $db->safesql( $parse->process( $_POST['fullname'] ) );
	$land = $db->safesql( $parse->process( $_POST['land'] ) );
	$icq = intval( $_POST['icq'] );
	if( ! $icq ) $icq = "";
	
	$allowed_ip = str_replace( "\r", "", trim( $_POST['allowed_ip'] ) );
	$allowed_ip = str_replace( "\n", "|", $allowed_ip );
	$allowed_ip = $db->safesql( $parse->process( $allowed_ip ) );
	
	$row = $db->super_query( "SELECT * FROM " . USERPREFIX . "_users WHERE name = '$user'" );
	$xfieldsid = stripslashes( $row['xfields'] );
	
	if( $user_group[$row['user_group']]['allow_signature'] ) {
		
		$parse->allow_url = $user_group[$member_id['user_group']]['allow_url'];
		$parse->allow_image = $user_group[$member_id['user_group']]['allow_image'];
		$signature = $db->safesql( $parse->BB_Parse( $parse->process( $_POST['signature'] ), false ) );
	
	} else
		$signature = "";
	
	$image = $_FILES['image']['tmp_name'];
	$image_name = $_FILES['image']['name'];
	$image_size = $_FILES['image']['size'];
	$img_name_arr = explode( ".", $image_name );
	$type = end( $img_name_arr );
	
	if( $image_name != "" ) $image_name = totranslit( stripslashes( $img_name_arr[0] ) ) . "." . totranslit( $type );
	
	if( ! $is_logged or ! ($member_id['user_id'] == $row['user_id'] or $member_id['user_group'] == 1) ) {
		$stop = $lang['news_err_13'];
	}
	
	if( is_uploaded_file( $image ) and ! $stop ) {
		
		if( intval( $user_group[$member_id['user_group']]['max_foto'] ) > 0 ) {
			
			if( $image_size < 100000 ) {
				
				$allowed_extensions = array ("jpg", "png", "jpe", "jpeg", "gif" );
				
				if( (in_array( $type, $allowed_extensions ) or in_array( strtolower( $type ), $allowed_extensions )) and $image_name ) {
					
					include_once ENGINE_DIR . '/inc/makethumb.php';
					
					$res = @move_uploaded_file( $image, ROOT_DIR . "/uploads/fotos/" . $row['user_id'] . "." . $type );
					
					if( $res ) {
						
						@chmod( ROOT_DIR . "/uploads/fotos/" . $row['user_id'] . "." . $type, 0666 );
						$thumb = new thumbnail( ROOT_DIR . "/uploads/fotos/" . $row['user_id'] . "." . $type );
						
						if( $thumb->size_auto( $user_group[$member_id['user_group']]['max_foto'] ) ) {
							$thumb->jpeg_quality( $config['jpeg_quality'] );
							$thumb->save( ROOT_DIR . "/uploads/fotos/foto_" . $row['user_id'] . "." . $type );
						} else {
							@rename( ROOT_DIR . "/uploads/fotos/" . $row['user_id'] . "." . $type, ROOT_DIR . "/uploads/fotos/foto_" . $row['user_id'] . "." . $type );
						}
						
						@chmod( ROOT_DIR . "/uploads/fotos/foto_" . $row['user_id'] . "." . $type, 0666 );
						$foto_name = "foto_" . $row['user_id'] . "." . $type;
						
						$db->query( "UPDATE " . USERPREFIX . "_users set foto='$foto_name' where name='$user'" );
					
					} else
						$stop .= $lang['news_err_14'];
				} else
					$stop .= $lang['news_err_15'];
			} else
				$stop .= $lang['news_err_16'];
		} else
			$stop .= $lang['news_err_32'];
		
		@unlink( ROOT_DIR . "/uploads/fotos/" . $row['user_id'] . "." . $type );
	}
	
	if( $_POST['del_foto'] == "yes" ) {
		
		@unlink( ROOT_DIR . "/uploads/fotos/" . $row['foto'] );
		$db->query( "UPDATE " . USERPREFIX . "_users set foto='' WHERE name='$user'" );
	
	}
	
	if( strlen( $password1 ) > 0 ) {
		
		$altpass = md5( $altpass );
		
		if( $altpass != $member_id['password'] ) {
			$stop .= $lang['news_err_17'];
		}
		
		if( $password1 != $password2 ) {
			$stop .= $lang['news_err_18'];
		}
		
		if( strlen( $password1 ) < 6 ) {
			$stop .= $lang['news_err_19'];
		}
	}
	
# php 5.2
# 	if( (! ereg( '^[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+' . '@' . '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.' . '[-!#$%&\'*+\\./0-9=?A-Z^_`a-z{|}~]+$', $email )) or (empty( $email )) ) {

	
	if( (! preg_match( '/^[-!#$%&\'*+\\.\/0-9=?A-Z^_`a-z{|}~]+' . '@' . '[-!#$%&\'*+\\/0-9=?A-Z^_`a-z{|}~]+\.' . '[-!#$%&\'*+\\.\/0-9=?A-Z^_`a-z{|}~]+$/', $email )) or (empty( $email )) ) {
		
		$stop .= $lang['news_err_21'];
	}
	if( intval( $user_group[$member_id['user_group']]['max_info'] ) > 0 and strlen( $info ) > $user_group[$member_id['user_group']]['max_info'] ) {
		
		$stop .= $lang['news_err_22'];
	}
	if( intval( $user_group[$member_id['user_group']]['max_signature'] ) > 0 and strlen( $signature ) > $user_group[$member_id['user_group']]['max_signature'] ) {
		
		$stop .= $lang['not_allowed_sig'];
	}
	if( strlen( $fullname ) > 100 ) {
		
		$stop .= $lang['news_err_23'];
	}
	if ( preg_match( "/[\||\'|\<|\>|\"|\!|\]|\?|\$|\@|\/|\\\|\&\~\*\+]/", $fullname ) ) {

		$stop .= $lang['news_err_35'];
	}
	if( strlen( $land ) > 100 ) {
		
		$stop .= $lang['news_err_24'];
	}
	if ( preg_match( "/[\||\'|\<|\>|\"|\!|\]|\?|\$|\@|\/|\\\|\&\~\*\+]/", $land ) ) {

		$stop .= $lang['news_err_36'];
	}
	if( strlen( $icq ) > 20 ) {
		
		$stop .= $lang['news_err_25'];
	}
	
	if( $parse->not_allowed_tags ) {
		
		$stop .= $lang['news_err_34'];
	}
	
	$db->query( "SELECT name FROM " . USERPREFIX . "_users WHERE email = '$email' AND name != '$user'" );
	
	if( $db->num_rows() ) {
		$stop .= $lang['reg_err_8'];
	}
	
	$db->free();
	
	if( $stop ) {
		msgbox( $lang['all_err_1'], $stop );
	} else {
		
		if( $_POST['allow_mail'] ) {
			$allow_mail = 0;
		} else {
			$allow_mail = 1;
		}
		
		$xfieldsaction = "init";
		$xfieldsadd = false;
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
		
		if( strlen( $password1 ) > 0 ) {
			
			$password1 = md5( md5( $password1 ) );
			$sql_user = "UPDATE " . USERPREFIX . "_users set fullname='$fullname', land='$land', icq='$icq', email='$email', info='$info', signature='$signature', password='$password1', allow_mail='$allow_mail', xfields='$filecontents', allowed_ip='$allowed_ip' where name='$user'";
		
		} else {
			
			$sql_user = "UPDATE " . USERPREFIX . "_users set fullname='$fullname', land='$land', icq='$icq', email='$email', info='$info', signature='$signature', allow_mail='$allow_mail', xfields='$filecontents', allowed_ip='$allowed_ip' where name='$user'";
		
		}
		
		$db->query( $sql_user );
	}

}

//####################################################################################################################
//         Просмотр профиля пользователя
//####################################################################################################################


$parse = new ParseFilter( );

$user_found = FALSE;

$sql_result = $db->query( "SELECT * FROM " . USERPREFIX . "_users where name = '$user'" );

$tpl->load_template( 'userinfo.tpl' );

while ( $row = $db->get_row( $sql_result ) ) {
	
	$user_found = TRUE;
	
	if( $row['banned'] == 'yes' ) $user_group[$row['user_group']]['group_name'] = $lang['user_ban'];
	
	if( $row['allow_mail'] ) {
		$email = explode( "@", $row['email'], 2 );
		$tpl->set( '{email}', "<a href=\"$PHP_SELF?do=feedback&amp;user=$row[user_id]\">" . $lang['news_mail'] . "</a>" );
	} else {
		$tpl->set( '{email}', $lang['news_nomail'], $output );
	}
	
	$tpl->set( '{pm}', "<a href=\"$PHP_SELF?do=pm&amp;doaction=newpm&amp;user=" . $row['user_id'] . "\">" . $lang['news_pmnew'] . "</a>" );
	
	if( ! $row['allow_mail'] ) $mailbox = "checked";
	else $mailbox = "";
	
	if( $row['foto'] and (file_exists( ROOT_DIR . "/uploads/fotos/" . $row['foto'] )) ) $tpl->set( '{foto}', $config['http_home_url'] . "uploads/fotos/" . $row['foto'] );
	else $tpl->set( '{foto}', "{THEME}/images/noavatar.png" );
	
	$tpl->set( '{hidemail}', "<input type=\"checkbox\" name=\"allow_mail\" value=\"1\" " . $mailbox . "> " . $lang['news_noamail'] );
	$tpl->set( '{usertitle}', stripslashes( $row['name'] ) );
	$tpl->set( '{fullname}', stripslashes( $row['fullname'] ) );
	$tpl->set( '{icq}', stripslashes( $row['icq'] ) );
	$tpl->set( '{land}', stripslashes( $row['land'] ) );
	$tpl->set( '{info}', stripslashes( $row['info'] ) );
	$tpl->set( '{editmail}', stripslashes( $row['email'] ) );
	$tpl->set( '{comm_num}', $row['comm_num'] );
	$tpl->set( '{news_num}', $row['news_num'] );
	$tpl->set( '{status}', stripslashes( $user_group[$row['user_group']]['group_name'] ) );
	$tpl->set( '{rate}', userrating( $row['name'] ) );
	$tpl->set( '{registration}', langdate( "j F Y H:i", $row['reg_date'] ) );
	$tpl->set( '{lastdate}', langdate( "j F Y H:i", $row['lastdate'] ) );
	
	if( $user_group[$row['user_group']]['icon'] ) $tpl->set( '{group-icon}', "<img src=\"" . $user_group[$row['user_group']]['icon'] . "\" border=\"0\" />" );
	else $tpl->set( '{group-icon}', "" );
	
	if( $is_logged and $user_group[$row['user_group']]['time_limit'] and ($member_id['user_id'] == $row['user_id'] or $member_id['user_group'] < 3) ) {
		
		$tpl->set_block( "'\\[time_limit\\](.*?)\\[/time_limit\\]'si", "\\1" );
		
		if( $row['time_limit'] ) {
			
			$tpl->set( '{time_limit}', langdate( "j F Y H:i", $row['time_limit'] ) );
		
		} else {
			
			$tpl->set( '{time_limit}', $lang['no_limit'] );
		
		}
	
	} else {
		
		$tpl->set_block( "'\\[time_limit\\](.*?)\\[/time_limit\\]'si", "" );
	
	}
	
	$_IP = $db->safesql( $_SERVER['REMOTE_ADDR'] );
	
	$tpl->set( '{ip}', $_IP );
	$tpl->set( '{allowed-ip}', stripslashes( str_replace( "|", "\n", $row['allowed_ip'] ) ) );
	$tpl->set( '{editinfo}', $parse->decodeBBCodes( $row['info'], false ) );
	
	if( $user_group[$row['user_group']]['allow_signature'] ) $tpl->set( '{editsignature}', $parse->decodeBBCodes( $row['signature'], false ) );
	else $tpl->set( '{editsignature}', $lang['sig_not_allowed'] );
	
	if( $row['comm_num'] ) {
		
		$tpl->set( '{comments}', "<a href=\"$PHP_SELF?do=lastcomments&amp;userid=" . $row['user_id'] . "\">" . $lang['last_comm'] . "</a>" );
	
	} else {
		
		$tpl->set( '{comments}', $lang['last_comm'] );
	
	}
	
	if( $row['news_num'] ) {
		
		if( $config['allow_alt_url'] == "yes" ) {
			
			$tpl->set( '{news}', "<a href=\"" . $config['http_home_url'] . "user/" . urlencode( $row['name'] ) . "/news/" . "\">" . $lang['all_user_news'] . "</a>" );
			$tpl->set( '[rss]', "<a href=\"" . $config['http_home_url'] . "user/" . urlencode( $row['name'] ) . "/rss.xml" . "\" title=\"" . $lang['rss_user'] . "\">" );
			$tpl->set( '[/rss]', "</a>" );
		
		} else {
			
			$tpl->set( '{news}', "<a href=\"" . $PHP_SELF . "?subaction=allnews&amp;user=" . urlencode( $row['name'] ) . "\">" . $lang['all_user_news'] . "</a>" );
			$tpl->set( '[rss]', "<a href=\"engine/rss.php?subaction=allnews&amp;user=" . urlencode( $row['name'] ) . "\" title=\"" . $lang['rss_user'] . "\">" );
			$tpl->set( '[/rss]', "</a>" );
		}
	} else {
		
		$tpl->set( '{news}', $lang['all_user_news'] );
		$tpl->set_block( "'\\[rss\\](.*?)\\[/rss\\]'si", "" );
	
	}
	
	if( $row['signature'] and $user_group[$row['user_group']]['allow_signature'] ) {
		
		$tpl->set_block( "'\\[signature\\](.*?)\\[/signature\\]'si", "\\1" );
		$tpl->set( '{signature}', stripslashes( $row['signature'] ) );
	
	} else {
		
		$tpl->set_block( "'\\[signature\\](.*?)\\[/signature\\]'si", "" );
	
	}
	
	$xfieldsaction = "list";
	$xfieldsadd = false;
	$xfieldsid = $row['xfields'];
	include (ENGINE_DIR . '/inc/userfields.php');
	$tpl->set( '{xfields}', $output );
	
	// Обработка дополнительных полей
	$xfieldsdata = xfieldsdataload( $row['xfields'] );
	
	foreach ( $xfields as $value ) {
		$preg_safe_name = preg_quote( $value[0], "'" );
		
		if( $value[5] != 1 or ($is_logged and $member_id['user_group'] == 1) or ($is_logged and $member_id['user_id'] == $row['user_id']) ) {
			if( empty( $xfieldsdata[$value[0]] ) ) {
				$tpl->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
			} else {
				$tpl->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "\\1", $tpl->copy_template );
			}
			$tpl->copy_template = preg_replace( "'\\[xfvalue_{$preg_safe_name}\\]'i", stripslashes( $xfieldsdata[$value[0]] ), $tpl->copy_template );
		} else {
			$tpl->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
			$tpl->copy_template = preg_replace( "'\\[xfvalue_{$preg_safe_name}\\]'i", "", $tpl->copy_template );
		}
	}
	// Обработка дополнительных полей
	

	if( $is_logged and ($member_id['user_id'] == $row['user_id'] or $member_id['user_group'] == 1) ) {
		$tpl->set( '{edituser}', "[ <a href=\"javascript:ShowOrHide('options')\">" . $lang['news_option'] . "</a> ]" );
	} else
		$tpl->set( '{edituser}', "" );
	
	if( $is_logged and ($member_id['user_id'] == $row['user_id'] or $member_id['user_group'] == 1) ) {
		$tpl->set( '[not-logged]', "" );
		$tpl->set( '[/not-logged]', "" );
	} else
		$tpl->set_block( "'\\[not-logged\\](.*?)\\[/not-logged\\]'si", "<!-- profile -->" );
	
	if( $config['allow_alt_url'] == "yes" ) $link_profile = $config['http_home_url'] . "user/" . urlencode( $row['name'] ) . "/";
	else $link_profile = $PHP_SELF . "?subaction=userinfo&user=" . urlencode( $row['name'] );
	
	if( $is_logged and ($member_id['user_id'] == $row['user_id'] or $member_id['user_group'] == 1) ) {
		$tpl->copy_template = "<form  method=\"post\" name=\"userinfo\" id=\"userinfo\" enctype=\"multipart/form-data\" action=\"{$link_profile}\">" . $tpl->copy_template . "
		<input type=\"hidden\" name=\"doaction\" value=\"adduserinfo\" />
		<input type=\"hidden\" name=\"dle_allow_hash\" value=\"{$dle_login_hash}\" />
		</form>";
	}
	
	$tpl->compile( 'content' );

}

$tpl->clear();
$db->free( $sql_result );

if( $user_found == FALSE ) {
	$allow_active_news = false;
	msgbox( $lang['all_err_1'], $lang['news_err_26'] );
}
?>