<?php
/*
=====================================================
 DataLife Engine Nulled by M.I.D-Team
-----------------------------------------------------
 http://www.mid-team.ws/
-----------------------------------------------------
 Copyright (c) 2004,2009 SoftNews Media Group
=====================================================
 ������ ��� ������� ���������� �������
=====================================================
 ����: show.full.php
-----------------------------------------------------
 ����������: �������� ������ ������� � ������������
=====================================================
*/
if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

if( ! $CN_HALT ) {
	
	$sql_result = $db->query( $sql_news );
	
	$allow_list = explode( ',', $user_group[$member_id['user_group']]['allow_cats'] );
	
	$perm = 1;
	$i = 0;
	$news_found = false;
	
	while ( $row = $db->get_row( $sql_result ) ) {
		
		if( $i ) break;
		
		$xfields = xfieldsload();
		$options = news_permission( $row['access'] );
		
		if( $row['votes'] and $view_template != "print" ) include_once ENGINE_DIR . '/modules/poll.php';
		
		if( ! $row['category'] ) {
			$my_cat = "---";
			$my_cat_link = "---";
		} else {
			
			$my_cat = array ();
			$my_cat_link = array ();
			$cat_list = explode( ',', $row['category'] );
			
			if( count( $cat_list ) == 1 ) {
				
				if( $allow_list[0] != "all" and ! in_array( $cat_list[0], $allow_list ) ) $perm = 0;
				
				$my_cat[] = $cat_info[$cat_list[0]]['name'];
				
				$my_cat_link = get_categories( $cat_list[0] );
			
			} else {
				
				foreach ( $cat_list as $element ) {
					
					if( $allow_list[0] != "all" and ! in_array( $element, $allow_list ) ) $perm = 0;
					
					if( $element ) {
						$my_cat[] = $cat_info[$element]['name'];
						if( $config['ajax'] ) $go_page = "onclick=\"DlePage('do=cat&category={$cat_info[$element]['alt_name']}'); return false;\" ";
						else $go_page = "";
						if( $config['allow_alt_url'] == "yes" ) $my_cat_link[] = "<a {$go_page}href=\"" . $config['http_home_url'] . get_url( $element ) . "/\">{$cat_info[$element]['name']}</a>";
						else $my_cat_link[] = "<a {$go_page}href=\"$PHP_SELF?do=cat&amp;category={$cat_info[$element]['alt_name']}\">{$cat_info[$element]['name']}</a>";
					}
				}
				
				$my_cat_link = implode( ', ', $my_cat_link );
			}
			
			$my_cat = implode( ', ', $my_cat );
		}
		
		$row['category'] = intval( $row['category'] );
		$category_id = $row['category'];
		
		if( isset( $view_template ) and $view_template == "print" ) $tpl->load_template( 'print.tpl' );
		elseif( $category_id and $cat_info[$category_id]['full_tpl'] != '' ) $tpl->load_template( $cat_info[$category_id]['full_tpl'] . '.tpl' );
		else $tpl->load_template( 'fullstory.tpl' );
		
		if( $options[$member_id['user_group']] and $options[$member_id['user_group']] != 3 ) $perm = 1;
		if( $options[$member_id['user_group']] == 3 ) $perm = 0;
		
		if( $options[$member_id['user_group']] == 1 ) $user_group[$member_id['user_group']]['allow_addc'] = 0;
		if( $options[$member_id['user_group']] == 2 ) $user_group[$member_id['user_group']]['allow_addc'] = 1;
		
		if( ! $row['approve'] and $member_id['name'] != $row['autor'] and $member_id['user_group'] != '1' ) $perm = 0;
		if( ! $row['approve'] ) $allow_comments = false;
		
		if( ! $perm ) break;
		
		if( $config['allow_read_count'] == "yes" ) {
			if( $config['cache_count'] ) $db->query( "INSERT INTO " . PREFIX . "_views (news_id) VALUES ('{$row['id']}')" );
			else $db->query( "UPDATE " . PREFIX . "_post set news_read=news_read+1 where id='{$row['id']}'" );
		}
		
		$news_found = TRUE;
		$row['date'] = strtotime( $row['date'] );
		
		if( (strlen( $row['full_story'] ) < 13) and (strpos( $tpl->copy_template, "{short-story}" ) === false) ) {
			$row['full_story'] = $row['short_story'];
		}
		
		if( ! $news_page ) {
			$news_page = 1;
		}
		;
		
		$news_seiten = explode( "{PAGEBREAK}", $row['full_story'] );
		$anzahl_seiten = count( $news_seiten );
		
		if( $news_page <= 0 or $news_page > $anzahl_seiten ) {
			
			$news_page = 1;
		}
		
		if( $config['allow_alt_url'] == "yes" ) {
			
			if( $row['flag'] and $config['seo_type'] ) {
				
				if( $category_id and $config['seo_type'] == 2 ) {
					
					$full_link = $config['http_home_url'] . get_url( $category_id ) . "/" . $row['id'] . "-" . $row['alt_name'] . ".html";
					$print_link = $config['http_home_url'] . get_url( $category_id ) . "/print:page,1," . $row['id'] . "-" . $row['alt_name'] . ".html";
					$short_link = $config['http_home_url'] . get_url( $category_id ) . "/";
					$row['alt_name'] = $row['id'] . "-" . $row['alt_name'];
					$link_page = $config['http_home_url'] . get_url( $category_id ) . "/" . 'page,' . $news_page . ',';
					$news_name = $row['alt_name'];
				
				} else {
					
					$full_link = $config['http_home_url'] . $row['id'] . "-" . $row['alt_name'] . ".html";
					$print_link = $config['http_home_url'] . "print:page,1," . $row['id'] . "-" . $row['alt_name'] . ".html";
					$short_link = $config['http_home_url'];
					$row['alt_name'] = $row['id'] . "-" . $row['alt_name'];
					$link_page = $config['http_home_url'] . 'page,' . $news_page . ',';
					$news_name = $row['alt_name'];
				
				}
			
			} else {
				
				$full_link = $config['http_home_url'] . date( 'Y/m/d/', $row['date'] ) . $row['alt_name'] . ".html";
				$print_link = $config['http_home_url'] . date( 'Y/m/d/', $row['date'] ) . "print:page,1," . $row['alt_name'] . ".html";
				$short_link = $config['http_home_url'] . date( 'Y/m/d/', $row['date'] );
				$link_page = $config['http_home_url'] . date( 'Y/m/d/', $row['date'] ) . 'page,' . $news_page . ',';
				$news_name = $row['alt_name'];
			
			}
		
		} else {
			
			$full_link = $config['http_home_url'] . "index.php?newsid=" . $row['id'];
			$print_link = $config['http_home_url'] . "engine/print.php?newsid=" . $row['id'];
			$short_link = "";
		
		}
		
		$i ++;
		
		//
		// ��������� �������
		//
		if( $view_template == "print" ) {






function clears_host ($url) {
    $value = str_replace ('http://', '', $url);
    $value = str_replace ('www.', '', $value);
    $value = explode ('/', $value);
    $value = reset ($value);

    return strtolower ($value);
}






			
			$row['full_story'] = str_replace( "{PAGEBREAK}", "", $row['full_story'] );
			$row['full_story'] = str_replace( "{pages}", "", $row['full_story'] );
			$row['full_story'] = preg_replace( "'\[PAGE=(.*?)\](.*?)\[/PAGE\]'si", "", $row['full_story'] );
		
		} else {
			
			$row['full_story'] = $news_seiten[$news_page - 1];
			
			$row['full_story'] = preg_replace( '#(\A[\s]*<br[^>]*>[\s]*|<br[^>]*>[\s]*\Z)#is', '', $row['full_story'] ); // remove <br/> at end of string
			$news_seiten = "";
			unset( $news_seiten );
			
			if( $anzahl_seiten > 1 ) {
				
				if( $news_page < $anzahl_seiten ) {
					$pages = $news_page + 1;
					
					if( $config['allow_alt_url'] == "yes" ) {
						$nextpage = " | <a href=\"" . $short_link . "page," . $pages . "," . $row['alt_name'] . ".html\">" . $lang['news_next'] . "</a>";
					} else {
						$nextpage = " | <a href=\"$PHP_SELF?newsid=" . $row['id'] . "&amp;news_page=" . $pages . "\">" . $lang['news_next'] . "</a>";
					}
				}
				
				if( $news_page > 1 ) {
					$pages = $news_page - 1;
					
					if( $config['allow_alt_url'] == "yes" ) {
						$prevpage = "<a href=\"" . $short_link . "page," . $pages . "," . $row['alt_name'] . ".html\">" . $lang['news_prev'] . "</a> | ";
					} else {
						$prevpage = "<a href=\"$PHP_SELF?newsid=" . $row['id'] . "&amp;news_page=" . $pages . "\">" . $lang['news_prev'] . "</a> | ";
					}
				}
				
				$tpl->set( '{pages}', $prevpage . $lang['news_site'] . " " . $news_page . $lang['news_iz'] . $anzahl_seiten . $nextpage );
				
				if( $config['allow_alt_url'] == "yes" ) {
					
					$replacepage = "<a href=\"" . $short_link . "page," . "\\1" . "," . $row['alt_name'] . ".html\">\\2</a>";
				
				} else {
					
					$replacepage = "<a href=\"$PHP_SELF?newsid=" . $row['id'] . "&amp;news_page=\\1\">\\2</a>";
				}
				
				$row['full_story'] = preg_replace( "'\[PAGE=(.*?)\](.*?)\[/PAGE\]'si", $replacepage, $row['full_story'] );
			
			} else {
				
				$tpl->set( '{pages}', '' );
				$row['full_story'] = preg_replace( "'\[PAGE=(.*?)\](.*?)\[/PAGE\]'si", "", $row['full_story'] );
			}
		}
		







		$metatags['title'] = stripslashes( $row['title'] );
		$comments_num = $row['comm_num'];
		
		$news_find = array ('{comments-num}' => $comments_num, '{views}' => $row['news_read'], '{category}' => $my_cat, '{link-category}' => $my_cat_link, '{news-id}' => $row['id'] );
		
		if( date( Ymd, $row['date'] ) == date( Ymd, $_TIME ) ) {
			
			$tpl->set( '{date}', $lang['time_heute'] . langdate( ", H:i", $row['date'] ) );
		
		} elseif( date( Ymd, $row['date'] ) == date( Ymd, ($_TIME - 86400) ) ) {
			
			$tpl->set( '{date}', $lang['time_gestern'] . langdate( ", H:i", $row['date'] ) );
		
		} else {
			
			$tpl->set( '{date}', langdate( $config['timestamp_active'], $row['date'] ) );
		
		}
		
		if( $row['editdate'] ) $_DOCUMENT_DATE = $row['editdate'];
		else $_DOCUMENT_DATE = $row['date'];
		
		if( $row['view_edit'] and $row['editdate'] ) {
			
			if( date( Ymd, $row['editdate'] ) == date( Ymd, $_TIME ) ) {
				
				$tpl->set( '{edit-date}', $lang['time_heute'] . langdate( ", H:i", $row['editdate'] ) );
			
			} elseif( date( Ymd, $row['editdate'] ) == date( Ymd, ($_TIME - 86400) ) ) {
				
				$tpl->set( '{edit-date}', $lang['time_gestern'] . langdate( ", H:i", $row['editdate'] ) );
			
			} else {
				
				$tpl->set( '{edit-date}', langdate( $config['timestamp_active'], $row['editdate'] ) );
			
			}
			
			$tpl->set( '{editor}', $row['editor'] );
			$tpl->set( '{edit-reason}', $row['reason'] );
			
			if( $row['reason'] ) {
				
				$tpl->set( '[edit-reason]', "" );
				$tpl->set( '[/edit-reason]', "" );
			
			} else
				$tpl->set_block( "'\\[edit-reason\\](.*?)\\[/edit-reason\\]'si", "" );
			
			$tpl->set( '[edit-date]', "" );
			$tpl->set( '[/edit-date]', "" );
		
		} else {
			
			$tpl->set( '{edit-date}', "" );
			$tpl->set( '{editor}', "" );
			$tpl->set( '{edit-reason}', "" );
			$tpl->set_block( "'\\[edit-date\\](.*?)\\[/edit-date\\]'si", "" );
			$tpl->set_block( "'\\[edit-reason\\](.*?)\\[/edit-reason\\]'si", "" );
		}
		
		if( $config['allow_tags'] and $row['tags'] ) {
			
			$tpl->set( '[tags]', "" );
			$tpl->set( '[/tags]', "" );
			
			$tags = array ();
			
			$row['tags'] = explode( ",", $row['tags'] );
			
			foreach ( $row['tags'] as $value ) {
				
				$value = trim( $value );
				
				$go_page = ($config['ajax']) ? "onclick=\"DlePage('do=tags&amp;tag=" . urlencode( $value ) . "'); return false;\" " : "";
				
				if( $config['allow_alt_url'] == "yes" ) $tags[] = "<a {$go_page} href=\"" . $config['http_home_url'] . "tags/" . urlencode( $value ) . "/\">" . $value . "</a>";
				else $tags[] = "<a {$go_page} href=\"$PHP_SELF?do=tags&amp;tag=" . urlencode( $value ) . "\">" . $value . "</a>";
			
			}
			
			$tpl->set( '{tags}', implode( ", ", $tags ) );
		
		} else {
			
			$tpl->set_block( "'\\[tags\\](.*?)\\[/tags\\]'si", "" );
			$tpl->set( '{tags}', "" );
		
		}
		
		$tpl->set( '', $news_find );
		
		if( $cat_info[$row['category']]['icon'] ) {
			
			$tpl->set( '{category-icon}', $cat_info[$row['category']]['icon'] );
		
		} else {
			
			$tpl->set( '{category-icon}', "{THEME}/dleimages/no_icon.gif" );
		
		}
		
		// ������ �� ������ ��� ������
		$tpl->set( '[print-link]', "<a href=\"" . $print_link . "\">" );
		$tpl->set( '[/print-link]', "</a>" );
		// ������ �� ������ ��� ������
		

		if( $row['allow_rate'] ) $tpl->set( '{rating}', ShowRating( $row['id'], $row['rating'], $row['vote_num'], $user_group[$member_id['user_group']]['allow_rating'] ) );
		else $tpl->set( '{rating}', "" );
		
		if( $config['ajax'] ) {
			
			$go_page = "onclick=\"DlePage(\'subaction=userinfo&user=" . urlencode( $row['autor'] ) . "\'); return false;\" ";
			$news_page = "onclick=\"DlePage(\'subaction=allnews&user=" . urlencode( $row['autor'] ) . "\'); return false;\" ";
		
		} else {
			$go_page = "";
			$news_page = "";
		}
		
		if( $config['allow_alt_url'] == "yes" ) {
			
			$go_page .= "href=\"" . $config['http_home_url'] . "user/" . urlencode( $row['autor'] ) . "/\"";
			$news_page .= "href=\"" . $config['http_home_url'] . "user/" . urlencode( $row['autor'] ) . "/news/\"";
		
		} else {
			
			$go_page .= "href=\"$PHP_SELF?subaction=userinfo&amp;user=" . urlencode( $row['autor'] ) . "\"";
			$news_page .= "href=\"$PHP_SELF?subaction=allnews&amp;user=" . urlencode( $row['autor'] ) . "\"";
		
		}
		
		$go_page = "onclick=\"return dropdownmenu(this, event, UserNewsMenu('" . htmlspecialchars( $go_page ) . "', '" . htmlspecialchars( $news_page ) . "','" . urlencode( $row['autor'] ) . "', '" . $member_id['user_group'] . "'), '170px')\" onmouseout=\"delayhidemenu()\"";
		
		if( $config['allow_alt_url'] == "yes" ) $tpl->set( '{author}', "<a {$go_page} href=\"" . $config['http_home_url'] . "user/" . urlencode( $row['autor'] ) . "/\">" . $row['autor'] . "</a>" );
		else $tpl->set( '{author}', "<a {$go_page} href=\"$PHP_SELF?subaction=userinfo&amp;user=" . urlencode( $row['autor'] ) . "\">" . $row['autor'] . "</a>" );
		
		if( strpos( $_SERVER['REQUEST_URI'], "pages.php" ) !== false ) {
			
			$_SESSION['referrer'] = $full_link;
		
		} else
			$_SESSION['referrer'] = $_SERVER['REQUEST_URI'];
		
		$tpl->set( '[full-link]', "<a href=\"" . $full_link . "\">" );
		$tpl->set( '[/full-link]', "</a>" );
		
		$tpl->set( '{full-link}', $full_link );
		
		if( $row['allow_comm'] ) {
			
			$tpl->set( '[com-link]', "<a href=\"" . $full_link . "\">" );
			$tpl->set( '[/com-link]', "</a>" );
		
		} else
			$tpl->set_block( "'\\[com-link\\](.*?)\\[/com-link\\]'si", "" );
		
		if( ! $row['approve'] and ($member_id['name'] == $row['autor'] and ! $user_group[$member_id['user_group']]['allow_all_edit']) ) {
			$tpl->set( '[edit]', "<a href=\"" . $config['http_home_url'] . "index.php?do=addnews&amp;id=" . $row['id'] . "\" >" );
			$tpl->set( '[/edit]', "</a>" );
			if( $config['allow_quick_wysiwyg'] ) $allow_comments_ajax = true;
		} elseif( $is_logged and (($member_id['name'] == $row['autor'] and $user_group[$member_id['user_group']]['allow_edit']) or $user_group[$member_id['user_group']]['allow_all_edit']) ) {
			$tpl->set( '[edit]', "<a onclick=\"return dropdownmenu(this, event, MenuNewsBuild('" . $row['id'] . "', 'full'), '170px')\" href=\"" . $config['http_home_url'] . $config['admin_path'] . "?mod=editnews&amp;action=editnews&amp;id=" . $row['id'] . "\" target = \"_blank\" >" );
			$tpl->set( '[/edit]', "</a>" );
			if( $config['allow_quick_wysiwyg'] ) $allow_comments_ajax = true;
		} else
			$tpl->set_block( "'\\[edit\\](.*?)\\[/edit\\]'si", "" );
		
		if( $config['related_news'] ) {
			
			if( $config['allow_cache'] != "yes" ) {
				$config['allow_cache'] = "yes";
				$revert_cache = true;
			} else
				$revert_cache = false;
			
			$buffer = dle_cache( "related", $row['id'] );
			
			if( $buffer === FALSE ) {
				
				if( strlen( $row['full_story'] ) < strlen( $row['short_story'] ) ) $body = $row['short_story'];
				else $body = $row['full_story'];
				
				$body = $db->safesql( strip_tags( stripslashes( $metatags['title'] . " " . $body ) ) );
				
				$config['related_number'] = intval( $config['related_number'] );
				if( $config['related_number'] < 1 ) $config['related_number'] = 5;
				
				$db->query( "SELECT id, title, date, category, alt_name, flag FROM " . PREFIX . "_post WHERE MATCH (title, short_story, full_story, xfields) AGAINST ('$body') AND id != " . $row['id'] . " AND approve='1'" . $where_date . " LIMIT " . $config['related_number'] );
				
				while ( $related = $db->get_row() ) {
					
					$related['date'] = strtotime( $related['date'] );
					$related['category'] = intval( $related['category'] );
					
					if( strlen( $related['title'] ) > 75 ) $related['title'] = substr( $related['title'], 0, 75 ) . " ...";
					
					if( $config['allow_alt_url'] == "yes" ) {
						
						if( $related['flag'] and $config['seo_type'] ) {
							
							if( $related['category'] and $config['seo_type'] == 2 ) {
								
								$full_link = $config['http_home_url'] . get_url( $related['category'] ) . "/" . $related['id'] . "-" . $related['alt_name'] . ".html";
							
							} else {
								
								$full_link = $config['http_home_url'] . $related['id'] . "-" . $related['alt_name'] . ".html";
							
							}
						
						} else {
							
							$full_link = $config['http_home_url'] . date( 'Y/m/d/', $related['date'] ) . $related['alt_name'] . ".html";
						}
					
					} else {
						
						$full_link = $config['http_home_url'] . "index.php?newsid=" . $related['id'];
					
					}
					
					$buffer .= "<li><a href=\"" . $full_link . "\">" . stripslashes( $related['title'] ) . "</a></li>";
				
				}
				
				$db->free();
				create_cache( "related", $buffer, $row['id'] );
			}
			
			$tpl->set( '{related-news}', $buffer );
			
			if( $revert_cache ) $config['allow_cache'] = "no";
		
		}
		
		if( $is_logged ) {
			
			$fav_arr = explode( ',', $member_id['favorites'] );
			
			if( ! in_array( $row['id'], $fav_arr ) ) $tpl->set( '{favorites}', "<a id=\"fav-id-" . $row['id'] . "\" href=\"$PHP_SELF?do=favorites&amp;doaction=add&amp;id=" . $row['id'] . "\"><img src=\"" . $config['http_home_url'] . "templates/{$config['skin']}/dleimages/plus_fav.gif\" onclick=\"doFavorites('" . $row['id'] . "', 'plus'); return false;\" title=\"" . $lang['news_addfav'] . "\" align=\"absmiddle\" border=\"0\" alt=\"\" /></a>" );
			else $tpl->set( '{favorites}', "<a id=\"fav-id-" . $row['id'] . "\" href=\"$PHP_SELF?do=favorites&amp;doaction=del&amp;id=" . $row['id'] . "\"><img src=\"" . $config['http_home_url'] . "templates/{$config['skin']}/dleimages/minus_fav.gif\" onclick=\"doFavorites('" . $row['id'] . "', 'minus'); return false;\" title=\"" . $lang['news_minfav'] . "\" align=\"absmiddle\" border=\"0\" alt=\"\" /></a>" );
		
		} else
			$tpl->set( '{favorites}', "" );
		
		if( strpos( $tpl->copy_template, "[group=" ) !== false ) {
			$tpl->copy_template = preg_replace( "#\\[group=(.+?)\\](.*?)\\[/group\\]#ies", "check_group('\\1', '\\2')", $tpl->copy_template );
		}
		
		if( strpos( $tpl->copy_template, "[not-group=" ) !== false ) {
			$tpl->copy_template = preg_replace( "#\\[not-group=(.+?)\\](.*?)\\[/not-group\\]#ies", "check_group('\\1', '\\2', false)", $tpl->copy_template );
		}
		
		if( $row['votes'] ) $tpl->set( '{poll}', $tpl->result['poll'] );
		else $tpl->set( '{poll}', '' );
		
		if( $config['allow_banner'] ) include_once ENGINE_DIR . '/modules/banners.php';
		
		if( count( $banners ) and $config['allow_banner'] ) {
			
			foreach ( $banners as $name => $value ) {
				$tpl->copy_template = str_replace( "{banner_" . $name . "}", $value, $tpl->copy_template );
			}
		}
		
		$tpl->set_block( "'{banner_(.*?)}'si", "" );
		
		if( strpos( $tpl->copy_template, "[category=" ) !== false ) {
			$tpl->copy_template = preg_replace( "#\\[category=(.+?)\\](.*?)\\[/category\\]#ies", "check_category('\\1', '\\2', '{$row['category']}')", $tpl->copy_template );
		}
		
		if( strpos( $tpl->copy_template, "[not-category=" ) !== false ) {
			$tpl->copy_template = preg_replace( "#\\[not-category=(.+?)\\](.*?)\\[/not-category\\]#ies", "check_category('\\1', '\\2', '{$row['category']}', false)", $tpl->copy_template );
		}
		
		$tpl->set( '{title}', $metatags['title'] );

		if ($smartphone_detected AND !$config['allow_smart_images']) {

			$row['short_story'] = preg_replace( "#<!--TBegin-->(.+?)<!--TEnd-->#is", "", $row['short_story'] );
			$row['short_story'] = preg_replace( "#<img(.+?)>#is", "", $row['short_story'] );
			$row['full_story'] = preg_replace( "#<!--TBegin-->(.+?)<!--TEnd-->#is", "", $row['full_story'] );
			$row['full_story'] = preg_replace( "#<img(.+?)>#is", "", $row['full_story'] );

		}

		$tpl->set( '{short-story}', stripslashes( $row['short_story'] ) );
		$tpl->set( '{full-story}', stripslashes( "<div id='news-id-" . $row['id'] . "'>" . $row['full_story'] . "</div>" ) );
		
		if( $user_group[$member_id['user_group']]['allow_hide'] ) $tpl->set_block( "'\[hide\](.*?)\[/hide\]'si", "\\1" );
		else $tpl->set_block( "'\\[hide\\](.*?)\\[/hide\\]'si", "<div class=\"quote\">" . $lang['news_regus'] . "</div>" );
		
		if( $row['keywords'] == '' and $row['descr'] == '' ) create_keywords( $row['short_story'] . $row['full_story'] );
		else {
			$metatags['keywords'] = $row['keywords'];
			$metatags['description'] = $row['descr'];
		}
		
		if( strpos( $tpl->copy_template, "[xfvalue_" ) !== false ) {
			
			$xfieldsdata = xfieldsdataload( $row['xfields'] );
			
			foreach ( $xfields as $value ) {
				$preg_safe_name = preg_quote( $value[0], "'" );
				
				if( empty( $xfieldsdata[$value[0]] ) ) {
					$tpl->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "", $tpl->copy_template );
				} else {
					$tpl->copy_template = preg_replace( "'\\[xfgiven_{$preg_safe_name}\\](.*?)\\[/xfgiven_{$preg_safe_name}\\]'is", "\\1", $tpl->copy_template );
				}
				
				$tpl->copy_template = str_replace( "[xfvalue_{$preg_safe_name}]", stripslashes( $xfieldsdata[$value[0]] ), $tpl->copy_template );
			}
		}
		
		$tpl->compile( 'content' );
		
		$news_id = $row['id'];
		$allow_add = $row['allow_comm'];
		
		if( isset( $view_template ) ) $allow_add = false;
	
	}
	
	$tpl->clear();
	$db->free( $sql_result );
	
	if( $config['files_allow'] == "yes" ) if( strpos( $tpl->result['content'], "[attachment=" ) !== false ) {
		$tpl->result['content'] = show_attach( $tpl->result['content'], $news_id );
	}
	
	if( ! $news_found and ! $perm ) msgbox( $lang['all_err_1'], "<b>{$user_group[$member_id['user_group']]['group_name']}</b> " . $lang['news_err_28'] );
	elseif( ! $news_found ) {
		@header( "HTTP/1.0 404 Not Found" );
		msgbox( $lang['all_err_1'], $lang['news_err_12'] );
	}

}
//####################################################################################################################
//		 �������� ������������
//####################################################################################################################
if( $allow_comments and $allow_add and $CN_HALT != TRUE ) {
	
	if( $comments_num > 0 ) {
		
		$config['comm_nummers'] = intval( $config['comm_nummers'] );
		$cstart = intval( $_GET['cstart'] );
		
		if( $cstart ) {
			$cstart = $cstart - 1;
			$cstart = $cstart * $config['comm_nummers'];
		} else
			$cstart = 0;
		
		if( $config['comm_msort'] == "" ) $config['comm_msort'] = "ASC";
		
		$i = 0;
		
		$tpl->load_template( 'comments.tpl' );
		
		if( strpos( $tpl->copy_template, "[xfvalue_" ) !== false ) $xfound = true;
		else $xfound = false;
		
		if( $xfound ) $xfields = xfieldsload( true );
		
		$tpl->copy_template = "<a name=\"comment\"></a>" . $tpl->copy_template;
		
		if( $config['allow_cmod'] ) $where_approve = " AND " . PREFIX . "_comments.approve='1'";
		else $where_approve = "";
		
		$sql_result = $db->query( "SELECT " . PREFIX . "_comments.id, post_id, " . PREFIX . "_comments.user_id, date, autor as gast_name, " . PREFIX . "_comments.email as gast_email, text, ip, is_register, name, " . USERPREFIX . "_users.email, news_num, comm_num, user_group, reg_date, signature, foto, fullname, land, icq, xfields FROM " . PREFIX . "_comments LEFT JOIN " . USERPREFIX . "_users ON " . PREFIX . "_comments.user_id=" . USERPREFIX . "_users.user_id WHERE " . PREFIX . "_comments.post_id = '$news_id'" . $where_approve . " ORDER BY date " . $config['comm_msort'] . " LIMIT " . $cstart . "," . $config['comm_nummers'] );
		
		while ( $row = $db->get_row( $sql_result ) ) {
			$i ++;
			
			$row['date'] = strtotime( $row['date'] );
			
			$row['gast_name'] = stripslashes( $row['gast_name'] );
			$row['gast_email'] = stripslashes( $row['gast_email'] );
			$row['name'] = stripslashes( $row['name'] );
			
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
				
				$go_page = "onclick=\"return dropdownmenu(this, event, UserMenu('" . htmlspecialchars( $go_page ) . "', '" . $row['user_id'] . "', '" . $member_id['user_group'] . "'), '170px')\" onmouseout=\"delayhidemenu()\"";
				
			#	if( $config['allow_alt_url'] == "yes" ) $tpl->set( '{author}', "<a {$go_page} href=\"" . $config['http_home_url'] . "user/" . urlencode( $row['name'] ) . "/\">" . $row['name'] . "</a>" );
			#	else $tpl->set( '{author}', "<a {$go_page} href=\"$PHP_SELF?subaction=userinfo&amp;user=" . urlencode( $row['name'] ) . "\">" . $row['name'] . "</a>" );
			
			
			 if( $config['allow_alt_url'] == "yes" ) $tpl->set( '{author}',  $row['name']  );
			else $tpl->set( '{author}', $row['name'] );
			
			}
			
			if( $is_logged and $member_id['user_group'] == '1' ) $tpl->set( '{ip}', "IP: <a onclick=\"return dropdownmenu(this, event, IPMenu('" . $row['ip'] . "', '" . $lang['ip_info'] . "', '" . $lang['ip_tools'] . "', '" . $lang['ip_ban'] . "'), '190px')\" onmouseout=\"delayhidemenu()\" href=\"http://www.nic.ru/whois/?ip={$row['ip']}\" target=\"_blank\">{$row['ip']}</a>" );
			else $tpl->set( '{ip}', '' );
			
			if( $is_logged and (($member_id['name'] == $row['name'] and $row['is_register'] and $user_group[$member_id['user_group']]['allow_editc']) or $user_group[$member_id['user_group']]['edit_allc']) ) {
				$tpl->set( '[com-edit]', "<a onclick=\"return dropdownmenu(this, event, MenuCommBuild('" . $row['id'] . "'), '170px')\" onmouseout=\"delayhidemenu()\" href=\"" . $config['http_home_url'] . "index.php?do=comments&amp;action=comm_edit&amp;id=" . $row['id'] . "\">" );
				$tpl->set( '[/com-edit]', "</a>" );
				$allow_comments_ajax = true;
			} else
				$tpl->set_block( "'\\[com-edit\\](.*?)\\[/com-edit\\]'si", "" );
			
			if( $is_logged and (($member_id['name'] == $row['name'] and $row['is_register'] and $user_group[$member_id['user_group']]['allow_delc']) or $member_id['user_group'] == '1' or $user_group[$member_id['user_group']]['del_allc']) ) {
				$tpl->set( '[com-del]', "<a href=\"javascript:confirmDelete('" . $config['http_home_url'] . "index.php?do=comments&amp;action=comm_del&amp;id=" . $row['id'] . "&amp;dle_allow_hash=" . $dle_login_hash . "')\">" );
				$tpl->set( '[/com-del]', "</a>" );
			} else
				$tpl->set_block( "'\\[com-del\\](.*?)\\[/com-del\\]'si", "" );
			
			if( ($user_group[$member_id['user_group']]['allow_addc']) and $config['allow_comments'] == "yes" ) {
				if( ! $row['is_register'] or $row['name'] == '' ) $row['name'] = $row['gast_name'];
				else $row['name'] = $row['name'];
				$tpl->set( '[fast]', "<a onmouseover=\"dle_copy_quote('" . str_replace( array (" ", "&#039;" ), array ("&nbsp;", "&amp;#039;" ), $row['name'] ) . "');\" href=\"#\" onclick=\"dle_ins('" . str_replace( array (" ", "&#039;" ), array ("&nbsp;", "&amp;#039;" ), $row['name'] ) . "'); return false;\">" );
				$tpl->set( '[/fast]', "</a>" );
			} else
				$tpl->set_block( "'\\[fast\\](.*?)\\[/fast\\]'si", "" );
			
			$tpl->set( '{mail}', $row['email'] );
			
			if( date( Ymd, $row['date'] ) == date( Ymd, $_TIME ) ) {
				
				$tpl->set( '{date}', $lang['time_heute'] . langdate( ", H:i", $row['date'] ) );
			
			} elseif( date( Ymd, $row['date'] ) == date( Ymd, ($_TIME - 86400) ) ) {
				
				$tpl->set( '{date}', $lang['time_gestern'] . langdate( ", H:i", $row['date'] ) );
			
			} else {
				
				$tpl->set( '{date}', langdate( $config['timestamp_comment'], $row['date'] ) );
			
			}
			
			$tpl->set( '{news_title}', "" );
			
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
			
			if( $config['comm_msort'] == "ASC" ) $tpl->set( '{comment-id}', $cstart + $i );
			else $tpl->set( '{comment-id}', $comments_num - $cstart - $i + 1 );
			
			if( $row['foto'] ) $tpl->set( '{foto}', $config['http_home_url'] . "uploads/fotos/" . $row['foto'] );
			else $tpl->set( '{foto}', "{THEME}/images/noavatar.png" );
			
			if( $row['is_register'] and $row['icq'] ) $tpl->set( '{icq}', stripslashes( $row['icq'] ) );
			else $tpl->set( '{icq}', '--' );
			
			if( $row['is_register'] and $row['land'] ) $tpl->set( '{land}', stripslashes( $row['land'] ) );
			else $tpl->set( '{land}', '--' );
			
			if( $row['is_register'] and $row['fullname'] ) $tpl->set( '{fullname}', stripslashes( $row['fullname'] ) );
			else $tpl->set( '{fullname}', '--' );
			
			if( $row['is_register'] and $row['reg_date'] ) $tpl->set( '{registration}', langdate( "j.m.Y", $row['reg_date'] ) );
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
			
			$tpl->set( '{comment}', "<div id='comm-id-" . $row['id'] . "'>" . stripslashes( $row['text'] ) . "</div>" );
			
			if( $user_group[$member_id['user_group']]['allow_hide'] ) $tpl->set_block( "'\[hide\](.*?)\[/hide\]'si", "\\1" );
			else $tpl->set_block( "'\\[hide\\](.*?)\\[/hide\\]'si", "<div class=\"quote\">" . $lang['news_regus'] . "</div>" );
			
			$tpl->compile( 'content' );
		}
		$tpl->clear();
		
		$tpl->result['content'] .= "\n<span id='dle-ajax-comments'></span>\n";
		
		$db->free( $sql_result );
		
		if( $comments_num > $config['comm_nummers'] ) {
			include_once ENGINE_DIR . '/modules/c_navigation.php';
		}
	}
	
	if( $member_id['restricted'] and $member_id['restricted_days'] and $member_id['restricted_date'] < $_TIME ) {
		
		$member_id['restricted'] = 0;
		$db->query( "UPDATE LOW_PRIORITY " . USERPREFIX . "_users SET restricted='0', restricted_days='0', restricted_date='' WHERE user_id='{$member_id['user_id']}'" );
	
	}
	
	if( $user_group[$member_id['user_group']]['allow_addc'] AND $config['allow_comments'] == "yes" AND ($member_id['restricted'] != 2 AND $member_id['restricted'] != 3) ) {
		
		if( ! $comments_num ) $tpl->result['content'] .= "\n<span id='dle-ajax-comments'></span>\n";
		
		$tpl->load_template( 'addcomments.tpl' );
		
		if( $config['allow_comments_wysiwyg'] == "yes" ) {
			include_once ENGINE_DIR . '/editor/comments.php';
			$bb_code = "";
			$allow_comments_ajax = true;
		} else
			include_once ENGINE_DIR . '/modules/bbcode.php';
		
		if( $config['allow_comments_wysiwyg'] != "yes" ) {
			$tpl->set( '[not-wysywyg]', "" );
			$tpl->set( '[/not-wysywyg]', "" );
		} else
			$tpl->set_block( "'\\[not-wysywyg\\](.*?)\\[/not-wysywyg\\]'si", "" );
		
		if( $user_group[$member_id['user_group']]['captcha'] ) {
			$tpl->set( '[sec_code]', "" );
			$tpl->set( '[/sec_code]', "" );
			$path = parse_url( $config['http_home_url'] );
			$tpl->set( '{sec_code}', "<span id=\"dle-captcha\"><img src=\"" . $path['path'] . "engine/modules/antibot.php\" alt=\"${lang['sec_image']}\" border=\"0\" alt=\"\" /><br /><a onclick=\"reload(); return false;\" href=\"#\">{$lang['reload_code']}</a></span>" );
		} else {
			$tpl->set( '{sec_code}', "" );
			$tpl->set_block( "'\\[sec_code\\](.*?)\\[/sec_code\\]'si", "" );
		}
		
		if( $config['allow_comments_wysiwyg'] == "yes" ) {
			$tpl->set( '{wysiwyg}', $wysiwyg );
		} else
			$tpl->set( '{wysiwyg}', '' );
		
		$tpl->set( '{text}', '' );
		$tpl->set( '{bbcode}', $bb_code );
		$tpl->set( '{title}', $lang['news_addcom'] );
		
		if( ! $is_logged ) {
			$tpl->set( '[not-logged]', '' );
			$tpl->set( '[/not-logged]', '' );
		} else
			$tpl->set_block( "'\\[not-logged\\](.*?)\\[/not-logged\\]'si", "" );
		
		if( $is_logged ) $hidden = "<input type=\"hidden\" name=\"name\" id=\"name\" value=\"{$member_id['name']}\" /><input type=\"hidden\" name=\"mail\" id=\"mail\" value=\"\" />";
		else $hidden = "";
		
		$tpl->copy_template = "<form  method=\"post\" name=\"dle-comments-form\" id=\"dle-comments-form\" action=\"{$_SESSION['referrer']}\">" . $tpl->copy_template . "
		<input type=\"hidden\" name=\"subaction\" value=\"addcomment\" />{$hidden}
		<input type=\"hidden\" name=\"post_id\" id=\"post_id\" value=\"$news_id\" /></form>";
		
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
	} elseif( $member_id['restricted'] ) {
		
		$tpl->load_template( 'info.tpl' );
		
		if( $member_id['restricted_days'] ) {
			
			$tpl->set( '{error}', $lang['news_info_2'] );
			$tpl->set( '{date}', langdate( "j F Y H:i", $member_id['restricted_date'] ) );
		
		} else
			$tpl->set( '{error}', $lang['news_info_3'] );
		
		$tpl->set( '{title}', $lang['all_info'] );
		$tpl->compile( 'content' );
		$tpl->clear();
	
	} elseif( $config['allow_comments'] != "no" ) {
		
		$tpl->load_template( 'info.tpl' );
		$tpl->set( '{error}', $lang['news_info_1'] );
		$tpl->set( '{group}', $user_group[$member_id['user_group']]['group_name'] );
		$tpl->set( '{title}', $lang['all_info'] );
		$tpl->compile( 'content' );
		$tpl->clear();
	
	}
}
?>