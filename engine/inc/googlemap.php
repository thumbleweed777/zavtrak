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
 Файл: googlemap.php
-----------------------------------------------------
 Назначение: поиск и замена текста в базе данных
=====================================================
*/
if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

if($member_id['user_group'] != 1){ msg("error", $lang['addnews_denied'], $lang['db_denied']); }

if ($_POST['action'] == "create") {

	include_once ENGINE_DIR.'/classes/google.class.php';
	$map = new googlemap($config);

	$map->limit = intval($_POST['limit']);
	$map->news_priority = strip_tags(stripslashes($_POST['priority']));
	$map->stat_priority = strip_tags(stripslashes($_POST['stat_priority']));
	$map->cat_priority = strip_tags(stripslashes($_POST['cat_priority']));


	$sitemap = $map->build_map();

    $handler = fopen(ROOT_DIR. "/uploads/sitemap.xml", "wb+");
    fwrite($handler, $sitemap);
    fclose($handler);

	@chmod(ROOT_DIR. "/uploads/sitemap.xml", 0666);
}

echoheader("", "");


echo <<<HTML
<form action="" method="post">
<div style="padding-top:5px;padding-bottom:2px;">
<table width="100%">
    <tr>
        <td width="4"><img src="engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="engine/skins/images/tl_oo.gif"><img src="engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src="engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="engine/skins/images/tl_lb.gif"><img src="engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['google_map']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td style="padding:2px;" colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td style="padding:2px;" colspan="2">
HTML;

	if(!@file_exists(ROOT_DIR. "/uploads/sitemap.xml")){ 

		echo $lang['no_google_map'];

	} else {

		$file_date = date("d.m.Y H:i", filectime(ROOT_DIR. "/uploads/sitemap.xml"));

		echo "<b>".$file_date."</b> ".$lang['google_map_info'];

		if ($config['allow_alt_url'] == "yes")
			echo " <a class=\"list\" href=\"".$config['http_home_url']."sitemap.xml\" target=\"_blank\">".$config['http_home_url']."sitemap.xml</a>";
		else 
			echo " <a class=\"list\" href=\"{$config['http_home_url']}uploads/sitemap.xml\" target=\"_blank\">".$config['http_home_url']."uploads/sitemap.xml</a>";

	}


echo <<<HTML
</td>
    <tr>
        <td style="padding:2px;" colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td style="padding:2px;" nowrap>{$lang['google_nnum']}</td>
        <td style="padding:2px;" width="100%"><input class="edit" type="text" size="10" name="limit"><a href="#" class="hintanchor" onMouseover="showhint('{$lang[hint_g_num]}', this, event, '220px')">[?]</a></td>
    </tr>
    <tr>
        <td style="padding:2px;" nowrap>{$lang['google_stat_priority']}</td>
        <td style="padding:2px;" width="100%"><input class="edit" type="text" size="10" name="stat_priority" value="0.5"><a href="#" class="hintanchor" onMouseover="showhint('{$lang[hint_g_priority]}', this, event, '220px')">[?]</a></td>
    </tr>
    <tr>
        <td style="padding:2px;" nowrap>{$lang['google_priority']}</td>
        <td style="padding:2px;" width="100%"><input class="edit" type="text" size="10" name="priority" value="0.6"></td>
    </tr>
    <tr>
        <td style="padding:2px;" nowrap>{$lang['google_cat_priority']}</td>
        <td style="padding:2px;" width="100%"><input class="edit" type="text" size="10" name="cat_priority" value="0.7"></td>
    </tr>
    <tr>
        <td style="padding:2px;" colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td style="padding:2px;" colspan="2"><input type="submit" class="buttons" value="{$lang['google_create']}" style="width:250px;"><input type="hidden" name="action" value="create"></td>
    </tr>
</table>
</td>
        <td background="engine/skins/images/tl_rb.gif"><img src="engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src="engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="engine/skins/images/tl_ub.gif"><img src="engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
        <td><img src="engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</table>
</div></form>
HTML;

echo <<<HTML
<div style="padding-top:5px;padding-bottom:2px;">
<table width="100%">
    <tr>
        <td width="4"><img src="engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="engine/skins/images/tl_oo.gif"><img src="engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src="engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="engine/skins/images/tl_lb.gif"><img src="engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['google_main']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td style="padding:2px;">{$lang['google_info']}</td>
    </tr>
</table>
</td>
        <td background="engine/skins/images/tl_rb.gif"><img src="engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src="engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="engine/skins/images/tl_ub.gif"><img src="engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
        <td><img src="engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</table>
</div>
HTML;


echofooter();
?>