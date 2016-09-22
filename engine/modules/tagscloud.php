<?php
/*
=====================================================
 DataLife Engine - by SoftNews Media Group 
-----------------------------------------------------
 http://dle-news.ru/
-----------------------------------------------------
 Copyright (c) 2004,2008 SoftNews Media Group
=====================================================
 Данный код защищен авторскими правами
=====================================================
 Файл: tagscloud.php
-----------------------------------------------------
 Назначение: Формирование облака тегов
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

$is_change = false;

if ($config['allow_cache'] != "yes") { $config['allow_cache'] = "yes"; $is_change = true;}

$tpl->result['tags_cloud'] = dle_cache("tagscloud", $config['skin']);
$tpl->result['tags_all_view'] = dle_cache("tags_all_view", $config['skin']);
if ($tpl->result['tags_cloud'] === false) {

	$counts = array();
	$tags = array();
	$list = array();
	$sizes = array( "8pt", "11.5pt", "15pt", "18.5pt", "22pt" );
	$min   = 1;
	$max   = 1;
	$range = 1;

	$db->query("SELECT SQL_CALC_FOUND_ROWS tag, COUNT(*) AS count FROM " . PREFIX . "_tags GROUP BY tag ORDER BY count DESC LIMIT 0,40");

	while($row = $db->get_row()){

		$tags[$row['tag']] = $row['count'];
		$counts[] = $row['count'];

	}
	$db->free();

	if (count($counts)) {
		$min   = min($counts);
		$max   = max($counts);
		$range = ($max-$min);
	}













if (!$range) $range = 1;

















	foreach ($tags as $tag => $value) {

		$list[$tag]['tag']   = $tag;
		$list[$tag]['size']  = $sizes[sprintf("%d", ($value-$min)/$range*4 )];

	}

	usort ($list, "compare_tags");
	$tags = array();	

	foreach ($list as $value) {

		

		if ($config['allow_alt_url'] == "yes")
        	$tags[] = "<a href='".$config['http_home_url']."tags/".urlencode($value['tag'])."' style='font-size:{$value['size']};'>".$value['tag']."</a>";
		else
			$tags[] = "<a href='".$config['http_home_url']."index.php?do=tags&tag=".urlencode($value['tag'])."' style='font-size:{$value['size']};'>".$value['tag']."</a>";

	}

	$tpl->result['tags_cloud'] = implode("", $tags);

	$row = $db->super_query("SELECT FOUND_ROWS() as count");

	if ($row['count'] > 40) {

		if ($config['allow_alt_url'] == "yes")
        	$tpl->result['tags_all_view'] .= "<a href=\"".$config['http_home_url']."tags/\">".$lang['all_tags']."</a>";
		else
			$tpl->result['tags_all_view'] .= "<a href=\"$PHP_SELF?do=tags\">".$lang['all_tags']."</a>";


	}

	create_cache ("tagscloud", $tpl->result['tags_cloud'], $config['skin']);
	create_cache ("tags_all_view", $tpl->result['tags_all_view'], $config['skin']);
}


if ($do == "alltags") {

	$tpl->result['content'] = dle_cache("alltagscloud", $config['skin']);

	if (!$tpl->result['content']) {

		$tpl->load_template('tagscloud.tpl');

		$counts = array();
		$tags = array();
		$list = array();
		$sizes = array( "clouds_xsmall", "clouds_small", "clouds_medium", "clouds_large", "clouds_xlarge"  );
		$min   = 1;
		$max   = 1;
		$range = 1;

		$db->query("SELECT tag, COUNT(*) AS count FROM " . PREFIX . "_tags GROUP BY tag");

		while($row = $db->get_row()){

			$tags[$row['tag']] = $row['count'];
			$counts[] = $row['count'];

		}
		$db->free();

		if (count($counts)) {
			$min   = min($counts);
			$max   = max($counts);
			$range = ($max-$min);
		}

















if (!$range) $range = 1;




















		foreach ($tags as $tag => $value) {

			$list[$tag]['tag']   = $tag;
			$list[$tag]['size']  = $sizes[sprintf("%d", ($value-$min)/$range*4 )];

		}

		usort ($list, "compare_tags");
		$tags = array();	

		foreach ($list as $value) {

			$go_page = ($config['ajax']) ? "onclick=\"DlePage('do=tags&amp;tag=".urlencode($value['tag'])."'); return false;\" " : "";

			if ($config['allow_alt_url'] == "yes")
	        	$tags[] = "<a {$go_page} href=\"".$config['http_home_url']."tags/".urlencode($value['tag'])."/\" class=\"{$value['size']}\">".$value['tag']."</a>";
			else
				$tags[] = "<a {$go_page} href=\"$PHP_SELF?do=tags&amp;tag=".urlencode($value['tag'])."\" class=\"{$value['size']}\">".$value['tag']."</a>";

		}

		$tags = implode(", ", $tags);

		$tpl->set('{tags}', $tags);
		$tpl->compile('content');
		$tpl->clear();

		create_cache ("alltagscloud", $tpl->result['content'], $config['skin']);

	}

}

if ($is_change) $config['allow_cache'] = false;

?>