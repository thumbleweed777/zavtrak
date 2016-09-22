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
 Файл: c_navigation.php
-----------------------------------------------------
 Назначение: Вывод навигации для комментариев
=====================================================
*/
if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}
$number = intval( $config['comm_nummers'] );

if( isset( $_REQUEST['cstart'] ) ) $cstart = intval( $_GET['cstart'] );
if( ! $cstart ) $cstart = 1;
if( $_GET['news_page'] ) $user_query = "newsid=" . $newsid . "&amp;news_page=" . intval( $_GET['news_page'] ); else $user_query = "newsid=" . $newsid;

//####################################################################################################################
//         Навигация 
//####################################################################################################################


$tpl->load_template( 'navigation.tpl' );
//----------------------------------
// Previous link
//----------------------------------
if( $cstart > 1 ) {
	$prev = $cstart - 1;
	if( $config['allow_alt_url'] == "yes" ) $tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", "<a href=\"" . $link_page . $prev . "," . $news_name . ".html#comment\">\\1</a>" );
	else $tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", "<a href=\"$PHP_SELF?cstart=" . $prev . "&amp;{$user_query}#comment\">\\1</a>" );

} else {
	$tpl->set_block( "'\[prev-link\](.*?)\[/prev-link\]'si", "<span>\\1</span>" );
	$no_prev = TRUE;
}

//----------------------------------
// Pages
//----------------------------------
if( $number ) {
	
	$count_all = $comments_num;
	
	$enpages_count = @ceil( $count_all / $number );
	$pages = "";
	
	if( $enpages_count <= 10 ) {
		
		for($j = 1; $j <= $enpages_count; $j ++) {
			
			if( $j != $cstart ) {
				
				if( $config['allow_alt_url'] == "yes" ) $pages .= "<a href=\"" . $link_page . $j . "," . $news_name . ".html#comment\">$j</a> ";
				else $pages .= "<a href=\"$PHP_SELF?cstart=$j&amp;{$user_query}#comment\">$j</a> ";
			
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
			
			if( $config['allow_alt_url'] == "yes" ) $pages .= "<a href=\"" . $link_page . "1," . $news_name . ".html#comment\">1</a> ... ";
			else $pages .= "<a href=\"$PHP_SELF?cstart=1&amp;{$user_query}#comment\">1</a> ... ";
		
		}
		
		for($j = $start; $j <= $end; $j ++) {
			
			if( $j != $cstart ) {
				
				if( $config['allow_alt_url'] == "yes" ) $pages .= "<a href=\"" . $link_page . $j . "," . $news_name . ".html#comment\">$j</a> ";
				else $pages .= "<a href=\"$PHP_SELF?cstart=$j&amp;{$user_query}#comment\">$j</a> ";
			
			} else {
				
				$pages .= "<span>$j</span> ";
			}
		
		}
		
		if( $cstart != $enpages_count ) {
			
			if( $config['allow_alt_url'] == "yes" ) $pages .= $nav_prefix . "<a href=\"" . $link_page . $enpages_count . "," . $news_name . ".html#comment\">{$enpages_count}</a>";
			else $pages .= $nav_prefix . "<a href=\"$PHP_SELF?cstart={$enpages_count}&amp;{$user_query}#comment\">{$enpages_count}</a>";
		
		} else
			$pages .= "<span>{$enpages_count}</span> ";
	
	}
	
	$tpl->set( '{pages}', $pages );

}

//----------------------------------
// Next link
//----------------------------------
if( $cstart < $enpages_count ) {
	$next_page = $cstart + 1;
	if( $config['allow_alt_url'] == "yes" ) $tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", "<a href=\"" . $link_page . $next_page . "," . $news_name . ".html#comment\">\\1</a>" );
	else $tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", "<a href=\"$PHP_SELF?cstart=$next_page&amp;{$user_query}#comment\">\\1</a>" );

} else {
	$tpl->set_block( "'\[next-link\](.*?)\[/next-link\]'si", "<span>\\1</span>" );
	$no_next = TRUE;
}

if( ! $no_prev or ! $no_next ) {
	$tpl->compile( 'content' );
}

$tpl->clear();
?>