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
 Файл: addcomments.php
-----------------------------------------------------
 Назначение: AJAX для добавления комментариев
=====================================================
*/

@error_reporting( 7 );
@ini_set( 'display_errors', true );
@ini_set( 'html_errors', false );

@session_start();

define( 'DATALIFEENGINE', true );
define( 'ROOT_DIR', '../..' );
define( 'ENGINE_DIR', '..' );

include ENGINE_DIR . '/data/config.php';

if( $config['http_home_url'] == "" ) {
	
	$config['http_home_url'] = explode( "engine/ajax/addcomments.php", $_SERVER['PHP_SELF'] );
	$config['http_home_url'] = reset( $config['http_home_url'] );
	$config['http_home_url'] = "http://" . $_SERVER['HTTP_HOST'] . $config['http_home_url'];

}

require_once ENGINE_DIR . '/classes/mysql.php';
require_once ENGINE_DIR . '/data/dbconfig.php';
require_once ENGINE_DIR . '/modules/functions.php';
require_once ENGINE_DIR . '/classes/templates.class.php';

$_REQUEST['skin'] = totranslit($_REQUEST['skin'], false, false);

if( ! @is_dir( ROOT_DIR . '/templates/' . $_REQUEST['skin'] ) ) {
	die( "Hacking attempt!" );
}

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

if( $config["lang_" . $_REQUEST['skin']] ) {
	
	@include_once (ROOT_DIR . '/language/' . $config["lang_" . $_REQUEST['skin']] . '/website.lng');

} else {
	
	include_once ROOT_DIR . '/language/' . $config['langs'] . '/website.lng';

}
$config['charset'] = ($lang['charset'] != '') ? $lang['charset'] : $config['charset'];

require_once ENGINE_DIR . '/modules/sitelogin.php';
if( ! $is_logged ) {
	$member_id['user_group'] = 5;
}

$tpl = new dle_template( );
$tpl->dir = ROOT_DIR . '/templates/' . $_REQUEST['skin'];
define( 'TEMPLATE_DIR', $tpl->dir );

$ajax_adds = true;

$_POST['name'] = convert_unicode( $_POST['name'], $config['charset']  );
$_POST['mail'] = convert_unicode( $_POST['mail'], $config['charset'] );
$_POST['comments'] = convert_unicode( $_POST['comments'], $config['charset'] );

require_once ENGINE_DIR . '/modules/addcomments.php';

if( $CN_HALT != TRUE ) {
	$row = $db->super_query( "SELECT " . PREFIX . "_comments.id, post_id, " . PREFIX . "_comments.user_id, date, autor as gast_name, " . PREFIX . "_comments.email as gast_email, text, ip, is_register, name, " . USERPREFIX . "_users.email, news_num, comm_num, user_group, reg_date, signature, foto, fullname, land, icq, xfields FROM " . PREFIX . "_comments LEFT JOIN " . USERPREFIX . "_users ON " . PREFIX . "_comments.user_id=" . USERPREFIX . "_users.user_id WHERE " . PREFIX . "_comments.post_id = '$post_id' order by id DESC LIMIT 0,1" );
	
	$tpl->load_template( 'comments.tpl' );
	
	if( strpos( $tpl->copy_template, "[xfvalue_" ) !== false ) $xfound = true;
	else $xfound = false;
	
	if( $xfound ) $xfields = xfieldsload( true );
	
	$row['date'] = strtotime( $row['date'] );
	$row['gast_name'] = stripslashes( $row['gast_name'] );
	$row['gast_email'] = stripslashes( $row['gast_email'] );
	
	if( ! $row['is_register'] or $row['name'] == '' ) {
		if( $row['gast_email'] != "" ) {
			if( preg_match( "/^[\.A-z0-9_\-]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $row['gast_email'] ) ) {
				$url_target = "";
				$mail_or_url = "mailto:";
			} else {
				$url_target = "target=\"_blank\"";
				$mail_or_url = "";
				if( substr( $row[email], 0, 3 ) == "www" ) {
					$mail_or_url = "http://";
				}
			}
			
			if( $mail_or_url == "mailto:" ) {
				$tpl->set( '{author}', "<a href=\"mailto:{$row['gast_email']}\">" . $row['gast_name'] . "</a>" );
			} else {
				$tpl->set( '{author}', "<a $url_target href=\"$mail_or_url" . $row[gast_email] . "\">" . $row['gast_name'] . "</a>" );
			}
		
		} else {
			$tpl->set( '{author}', $row['gast_name'] );
		}
	} else {
		
		if( $config['ajax'] ) $go_page = "onclick=\"DlePage(\'subaction=userinfo&user=" . urlencode( $row['name'] ) . "\'); return false;\" ";
		else $go_page = "";
		
		if( $config['allow_alt_url'] == "yes" ) {
			
			$go_page .= "href=\"" . $config['http_home_url'] . "user/" . urlencode( $row['name'] ) . "/\"";
		
		} else {
			
			$go_page .= "href=\"$PHP_SELF?subaction=userinfo&amp;user=" . urlencode( $row['name'] ) . "\"";
		
		}
		
		$go_page = "onClick=\"return dropdownmenu(this, event, UserMenu('" . htmlspecialchars( $go_page ) . "', '" . $row['user_id'] . "', '" . $member_id['user_group'] . "'), '170px')\" onMouseout=\"delayhidemenu()\"";
		
		if( $config['allow_alt_url'] == "yes" ) $tpl->set( '{author}', "<a {$go_page} href=\"" . $config['http_home_url'] . "user/" . urlencode( $row['name'] ) . "/\">" . $row['name'] . "</a>" );
		else $tpl->set( '{author}', "<a {$go_page} href=\"$PHP_SELF?subaction=userinfo&amp;user=" . urlencode( $row['name'] ) . "\">" . $row['name'] . "</a>" );
	
	}

	if( $is_logged and $member_id['user_group'] == '1' ) $tpl->set( '{ip}', "IP: <a onClick=\"return dropdownmenu(this, event, IPMenu('" . $row['ip'] . "', '" . $lang['ip_info'] . "', '" . $lang['ip_tools'] . "', '" . $lang['ip_ban'] . "'), '190px')\" onMouseout=\"delayhidemenu()\" href=\"http://www.nic.ru/whois/?ip={$row['ip']}\" target=\"_blank\">{$row['ip']}</a>" );
	else $tpl->set( '{ip}', '' );
	
	if( $is_logged and (($member_id['name'] == $row['name'] and $row['is_register'] and $user_group[$member_id['user_group']]['allow_editc']) or $user_group[$member_id['user_group']]['edit_allc']) ) {
		$tpl->set( '[com-edit]', "<a onClick=\"return dropdownmenu(this, event, MenuCommBuild('" . $row['id'] . "'), '170px')\" onMouseout=\"delayhidemenu()\" href=\"" . $config['http_home_url'] . "?do=comments&action=comm_edit&id=" . $row['id'] . "\">" );
		$tpl->set( '[/com-edit]', "</a>" );
		$allow_comments_ajax = true;
	} else
		$tpl->set_block( "'\\[com-edit\\](.*?)\\[/com-edit\\]'si", "" );
	
	if( $is_logged and (($member_id['name'] == $row['name'] and $row['is_register'] and $user_group[$member_id['user_group']]['allow_delc']) or $member_id['user_group'] == '1' or $user_group[$member_id['user_group']]['del_allc']) ) {
		$tpl->set( '[com-del]', "<a href=\"javascript:confirmDelete('" . $config['http_home_url'] . "?do=comments&action=comm_del&id=" . $row['id'] . "&amp;dle_allow_hash=" . $dle_login_hash . "')\">" );
		$tpl->set( '[/com-del]', "</a>" );
	} else
		$tpl->set_block( "'\\[com-del\\](.*?)\\[/com-del\\]'si", "" );
	
	if( ($user_group[$member_id['user_group']]['allow_addc']) and $config['allow_comments'] == "yes" ) {
		if( ! $row['is_register'] or $row['name'] == '' ) $row['name'] = stripslashes( $row['gast_name'] );
		else $row['name'] = stripslashes( $row['name'] );
		$tpl->set( '[fast]', "<a onmouseover=\"dle_copy_quote('" . str_replace( array (" ","&#039;"), array ("&nbsp;", "&amp;#039;"), $row['name'] ) . "');\" href=\"#\" onClick=\"dle_ins('" . str_replace( array (" ", "&#039;"), array ("&nbsp;", "&amp;#039;"), $row['name'] ) . "'); return false;\"\">" );
		$tpl->set( '[/fast]', "</a>" );
	} else
		$tpl->set_block( "'\\[fast\\](.*?)\\[/fast\\]'si", "" );
	
	$tpl->set( '{mail}', $row['email'] );
	
	if( date( Ymd, $row['date'] ) == date( Ymd, $_TIME ) ) {
		
		$tpl->set( '{date}', $lang['time_heute'] . langdate( ", H:i", $row['date'] ) );
	
	} elseif( date( Ymd, $row['date'] ) == date( Ymd, ($_TIME - 86400) ) ) {
		
		$tpl->set( '{date}', $lang['time_gestern'] . langdate( ", H:i", $row['date'] ) );
	
	} else {
		
		$tpl->set( '{date}', langdate( $config['timestamp_active'], $row['date'] ) );
	
	}
	
	$tpl->set( '{news_title}', "" );
	$tpl->set( '{PAGEBREAK}', '' );
	
	// Обработка дополнительных полей
	if( $xfound ) {
		$xfieldsdata = xfieldsdataload( $row['xfields'] );
		
		foreach ( $xfields as $value ) {
			$preg_safe_name = preg_quote( $value[0], "'" );
			
			if( $value[5] != 1 or $member_id['user_group'] == 1 or ($is_logged and $row['is_register'] and $member_id['name'] == $row['name']) ) {
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
	}
	// Обработка дополнительных полей
	

	$tpl->set( '{comment-id}', "--" );
	
	if( $row['foto'] ) $tpl->set( '{foto}', $config['http_home_url'] . "uploads/fotos/" . $row['foto'] );
	else $tpl->set( '{foto}', "{THEME}/images/noavatar.png" );
	
	if( $row['is_register'] and $row['icq'] ) $tpl->set( '{icq}', stripslashes( $row['icq'] ) );
	else $tpl->set( '{icq}', '--' );
	
	if( $row['is_register'] and $row['land'] ) $tpl->set( '{land}', stripslashes( $row['land'] ) );
	else $tpl->set( '{land}', '--' );
	
	if( $row['is_register'] and $row['fullname'] ) $tpl->set( '{fullname}', stripslashes( $row['fullname'] ) );
	else $tpl->set( '{fullname}', '--' );
	
	if( $row['is_register'] ) $tpl->set( '{registration}', langdate( "j.m.Y", $row['reg_date'] ) );
	else $tpl->set( '{registration}', '--' );
	
	if( $row['is_register'] and $row['signature'] and $user_group[$row['user_group']]['allow_signature'] ) {
		$tpl->set_block( "'\\[signature\\](.*?)\\[/signature\\]'si", "\\1" );
		$tpl->set( '{signature}', stripslashes( $row['signature'] ) );
	} else {
		$tpl->set_block( "'\\[signature\\](.*?)\\[/signature\\]'si", "" );
	}
	
	if( ! $row['user_group'] ) $row['user_group'] = 5;
	
	if( $user_group[$row['user_group']]['icon'] ) $tpl->set( '{group-icon}', "<img src=\"" . $user_group[$row['user_group']]['icon'] . "\" border=\"0\" />" );
	else $tpl->set( '{group-icon}', "" );
	
	$tpl->set( '{group-name}', $user_group[$row['user_group']]['group_name'] );
	
	$tpl->set( '{news-num}', intval( $row['news_num'] ) );
	$tpl->set( '{comm-num}', intval( $row['comm_num'] ) );
	
	$tpl->set( '{comment}', "<div id='comm-id-" . $row['id'] . "'>" . stripslashes( $row['text'] ) . "</div>" );
	
	if( $user_group[$member_id['user_group']]['allow_hide'] ) $tpl->set_block( "'\[hide\](.*?)\[/hide\]'si", "\\1" );
	else $tpl->set_block( "'\\[hide\\](.*?)\\[/hide\\]'si", "<div class=\"quote\">" . $lang['news_regus'] . "</div>" );
	
	$tpl->compile( 'content' );
}

$db->close();

if( $_POST['editor_mode'] == "wysiwyg" ) {
	
	$clear_value = "tinyMCE.execInstanceCommand('comments', 'mceSetContent', false, '', false)";

} else {
	
	$clear_value = "form.comments.value = '';";

}

if( $CN_HALT ) {
	
	$stop = implode( '\n', $stop );
	
	$tpl->result['content'] = "<script language=\"JavaScript\" type=\"text/javascript\">\n";
	
	if( ! $where_approve ) $tpl->result['content'] .= "
	var form = document.getElementById('dle-comments-form');

	{$clear_value}

	if ( form.sec_code ) {
	   form.sec_code.value = ''; 
    }";
	
	$tpl->result['content'] .= "\n alert ('" . $stop . "');\n var timeval = new Date().getTime();\n

if ( document.getElementById('dle-captcha') ) {
	document.getElementById('dle-captcha').innerHTML = '<img src=\"' + dle_root + 'engine/modules/antibot.php?rand=' + timeval + '\" border=0><br /><a onclick=\"reload(); return false;\" href=\"#\">{$lang['reload_code']}</a>';
}\n </script>";

} else {
	
	$tpl->result['content'] .= <<<HTML
<script language='JavaScript' type="text/javascript">
	var timeval = new Date().getTime();
	var post_box_top  = _get_obj_toppos( document.getElementById( 'dle-ajax-comments' ) );

			if ( post_box_top )
			{
				scroll( 0, post_box_top - 70 );
			}

	var form = document.getElementById('dle-comments-form');

	{$clear_value}

	if ( form.sec_code ) {
	   form.sec_code.value = ''; 
	   document.getElementById('dle-captcha').innerHTML = "<img src=\"" + dle_root + "engine/modules/antibot.php?rand=" + timeval + "\" border=0><br /><a onclick=\"reload(); return false;\" href=\"#\">{$lang['reload_code']}</a>";
    }
</script>
HTML;

}

$tpl->result['content'] = str_replace( '{THEME}', $config['http_home_url'] . 'templates/' . $_REQUEST['skin'], $tpl->result['content'] );

@header( "Content-type: text/css; charset=" . $config['charset'] );
echo $tpl->result['content'];
?>