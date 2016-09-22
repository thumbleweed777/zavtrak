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
include_once ENGINE_DIR.'/modules/yasitemap_function.php';
$yasitemap['number'] = 30; //количество выводимых юзеров на одной странице
$tpl->load_template('yasitemap_nav.tpl');
if ($config['allow_alt_url'] == "yes") 
{
	$tpl->set('{nav_stati_name}', "<a href=\"".$config['http_home_url']."yasitemap/\" title=\"Карта сайта (Статьи)\">Статьи</a>");
	$tpl->set('{nav_user_name}', "<a href=\"".$config['http_home_url']."yasitemap_users/\" title=\"Карта сайта (Пользователи)\"><b>Пользователи</b></a>");
}
else
{
	$tpl->set('{nav_stati_name}', "<a href=\"$PHP_SELF?do=yasitemap\" title=\"Карта сайта (Статьи)\"><b>Статьи</b></a>");
	$tpl->set('{nav_user_name}', "<a href=\"$PHP_SELF?do=yasitemap_users\" title=\"Карта сайта (Пользователи)\"><b>Пользователи</b></a>");
}
$tpl->compile('navigation');
$tpl->clear();
$tpl->set('{description}', "Катра сайта (Пользователи)");
$yasitemap['number'] = intval($yasitemap['number']);
$i = $cstart = ((isset($_GET['cstart']) ? intval($_GET['cstart']) : 1) - 1) * $yasitemap['number'];

$thisdate = date ("Y-m-d H:i:s", (time() + $config['date_adjust']*60));//
$sql_select = "SELECT name, banned, user_group, comm_num, fullname, reg_date FROM ".PREFIX."_users WHERE reg_date < '$thisdate' ORDER BY reg_date DESC LIMIT ".$cstart.",".$yasitemap['number']."";  
if ($config['allow_cache'] == "yes")
     {
	$papka = "yasitemap/";
          $cache = get_vars_yasitemap ($papka, "users_yasitemap_".$cstart);
          if (!is_array($cache))
          {
               $cache = $db->super_query($sql_select, true);
               set_vars_yasitemap ($papka, "users_yasitemap_".$cstart, $cache);
          }
$papka = "";
     }
     else
     $cache = $db->super_query($sql_select, true);
     $db->free($sql_select);
     foreach ($cache as $row)
     {	
		$i++;
		$tpl->load_template('yasitemap_user.tpl');
		$row['reg_date'] = langdate("j F Y H:i", $row['reg_date']);
		$row['lastdate'] = langdate("j F Y H:i", $row['lastdate']);
		$row['name'] = stripslashes($row['name']);
		if ($row['banned'] == 'yes') $user_group[$row['user_group']]['group_name'] = $lang['user_ban'];
		$row['user_group'] = stripslashes($user_group[$row['user_group']]['group_name']);
		if ($row['fullname'] == "")
			$fullname = "";
		else
			$fullname = $row['fullname'];
		if ($config['allow_alt_url'] == "yes")
        		$full_link_user = $config['http_home_url']."user/".urlencode($row['name']);
		else
			$full_link_user = $PHP_SELF."?subaction=userinfo&amp;user=".urlencode($row['name']);
		$tpl->set('{user_regdate}', $row['reg_date']);
		$tpl->set('{user_profile}', "<a href=".$full_link_user." title=\"Смотреть профиль\"><b>".$row['name']."</b></a>");
		$tpl->set('{user_group}', "Гуппа: ".$row['user_group']);
		if($fullname != "")
		$tpl->set('{user_name}', "Настоящее имя: ".$fullname);
		else
		$tpl->set('{user_name}', "");
		$tpl->compile('content');
	}
	$db->free($sql_select);	
	$tpl->clear();
	$tpl->set('{pages}', $pages);
		
	//#########################Навигация по карте сайта###############################################
	$tpl->set('{nav_parametr}', "<input type=\"hidden\" name=\"do\" value=\"yasitemap_users\" />");
	$tpl->load_template('navigation.tpl');

	//----------------------------------
	// Previous link
	//----------------------------------

	$no_prev = false;
	$no_next = false;

	if(isset($cstart) and $cstart != "" and $cstart > 0){
		$prev = $cstart / $yasitemap['number'];

		if ($config['allow_alt_url'] == "yes") {
			$prev_page = $config['http_home_url']."yasitemap_users/page".$prev."/";
			$tpl->set_block("'\\[prev-link\\](.*?)\\[/prev-link\\]'si", "<a href=\"".$prev_page."\">\\1</a>");
		} else {
			$prev_page = $PHP_SELF."?do=yasitemap_users&amp;cstart=".$prev;
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
		$count_all_yasitemap_users = get_vars_yasitemap ($papka, "count_all_yasitemap_users");
		if (!$count_all_yasitemap_users) {
		$sql_select = "SELECT COUNT(*) as count FROM " . PREFIX . "_users WHERE reg_date < '$thisdate'";
		$row = $db->super_query($sql_select);
		$count_all_yasitemap_users = $row['count'];
		set_vars_yasitemap ($papka, "count_all_yasitemap_users", $count_all_yasitemap_users);
		$db->free($sql_select);
  		}
		$papka = "";
		$pages_count = @ceil($count_all_yasitemap_users/$yasitemap['number']);
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
					$pages .= "<a href=\"".$config['http_home_url']."yasitemap_users/page".$j."/\">$j</a> ";
					else
					$pages .= "<a href=\"$PHP_SELF?do=yasitemap_users&amp;cstart=$j\">$j</a> ";
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
						$pages .= "<a href=\"".$config['http_home_url']."yasitemap_users/page".$j."/\">$j</a> ";
						else
						$pages .= "<a href=\"$PHP_SELF?do=yasitemap_users&amp;cstart=$j\">$j</a> ";
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
					$pages .= "<a href=\"".$config['http_home_url']."yasitemap_users/page".$j."/\">$j</a> ";
					else
					$pages .= "<a href=\"$PHP_SELF?do=yasitemap_users&amp;cstart=$j\">$j</a> ";
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
					$pages .= "<a href=\"".$config['http_home_url']."yasitemap_users/page".$j."/\">$j</a> ";
					else
					$pages .= "<a href=\"$PHP_SELF?do=yasitemap_users&amp;cstart=$j\">$j</a> ";

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
	if($yasitemap['number'] < $count_all_yasitemap_users and $i < $count_all_yasitemap_users){
		$next_page = $i / $yasitemap['number'] + 1;
		if ($config['allow_alt_url'] == "yes") {
			$next = $config['http_home_url']."yasitemap_users/page".$next_page."/";
			$tpl->set_block("'\\[next-link\\](.*?)\\[/next-link\\]'si", "<a href=\"".$next."\">\\1</a>");
		} else {
			$next = $PHP_SELF."?do=yasitemap_users&amp;cstart=".$next_page;
			$tpl->set_block("'\\[next-link\\](.*?)\\[/next-link\\]'si", "<a href=\"".$next."\">\\1</a>");
		};

	}else{ $tpl->set_block("'\\[next-link\\](.*?)\\[/next-link\\]'si", "<span>\\1</span>"); $no_next = TRUE;}

	if  (!$no_prev OR !$no_next){ $tpl->compile('nav_pages'); }

	$tpl->clear();
	$tpl->compile('copyright');
	$tpl->clear();
$tpl->result['content'] = $tpl->result['navigation'] . $tpl->result['content'] . $tpl->result['nav_pages'];
?>