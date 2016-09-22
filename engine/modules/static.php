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
 Файл: static.php
-----------------------------------------------------
 Назначение: вывод статистических страниц
=====================================================
*/
if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

$name = @$db->safesql( $_GET['page'] );

if( ! $static_result['id'] ) $static_result = $db->super_query( "SELECT * FROM " . PREFIX . "_static WHERE name='$name'" );

if( $static_result['id'] ) {
	
	$db->query( "UPDATE " . PREFIX . "_static set views=views+1 where id='{$static_result['id']}'" );
	
	$static_result['grouplevel'] = explode( ',', $static_result['grouplevel'] );
	
	if( $static_result['date'] ) $_DOCUMENT_DATE = $static_result['date'];
	
	if( $static_result['grouplevel'][0] != "all" and ! in_array( $member_id['user_group'], $static_result['grouplevel'] ) ) {
		msgbox( $lang['all_err_1'], $lang['static_denied'] );
	} else {
		
		$template = stripslashes( $static_result['template'] );
		$static_descr = stripslashes( strip_tags( $static_result['descr'] ) );
		
		if( $static_result['metakeys'] == '' and $static_result['metadescr'] == '' ) create_keywords( $template );
		else {
			$metatags['keywords'] = $static_result['metakeys'];
			$metatags['description'] = $static_result['metadescr'];
		}
		
		if( 1 == 1) {
			
			if( $view_template == "print" ) $tpl->load_template( 'static_print.tpl' );
			elseif( $static_result['tpl'] != '' ) $tpl->load_template( $static_result['tpl'] . '.tpl' );
			else $tpl->load_template( 'static.tpl' );
			
			if( strpos( $tpl->copy_template, "[group=" ) !== false ) {
				$tpl->copy_template = preg_replace( "#\\[group=(.+?)\\](.*?)\\[/group\\]#ies", "check_group('\\1', '\\2')", $tpl->copy_template );
			}
			
			if( strpos( $tpl->copy_template, "[not-group=" ) !== false ) {
				$tpl->copy_template = preg_replace( "#\\[not-group=(.+?)\\](.*?)\\[/not-group\\]#ies", "check_group('\\1', '\\2', false)", $tpl->copy_template );
			}
			
			if( strpos( $tpl->copy_template, "{custom" ) !== false ) {
				
				$tpl->copy_template = preg_replace( "#\\{custom category=['\"](.+?)['\"] template=['\"](.+?)['\"] aviable=['\"](.+?)['\"] from=['\"](.+?)['\"] limit=['\"](.+?)['\"] cache=['\"](.+?)['\"]\\}#ies", "custom_print('\\1', '\\2', '\\3', '\\4', '\\5', '\\6', '{$do}')", $tpl->copy_template );
			
			}
			
			if( ! $news_page ) $news_page = 1;
			
			if( $view_template == "print" ) {
				
				$template = str_replace( "{PAGEBREAK}", "", $template );
				$template = str_replace( "{pages}", "", $template );
				$template = preg_replace( "'\[PAGE=(.*?)\](.*?)\[/PAGE\]'si", "", $template );
			
			} else {
				
				$news_seiten = explode( "{PAGEBREAK}", $template );
				$anzahl_seiten = count( $news_seiten );
				
				if( $news_page <= 0 or $news_page > $anzahl_seiten ) {
					$news_page = 1;
				}
				
				$template = $news_seiten[$news_page - 1];
				
				$template = preg_replace( '#(\A[\s]*<br[^>]*>[\s]*|<br[^>]*>[\s]*\Z)#is', '', $template ); // remove <br/> at end of string
				

				$news_seiten = "";
				unset( $news_seiten );
				
				if( $anzahl_seiten > 1 ) {
					
					if( $news_page < $anzahl_seiten ) {
						$pages = $news_page + 1;
						if( $config['allow_alt_url'] == "yes" ) {
							$nextpage = " | <a href=\"" . $config['http_home_url'] . "page," . $pages . "," . $static_result['name'] . ".html\">" . $lang['news_next'] . "</a>";
						} else {
							$nextpage = " | <a href=\"$PHP_SELF?do=static&page=" . $static_result['name'] . "&news_page=" . $pages . "\">" . $lang['news_next'] . "</a>";
						}
					}
					
					if( $news_page > 1 ) {
						$pages = $news_page - 1;
						if( $config['allow_alt_url'] == "yes" ) {
							$prevpage = "<a href=\"" . $config['http_home_url'] . "page," . $pages . "," . $static_result['name'] . ".html\">" . $lang['news_prev'] . "</a> | ";
						} else {
							$prevpage = "<a href=\"$PHP_SELF?do=static&page=" . $static_result['name'] . "&news_page=" . $pages . "\">" . $lang['news_prev'] . "</a> | ";
						}
					}
					
					$tpl->set( '{pages}', $prevpage . $lang['news_site'] . " " . $news_page . $lang['news_iz'] . $anzahl_seiten . $nextpage );
					
					if( $config['allow_alt_url'] == "yes" ) {
						$replacepage = "<a href=\"" . $config['http_home_url'] . "page," . "\\1" . "," . $static_result['name'] . ".html\">\\2</a>";
					} else {
						$replacepage = "<a href=\"$PHP_SELF?do=static&page=" . $static_result['name'] . "&news_page=\\1\">\\2</a>";
					}
					
					$template = preg_replace( "'\[PAGE=(.*?)\](.*?)\[/PAGE\]'si", $replacepage, $template );
				
				} else {
					
					$tpl->set( '{pages}', '' );
					$template = preg_replace( "'\[PAGE=(.*?)\](.*?)\[/PAGE\]'si", "", $template );
				
				}
			
			}
			
			if( $user_group[$member_id['user_group']]['allow_hide'] ) $tpl->set_block( "'\[hide\](.*?)\[/hide\]'si", "\\1" );
			else $tpl->set_block( "'\\[hide\\](.*?)\\[/hide\\]'si", "<div class=\"quote\">" . $lang['news_regus'] . "</div>" );
			
			if( $config['allow_alt_url'] == "yes" ) $print_link = $config['http_home_url'] . "print:" . $static_result['name'] . ".html";
			else $print_link = $config['http_home_url'] . "engine/print.php?do=static&amp;page=" . $static_result['name'];
			
			$tpl->set( '{description}', $static_descr );
			$tpl->set( '{static}', $template );
			$tpl->set( '{views}', $static_result['views'] );
			$tpl->set( '[print-link]', "<a href=\"" . $print_link . "\">" );
			$tpl->set( '[/print-link]', "</a>" );
			
			if( $_GET['page'] == "dle-rules-page" ) if( $do != "register" ) {
				
				$tpl->set( '{ACCEPT-DECLINE}', "" );
			
			} else {
				
				$tpl->set( '{ACCEPT-DECLINE}', "<form  method=\"post\" name=\"registration\" id=\"registration\" action=\"" . $config['http_home_url'] . "index.php?do=register\"><input type=\"submit\" class=\"bbcodes\" value=\"{$lang['rules_accept']}\" />&nbsp;&nbsp;&nbsp;<input type=\"button\" class=\"bbcodes\" value=\"{$lang['rules_decline']}\" onclick=\"history.go(-1); return false;\" /><input name=\"dle_rules_accept\" type=\"hidden\" id=\"dle_rules_accept\" value=\"yes\" /></form>" );
			
			}
			
			$tpl->compile( 'content' );
			$tpl->clear();
		
		} else
			$tpl->result['content'] = $template;
	}
	
	if( $config['files_allow'] == "yes" ) if( strpos( $tpl->result['content'], "[attachment=" ) !== false ) {
		
		$tpl->result['content'] = show_attach( $tpl->result['content'], $static_result['id'], true );
	
	}

} else {
	
	@header( "HTTP/1.0 404 Not Found" );
	msgbox( $lang['all_err_1'], $lang['news_page_err'] );

}
?>