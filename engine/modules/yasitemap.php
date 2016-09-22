<?php
/*
=====================================================
Mod "Yandex Site Map (Yasitemap)" for DataLife Engine - by ShapeShifter
url = http://Smart-Planet.ru Version: 2.0
=====================================================
DataLife Engine - by SoftNews Media Group
-----------------------------------------------------
http://dle-news.ru/
-----------------------------------------------------
Copyright (c) 2004,2008 SoftNews Media Group
=====================================================
Данный код защищен авторскими правами
=====================================================
Файл: yasitemap.php
-----------------------------------------------------
Назначение: Вывод Карты Сайта
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}
@include (ENGINE_DIR.'/data/config.php');
include_once ENGINE_DIR.'/modules/yasitemap_function.php';
$yasitemap['number'] = 30; //количество выводимых статей на одной странице
$tpl->load_template('yasitemap_nav.tpl');
if ($config['allow_alt_url'] == "yes") 
{
	$tpl->set('{nav_stati_name}', "<a href=\"".$config['http_home_url']."yasitemap/\" title=\"Карта сайта (Статьи)\"><b>Статьи</b></a>");
	// $tpl->set('{nav_user_name}', "<a href=\"".$config['http_home_url']."yasitemap_users/\" title=\"Карта сайта (Пользователи)\">Пользователи</a>");
}
else
{
	$tpl->set('{nav_stati_name}', "<a href=\"$PHP_SELF?do=yasitemap\" title=\"Карта сайта (Статьи)\"><b>Статьи</b></a>");
	// $tpl->set('{nav_user_name}', "<a href=\"$PHP_SELF?do=yasitemap_users\" title=\"Карта сайта (Пользователи)\">Пользователи</a>");
}
$tpl->compile('navigation');
$tpl->clear();
$tpl->set('{description}', "Карта сайта (Статьи)");
$yasitemap['number'] = intval($yasitemap['number']);
$i = $cstart = ((isset($_GET['cstart']) ? intval($_GET['cstart']) : 1) - 1) * $yasitemap['number'];
$thisdate = date ("Y-m-d H:i:s", (time() + $config['date_adjust']*60));//
$sql_select = "SELECT `post`.alt_name post_alt_name, `post`.flag flag,`category`.alt_name cat_alt_name,`post`.date,`post`.title,`post`.category,`post`.title,`category`.name,`post`.id,`post`.news_read,`post`.comm_num, `post`.autor FROM `".PREFIX."_post` AS `post`, `".PREFIX."_category` AS `category` WHERE `post`.category = `category`.id AND approve = '1' AND date < '$thisdate' ORDER BY date DESC LIMIT ".$cstart.",".$yasitemap['number']."";  
if ($config['allow_cache'] == "yes")
     {
$papka = "yasitemap/";
          $cache = get_vars_yasitemap ($papka, "news_yasitemap_".$cstart);
          if (!is_array($cache))
          {
               $cache = $db->super_query($sql_select, true);
               set_vars_yasitemap ($papka, "news_yasitemap_".$cstart, $cache);
          }
$papka = "";
     }
     else
     $cache = $db->super_query($sql_select, true);
     $db->free($sql_select);
     foreach ($cache as $row)
     {
		$i++;
		$tpl->load_template('yasitemap.tpl');
		$row['date'] = strtotime($row['date']);
		if (strlen($row['title']) > 50)
		$row['title'] = substr ($row['title'], 0, 50)." ...";
		$row['category'] = intval($row['category']);
		if ($config['allow_alt_url'] == "yes")
        			$full_link_autor = $config['http_home_url']."user/".urlencode($row['autor']);
		else
			$full_link_autor = $PHP_SELF."?subaction=userinfo&amp;user=".urlencode($row['autor']);

		if (!$row['category']) 
		{ 
			$my_cat = "---"; 
			$my_cat_link = "---";
		} 
		else 
		{
			$my_cat = array (); 
			$my_cat_link = array ();
			$cat_list = explode (',', $row['category']);
			if (count($cat_list) == 1) 
			{
			$my_cat[] = $cat_info[$cat_list[0]]['name'];
			$my_cat_link = get_categories ($cat_list[0]);
			} 
			else 
			{
				foreach ($cat_list as $element) 
				{
					if ($element) 
					{ 
						$my_cat[] = $cat_info[$element]['name']; 
						if ($config['ajax']) 
						$go_page = "onclick=\"DlePage('do=cat&category={$cat_info[$element]['alt_name']}'); return false;\" "; 
						else 
						$go_page = "";
						if ($config['allow_alt_url'] == "yes")
							$my_cat_link[] = "<a {$go_page}href=\"".$config['http_home_url'].get_url($element)."/\">{$cat_info[$element]['name']}</a>";
						else
							$my_cat_link[] = "{$cat_info[$element]['name']}";
					}
				}
				$my_cat_link = stripslashes(implode (', ', $my_cat_link));
        		}
			$my_cat = stripslashes(implode (', ', $my_cat));
		}

		if ($config['allow_alt_url'] == "yes") 
		{
			if ($row['flag'] AND $config['seo_type']) 
			{
				if ($row['category'] AND $config['seo_type'] == 2)
				{
					$full_link = $config['http_home_url'].get_url($row['category'])."/".$row['id']."-".$row['post_alt_name'].".html";

				} else {

					$full_link = $config['http_home_url'].$row['id']."-".$row['post_alt_name'].".html";

				}

			} else {

				$full_link = $config['http_home_url'].date('Y/m/d/', $row['date']).$row['post_alt_name'].".html";
			}

		} else {

			$full_link = $PHP_SELF."?newsid=".$row['id'];

		}

		$date_news = langdate("j F Y H:i", $row['date']);
		$tpl->set('{date_map}', $date_news);
		$tpl->set('{title_map}', "<a href=\"".$full_link."\">".stripslashes($row['title'])."</a>");
		$tpl->set('{user_map}', "<a {$go_page}href=\"".$full_link_autor."\">".$row['autor']."</a>");
		$tpl->set('{cat_map}', $my_cat);
		$tpl->compile('content');
	}
	$db->free($sql_select);
	$tpl->clear();
	$tpl->set('{pages}', $pages);
		
	//#########################Навигация по карте сайта###############################################
	$tpl->set('{nav_parametr}', "<input type=\"hidden\" name=\"do\" value=\"yasitemap\" />");
	$tpl->load_template('navigation.tpl');

	//----------------------------------
	// Previous link
	//----------------------------------

	$no_prev = false;
	$no_next = false;

	if(isset($cstart) and $cstart != "" and $cstart > 0){
		$prev = $cstart / $yasitemap['number'];

		if ($config['allow_alt_url'] == "yes") {
			$prev_page = $config['http_home_url']."yasitemap/page".$prev."/";
			$tpl->set_block("'\\[prev-link\\](.*?)\\[/prev-link\\]'si", "<a href=\"".$prev_page."\">\\1</a>");
		} else {
			$prev_page = $PHP_SELF."?do=yasitemap&amp;cstart=".$prev;
			$tpl->set_block("'\\[prev-link\\](.*?)\\[/prev-link\\]'si", "<a href=\"".$prev_page."\">\\1</a>");
		};

	}
	else
	{ $tpl->set_block("'\\[prev-link\\](.*?)\\[/prev-link\\]'si", "<span>\\1</span>"); $no_prev = TRUE; }

	//----------------------------------
	// Pages
	//----------------------------------

	if($yasitemap['number']){
		$papka = "yasitemap/";
		$count_all_yasitemap = get_vars_yasitemap ($papka, "count_all_yasitemap");
		if (!$count_all_yasitemap) {
		$sql_select = "SELECT COUNT(*) as count FROM " . PREFIX . "_post WHERE approve = '1' AND date < '$thisdate'";
		$row = $db->super_query($sql_select);
		$count_all_yasitemap = $row['count'];
		set_vars_yasitemap ($papka, "count_all_yasitemap", $count_all_yasitemap);
		$db->free($sql_select);
  		}
		$papka = "";
		$pages_count = @ceil($count_all_yasitemap/$yasitemap['number']);
		$pages_start_from = 0;
		$pages = "";
		$pages_per_section = 3;
		if($pages_count > 6)
		{
			for($j = 1; $j <= $pages_per_section; $j++)
			{
				if($pages_start_from != $cstart)
				{
					if ($config['allow_alt_url'] == "yes")
					$pages .= "<a href=\"".$config['http_home_url']."yasitemap/page".$j."/\">$j</a> ";
					else
					$pages .= "<a href=\"$PHP_SELF?do=yasitemap&amp;cstart=$j\">$j</a> ";
				}
				else
				{
					$pages .= "<span>$j</span> ";
				}
				$pages_start_from += $yasitemap['number'];
			}
			if(((($cstart / $yasitemap['number']) + 1) > 1) && ((($cstart / $yasitemap['number']) + 1) < $pages_count))
			{
				$pages   .= ((($cstart / $yasitemap['number']) + 1) > ($pages_per_section + 2)) ? '... ' : ' ';
				$page_min = ((($cstart / $yasitemap['number']) + 1) > ($pages_per_section + 1)) ? ($cstart / $yasitemap['number']) : ($pages_per_section + 1);//
				$page_max = ((($cstart / $yasitemap['number']) + 1) < ($pages_count - ($pages_per_section + 1))) ? (($cstart / $yasitemap['number']) + 1) : $pages_count - ($pages_per_section + 1);

				$pages_start_from = ($page_min - 1) * $yasitemap['number'];

				for($j = $page_min; $j < $page_max + ($pages_per_section - 1); $j++)
				{
					if($pages_start_from != $cstart)
					{
						if ($config['allow_alt_url'] == "yes")
						$pages .= "<a href=\"".$config['http_home_url']."yasitemap/page".$j."/\">$j</a> ";
						else
						$pages .= "<a href=\"$PHP_SELF?do=yasitemap&amp;cstart=$j\">$j</a> ";
					}
					else
					{
						$pages .= "<span>$j</span> ";
					}
					$pages_start_from += $yasitemap['number'];
				}
				$pages .= ((($cstart / $yasitemap['number']) + 1) < $pages_count - ($pages_per_section + 1)) ? '... ' : ' ';
			}
			else
			{
				$pages .= '... ';
			}
			$pages_start_from = ($pages_count - $pages_per_section) * $yasitemap['number'];
			for($j=($pages_count - ($pages_per_section - 1)); $j <= $pages_count; $j++)
			{
				if($pages_start_from != $cstart)
				{
					if ($config['allow_alt_url'] == "yes")
					$pages .= "<a href=\"".$config['http_home_url']."yasitemap/page".$j."/\">$j</a> ";
					else
					$pages .= "<a href=\"$PHP_SELF?do=yasitemap&amp;cstart=$j\">$j</a> ";
				}
				else
				{
					$pages .= "<span>$j</span> ";
				}
				$pages_start_from += $yasitemap['number'];
			}

		}
		else
		{
			for($j=1;$j<=$pages_count;$j++)
			{
				if($pages_start_from != $cstart)
				{
					if ($config['allow_alt_url'] == "yes")
					$pages .= "<a href=\"".$config['http_home_url']."yasitemap/page".$j."/\">$j</a> ";
					else
					$pages .= "<a href=\"$PHP_SELF?do=yasitemap&amp;cstart=$j\">$j</a> ";

				}
				else
				{
					$pages .= "<span>$j</span> ";
				}
				$pages_start_from += $yasitemap['number'];
			}
		}
		$tpl->set('{pages}', $pages);
	}

	//----------------------------------
	// Next link
	//----------------------------------
	if($yasitemap['number'] < $count_all_yasitemap and $i < $count_all_yasitemap){
		$next_page = $i / $yasitemap['number'] + 1;
		if ($config['allow_alt_url'] == "yes") {
			$next = $config['http_home_url']."yasitemap/page".$next_page."/";
			$tpl->set_block("'\\[next-link\\](.*?)\\[/next-link\\]'si", "<a href=\"".$next."\">\\1</a>");
		} else {
			$next = $PHP_SELF."?do=yasitemap&amp;cstart=".$next_page;
			$tpl->set_block("'\\[next-link\\](.*?)\\[/next-link\\]'si", "<a href=\"".$next."\">\\1</a>");
		};

	}else{ $tpl->set_block("'\\[next-link\\](.*?)\\[/next-link\\]'si", "<span>\\1</span>"); $no_next = TRUE;}

	if  (!$no_prev OR !$no_next){ $tpl->compile('nav_pages'); }

	$tpl->clear();
	$tpl->compile('copyright');
	$tpl->clear();
$tpl->result['content'] = $tpl->result['navigation'] . $tpl->result['content'] . $tpl->result['nav_pages'];
?>