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
 Файл: lastcomments.php
-----------------------------------------------------
 Назначение: вывод последних комментариев
=====================================================
*/
if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

$tpl->load_template( 'comments.tpl' );

if( strpos( $tpl->copy_template, "[xfvalue_" ) !== false ) $xfound = true;
else $xfound = false;

if( $xfound ) $xfields = xfieldsload( true );

$number = intval( $config['comm_nummers'] );

$cstart = intval( $_REQUEST['cstart'] );
$userid = intval( $_REQUEST['userid'] );

if( $cstart < 0 ) $cstart = 0;

if( $cstart ) {
	$cstart = $cstart - 1;
	$cstart = $cstart * $number;
}

$i = $cstart;
$s = 0;

$_SESSION['referrer'] = $_SERVER['REQUEST_URI'];

$allow_list = explode( ',', $user_group[$member_id['user_group']]['allow_cats'] );
$where = array ();

if( $userid ) {
	$where[] = PREFIX . "_comments.user_id='$userid'";
	$user_query = "do=lastcomments&amp;userid=" . $userid;
} else
	$user_query = "do=lastcomments";

if( $allow_list[0] != "all" ) {
	
	$join = "LEFT JOIN " . PREFIX . "_post ON " . PREFIX . "_comments.post_id=" . PREFIX . "_post.id ";
	
	if( $config['allow_multi_category'] ) {
		
		$where[] = PREFIX . "_post.category regexp '[[:<:]](" . implode( '|', $allow_list ) . ")[[:>:]]'";
	
	} else {
		
		$where[] = PREFIX . "_post.category IN ('" . implode( "','", $allow_list ) . "')";
	
	}

} else {
	
	$join = "";

}

if( $config['allow_cmod'] ) {
	
	$where[] = PREFIX . "_comments.approve='1'";

}

if( count( $where ) ) {
	
	$where = implode( " AND ", $where );
	$where = "WHERE " . $where;

} else
	$where = "";

$sql_comm = "SELECT " . PREFIX . "_comments.id, post_id, " . PREFIX . "_comments.user_id, " . PREFIX . "_comments.date, " . PREFIX . "_comments.autor as gast_name, " . PREFIX . "_comments.email as gast_email, text, ip, is_register, name, " . USERPREFIX . "_users.email, news_num, " . USERPREFIX . "_users.comm_num, user_group, reg_date, signature, foto, fullname, land, icq, " . USERPREFIX . "_users.xfields as xprofile, " . PREFIX . "_post.title, " . PREFIX . "_post.date as newsdate, " . PREFIX . "_post.alt_name, " . PREFIX . "_post.category, " . PREFIX . "_post.flag FROM " . PREFIX . "_comments LEFT JOIN " . PREFIX . "_post ON " . PREFIX . "_comments.post_id=" . PREFIX . "_post.id LEFT JOIN " . USERPREFIX . "_users ON " . PREFIX . "_comments.user_id=" . USERPREFIX . "_users.user_id " . $where . " ORDER BY date desc LIMIT " . $cstart . "," . $number;
$sql_count = "SELECT COUNT(*) as count FROM " . PREFIX . "_comments " . $join . $where;

$sql_result = $db->query( $sql_comm );
$row_count = $db->super_query( $sql_count );

if( ! $db->num_rows( $sql_result ) ) msgbox( $lang['all_info'], $lang['err_last'] );

while ( $row = $db->get_row( $sql_result ) ) {
	
	$row['date'] = strtotime( $row['date'] );
	$row['newsdate'] = strtotime( $row['newsdate'] );
	$row['gast_name'] = stripslashes( $row['gast_name'] );
	$row['gast_email'] = stripslashes( $row['gast_email'] );
	$row['name'] = stripslashes( $row['name'] );
	
	$i ++;
	$s ++;
	
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
				$email = explode( "@", $row['gast_email'], 2 );
				$tpl->set( '{author}', "<script>var em0 = '$email[0]'; document.write('<a href=\"mailto:' + em0 + '@$email[1]\">" . $row['gast_name'] . "</a>');</script>" );
			} else {
				$tpl->set( '{author}', "<a $url_target href=\"$mail_or_url" . $row[gast_email] . "\">" . $row['gast_name'] . "</a>" );
			}
		
		} else {
			$tpl->set( '{author}', $row['gast_name'] );
		}
	} else {
		
		if( $config['allow_alt_url'] == "yes" ) {
			
			$go_page = "href=\"" . $config['http_home_url'] . "user/" . urlencode( $row['name'] ) . "/\"";
		
		} else {
			
			$go_page = "href=\"$PHP_SELF?subaction=userinfo&amp;user=" . urlencode( $row['name'] ) . "\"";
		
		}
		
		$go_page = "onClick=\"return dropdownmenu(this, event, UserMenu('" . htmlspecialchars( $go_page ) . "', '" . $row['user_id'] . "', '" . $member_id['user_group'] . "'), '170px')\" onMouseout=\"delayhidemenu()\"";
		
               if( $config['allow_alt_url'] == "yes" ) $tpl->set( '{author}',  $row['name']  );
               else $tpl->set( '{author}', $row['name'] );
	
	}
	
	if( $is_logged and $member_id['user_group'] == '1' ) $tpl->set( '{ip}', "IP: <a onClick=\"return dropdownmenu(this, event, IPMenu('" . $row['ip'] . "', '" . $lang['ip_info'] . "', '" . $lang['ip_tools'] . "', '" . $lang['ip_ban'] . "'), '190px')\" onMouseout=\"delayhidemenu()\" href=\"http://www.nic.ru/whois/?ip={$row['ip']}\" target=\"_blank\">{$row['ip']}</a>" );
	else $tpl->set( '{ip}', '' );
	
	if( $is_logged and (($member_id['name'] == $row['name'] and $row['is_register'] and $user_group[$member_id['user_group']]['allow_editc']) or $user_group[$member_id['user_group']]['edit_allc']) ) {
		$tpl->set( '[com-edit]', "<a onClick=\"return dropdownmenu(this, event, MenuCommBuild('" . $row['id'] . "'), '170px')\" onMouseout=\"delayhidemenu()\" href=\"" . $config['http_home_url'] . "index.php?do=comments&action=comm_edit&id=" . $row['id'] . "\">" );
		$tpl->set( '[/com-edit]', "</a>" );
		$allow_comments_ajax = true;
	} else
		$tpl->set_block( "'\\[com-edit\\](.*?)\\[/com-edit\\]'si", "" );
	
	if( $is_logged and (($member_id['name'] == $row['name'] and $row['is_register'] and $user_group[$member_id['user_group']]['allow_delc']) or $member_id['user_group'] == '1' or $user_group[$member_id['user_group']]['del_allc']) ) {
		$tpl->set( '[com-del]', "<a href=\"javascript:confirmDelete('" . $config['http_home_url'] . "index.php?do=comments&action=comm_del&id=" . $row['id'] . "&amp;dle_allow_hash=" . $dle_login_hash . "')\">" );
		$tpl->set( '[/com-del]', "</a>" );
	} else
		$tpl->set_block( "'\\[com-del\\](.*?)\\[/com-del\\]'si", "" );
	
	$tpl->set_block( "'\\[fast\\](.*?)\\[/fast\\]'si", "" );
	
	$tpl->set( '{mail}', $row['email'] );
	
	if( date( Ymd, $row['date'] ) == date( Ymd, $_TIME ) ) {
		
		$tpl->set( '{date}', $lang['time_heute'] . langdate( ", H:i", $row['date'] ) );
	
	} elseif( date( Ymd, $row['date'] ) == date( Ymd, ($_TIME - 86400) ) ) {
		
		$tpl->set( '{date}', $lang['time_gestern'] . langdate( ", H:i", $row['date'] ) );
	
	} else {
		
		$tpl->set( '{date}', langdate( $config['timestamp_comment'], $row['date'] ) );
	
	}
	
	$tpl->set( '{comment-id}', $row_count['count'] - $cstart - $s + 1 );
	
	// Обработка дополнительных полей
	if( $xfound ) {
		$xfieldsdata = xfieldsdataload( $row['xprofile'] );
		
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
	
	if( $user_group[$row['user_group']]['icon'] ) $tpl->set( '{group-icon}', "<img src=\"" . $user_group[$row['user_group']]['icon'] . "\" border=\"0\" alt=\"\" />" );
	else $tpl->set( '{group-icon}', "" );
	
	$tpl->set( '{group-name}', $user_group[$row['user_group']]['group_name'] );
	
	$tpl->set( '{news-num}', intval( $row['news_num'] ) );
	$tpl->set( '{comm-num}', intval( $row['comm_num'] ) );
	
	$row['category'] = intval( $row['category'] );
	
	if( $config['allow_alt_url'] == "yes" ) {
		
		if( $row['flag'] and $config['seo_type'] ) {
			
			if( $row['category'] and $config['seo_type'] == 2 ) {
				
				$full_link = $config['http_home_url'] . get_url( $row['category'] ) . "/" . $row['post_id'] . "-" . $row['alt_name'] . ".html";
			
			} else {
				
				$full_link = $config['http_home_url'] . $row['post_id'] . "-" . $row['alt_name'] . ".html";
			
			}
		
		} else {
			
			$full_link = $config['http_home_url'] . date( 'Y/m/d/', $row['newsdate'] ) . $row['alt_name'] . ".html";
		}
	
	} else {
		
		$full_link = $config['http_home_url'] . "index.php?newsid=" . $row['post_id'];
	
	}
	
	$tpl->set( '{news_title}', "<a href=\"" . $full_link . "\">" . stripslashes( $row['title'] ) . "</a>" );
	
	$tpl->set( '{comment}', "<div id='comm-id-" . $row['id'] . "'>" . stripslashes( $row['text'] ) . "</div>" );
	
	if( $user_group[$member_id['user_group']]['allow_hide'] ) $tpl->set_block( "'\[hide\](.*?)\[/hide\]'si", "\\1" );
	else $tpl->set_block( "'\\[hide\\](.*?)\\[/hide\\]'si", "<div class=\"quote\">" . $lang['news_regus'] . "</div>" );
	
	$tpl->compile( 'content' );
}

$tpl->clear();
$db->free( $sql_result );

//####################################################################################################################
//         Навигация по новостям
//####################################################################################################################


$tpl->load_template( 'navigation.tpl' );
//----------------------------------
// Previous link
//----------------------------------
if( $cstart > 0 ) {
	$prev = $cstart / $number;
	$prev_page = $PHP_SELF . "?cstart=" . $prev . "&amp;" . $user_query;
	$tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", "<a href=\"" . $prev_page . "\">\\1</a>" );

} else {
	$tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", "<span>\\1</span>" );
	$no_prev = TRUE;
}

//----------------------------------
// Pages
//----------------------------------
if( $number ) {
	
	$count_all = $row_count['count'];
	
	$enpages_count = @ceil( $count_all / $number );
	$pages = "";
	$cstart = ($cstart / $number) + 1;
	
	if( $enpages_count <= 10 ) {
		
		for($j = 1; $j <= $enpages_count; $j ++) {
			
			if( $j != $cstart ) {
				
				$pages .= "<a href=\"$PHP_SELF?cstart=$j&amp;$user_query\">$j</a> ";
			
			} else {
				
				$pages .= "<span>$j</span> ";
			}
		
		}
	
	} else {
		
		$start = 1;
		$end = 10;
		$nav_prefix = "... ";
		
		if( $cstart > 0 ) {
			
			if( $cstart > 5 ) {
				
				$start = $cstart - 4;
				$end = $start + 8;
				
				if( $end >= $enpages_count ) {
					$start = $enpages_count - 9;
					$end = $enpages_count - 1;
					$nav_prefix = "";
				} else
					$nav_prefix = "... ";
			
			}
		
		}
		
		if( $start >= 2 ) {
			
			$pages .= "<a href=\"$PHP_SELF?cstart=1&amp;$user_query\">1</a> ... ";
		
		}
		
		for($j = $start; $j <= $end; $j ++) {
			
			if( $j != $cstart ) {
				
				$pages .= "<a href=\"$PHP_SELF?cstart=$j&amp;$user_query\">$j</a> ";
			
			} else {
				
				$pages .= "<span>$j</span> ";
			}
		
		}
		
		if( $cstart != $enpages_count ) {
			
			$pages .= $nav_prefix . "<a href=\"$PHP_SELF?cstart={$enpages_count}&amp;$user_query\">{$enpages_count}</a>";
		
		} else
			$pages .= "<span>{$enpages_count}</span> ";
	
	}
	
	$tpl->set( '{pages}', $pages );

}

//----------------------------------
// Next link
//----------------------------------
if( $number < $count_all and $i < $count_all ) {
	
	$next_page = $i / $number + 1;
	$next = $PHP_SELF . "?cstart=" . $next_page . "&amp;" . $user_query;
	$tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", "<a href=\"" . $next . "\">\\1</a>" );

} else {
	$tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", "<span>\\1</span>" );
	$no_next = TRUE;
}

if( ! $no_prev or ! $no_next ) {
	$tpl->compile( 'content' );
}
$tpl->clear();
?>