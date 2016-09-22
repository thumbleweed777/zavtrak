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
 Файл: index.php
-----------------------------------------------------
 Назначение: Главная страница
=====================================================
*/
@session_start ();
@ob_start ();
@ob_implicit_flush ( 0 );

@error_reporting ( E_ALL ^ E_NOTICE );
@ini_set ( 'display_errors', false );
@ini_set ( 'html_errors', false );
@ini_set ( 'error_reporting', E_ALL ^ E_NOTICE );

define ( 'DATALIFEENGINE', true );

$member_id = FALSE;
$is_logged = FALSE;

define ( 'ROOT_DIR', dirname ( __FILE__ ) );
define ( 'ENGINE_DIR', ROOT_DIR . '/engine' );



require_once ROOT_DIR . '/engine/init.php';
require_once ENGINE_DIR.'/modules/referer.php';


# autositemap
define ( 'AUTOSITEMAP', true );
require_once 'autositemap.php';



if (clean_url ( $_SERVER['HTTP_HOST'] ) != clean_url ( $config['http_home_url'] )) {
	
	$replace_url = array ();
	$replace_url[0] = clean_url ( $config['http_home_url'] );
	$replace_url[1] = clean_url ( $_SERVER['HTTP_HOST'] );

} else
	$replace_url = false;

$tpl->load_template ( 'main.tpl' );


require_once ENGINE_DIR . '/modules/taginator/taginator_block.php';
$tpl->set ( '{tag_block}', $tag_block );


$tpl->set ( '{calendar}', $tpl->result['calendar'] );


$tpl->set('{referer}', $tpl->result['referer']);


$tpl->set ( '{archives}', $tpl->result['archive'] );
$tpl->set ( '{tags}', $tpl->result['tags_cloud'] );

$tpl->set('{tags_all_view}', $tpl->result['tags_all_view']);


$tpl->set ( '{vote}', $tpl->result['vote'] );
$tpl->set ( '{topnews}', $topnews );
$tpl->set ( '{login}', $login_panel );
$tpl->set ( '{info}', "<span id='dle-info'>" . $tpl->result['info'] . "</span>" );

$tpl->set('{online}', $tpl->result['online']);


$tpl->set ( '{speedbar}', $tpl->result['speedbar'] );

if ($config['allow_skin_change'] == "yes") $tpl->set ( '{changeskin}', ChangeSkin ( ROOT_DIR . '/templates', $config['skin'] ) );

if (count ( $banners ) and $config['allow_banner']) {
	
	foreach ( $banners as $name => $value ) {
		$tpl->copy_template = str_replace ( "{banner_" . $name . "}", $value, $tpl->copy_template );
	}

}

$tpl->set_block ( "'{banner_(.*?)}'si", "" );

if (count ( $informers ) and $config['rss_informer']) {
	foreach ( $informers as $name => $value ) {
		$tpl->copy_template = str_replace ( "{inform_" . $name . "}", $value, $tpl->copy_template );
	}
}

if ($do == "" and ! $subaction and $year) $do = "date";
elseif ($do == "" and $catalog) $do = "catalog";
elseif ($do == "") $do = $subaction;

if ($allow_active_news and $config['allow_change_sort'] and ! $config['ajax']) {
	
	$tpl->set ( '[sort]', "" );
	$tpl->set ( '{sort}', news_sort ( $do ) );
	$tpl->set ( '[/sort]', "" );

} else {
	
	$tpl->set_block ( "'\\[sort\\](.*?)\\[/sort\\]'si", "" );

}

if (strpos ( $tpl->copy_template, "[aviable=" ) !== false) {
	$tpl->copy_template = preg_replace ( "#\\[aviable=(.+?)\\](.*?)\\[/aviable\\]#ies", "check_module('\\1', '\\2', '{$do}')", $tpl->copy_template );
}

if (strpos ( $tpl->copy_template, "[not-aviable=" ) !== false) {
	$tpl->copy_template = preg_replace ( "#\\[not-aviable=(.+?)\\](.*?)\\[/not-aviable\\]#ies", "check_module('\\1', '\\2', '{$do}', false)", $tpl->copy_template );
}

if (strpos ( $tpl->copy_template, "[not-group=" ) !== false) {
	$tpl->copy_template = preg_replace ( "#\\[not-group=(.+?)\\](.*?)\\[/not-group\\]#ies", "check_group('\\1', '\\2', false)", $tpl->copy_template );
}

if (strpos ( $tpl->copy_template, "[group=" ) !== false) {
	$tpl->copy_template = preg_replace ( "#\\[group=(.+?)\\](.*?)\\[/group\\]#ies", "check_group('\\1', '\\2')", $tpl->copy_template );
}

if (strpos ( $tpl->copy_template, "[category=" ) !== false) {
	$tpl->copy_template = preg_replace ( "#\\[category=(.+?)\\](.*?)\\[/category\\]#ies", "check_category('\\1', '\\2', '{$category_id}')", $tpl->copy_template );
}

if (strpos ( $tpl->copy_template, "[not-category=" ) !== false) {
	$tpl->copy_template = preg_replace ( "#\\[not-category=(.+?)\\](.*?)\\[/not-category\\]#ies", "check_category('\\1', '\\2', '{$category_id}', false)", $tpl->copy_template );
}

if (strpos ( $tpl->copy_template, "{custom" ) !== false) {
	$tpl->copy_template = preg_replace ( "#\\{custom category=['\"](.+?)['\"] template=['\"](.+?)['\"] aviable=['\"](.+?)['\"] from=['\"](.+?)['\"] limit=['\"](.+?)['\"] cache=['\"](.+?)['\"]\\}#ies", "custom_print('\\1', '\\2', '\\3', '\\4', '\\5', '\\6', '{$do}')", $tpl->copy_template );
}






$a ="marketgid.com";
$nmarket = "<div style=\"display: none;\">";
$nmarkets = "</div>";



function clears_host ($url) {
    $value = str_replace ('http://', '', $url);
    $value = str_replace ('www.', '', $value);
    $value = explode ('/', $value);
    $value = reset ($value);

    return strtolower ($value);
}

$http_referer = @isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : getenv("http_referer"); 
$homeurl = @isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''; 

$homeurl = clears_host ($homeurl); # 
$http_referer = str_replace ('www.', '', $http_referer); 
$host_referer = clears_host ($http_referer);
if ($a == $host_referer) {
$tpl->set('[market]', $nmarket);
$tpl->set('[/market]',$nmarkets);
$tpl->set('[notmarket]','');
$tpl->set('[/notmarket]','');

}
else {
$tpl->set('[market]','');
$tpl->set('[/market]','');
$tpl->set('[notmarket]',$nmarket);
$tpl->set('[/notmarket]',$nmarkets);
}	








$config['http_home_url'] = explode ( "index.php", strtolower ( $_SERVER['PHP_SELF'] ) );
$config['http_home_url'] = reset ( $config['http_home_url'] );

if (! $user_group[$member_id['user_group']]['allow_admin']) $config['admin_path'] = "";

$ajax .= <<<HTML
<script language="javascript" type="text/javascript">
<!--
var dle_root       = '{$config['http_home_url']}';
var dle_admin      = '{$config['admin_path']}';
var dle_login_hash = '{$dle_login_hash}';
var dle_skin       = '{$config['skin']}';
var dle_wysiwyg    = '{$config['allow_comments_wysiwyg']}';
var quick_wysiwyg  = '{$config['allow_quick_wysiwyg']}';
var menu_short     = '{$lang['menu_short']}';
var menu_full      = '{$lang['menu_full']}';
var menu_profile   = '{$lang['menu_profile']}';
var menu_fnews     = '{$lang['menu_fnews']}';
var menu_fcomments = '{$lang['menu_fcomments']}';
var menu_send      = '{$lang['menu_send']}';
var menu_uedit     = '{$lang['menu_uedit']}';
var dle_req_field  = '{$lang['comm_req_f']}';
var dle_del_agree  = '{$lang['news_delcom']}';
var dle_del_news   = '{$lang['news_delnews']}';\n
HTML;

if ($user_group[$member_id['user_group']]['allow_all_edit']) {
	
	$ajax .= <<<HTML
var allow_dle_delete_news   = true;\n
HTML;

} else {
	
	$ajax .= <<<HTML
var dle_login_hash = '';
var allow_dle_delete_news   = false;\n
HTML;

}

$ajax .= <<<HTML
//-->
</script>
<script type="text/javascript" src="{$config['http_home_url']}engine/ajax/menu.js"></script>
<script type="text/javascript" src="{$config['http_home_url']}engine/ajax/dle_ajax.js"></script>
<div id="loading-layer" style="display:none;font-family: Verdana;font-size: 11px;width:200px;height:50px;background:#FFF;padding:10px;text-align:center;border:1px solid #000"><div style="font-weight:bold" id="loading-layer-text">{$lang['ajax_info']}</div><br /><img src="{$config['http_home_url']}engine/ajax/loading.gif"  border="0" alt="" /></div>
<div id="busy_layer" style="visibility: hidden; display: block; position: absolute; left: 0px; top: 0px; width: 100%; height: 100%; background-color: gray; opacity: 0.1; -ms-filter: 'progid:DXImageTransform.Microsoft.Alpha(Opacity=10)'; filter:progid:DXImageTransform.Microsoft.Alpha(opacity=10); "></div>
<script type="text/javascript" src="{$config['http_home_url']}engine/ajax/js_edit.js"></script>
HTML;

if ($allow_comments_ajax AND ($config['allow_comments_wysiwyg'] == "yes" OR $config['allow_quick_wysiwyg'])) $ajax .= <<<HTML

<script type="text/javascript" src="{$config['http_home_url']}engine/editor/jscripts/tiny_mce/tiny_mce.js"></script>

HTML;

if (strpos ( $tpl->result['content'], "hs.expand" ) !== false or strpos ( $tpl->copy_template, "hs.expand" ) !== false or $config['ajax'] or $pm_alert != "") {
	
	if ($pm_alert != "") $hs_prefix = "-html";
	else $hs_prefix = "";
	
	$ajax .= <<<HTML

<script type="text/javascript" src="{$config['http_home_url']}engine/classes/highslide/highslide{$hs_prefix}.js"></script>
<script type="text/javascript">    
    hs.graphicsDir = '{$config['http_home_url']}engine/classes/highslide/graphics/';
    hs.outlineType = 'rounded-white';
    hs.numberOfImagesToPreload = 0;
    hs.showCredits = false;
	hs.lang = {
		loadingText :     '{$lang['loading']}',
		fullExpandTitle : '{$lang['thumb_expandtitle']}',
		restoreTitle :    '{$lang['thumb_restore']}',
		focusTitle :      '{$lang['thumb_focustitle']}',
		loadingTitle :    '{$lang['thumb_cancel']}'
	};
</script>
{$pm_alert}
HTML;

}

$tpl->set ( '{AJAX}', $ajax );
$tpl->set ( '{headers}', $metatags );

$tpl->set ( '{content}', "<div id='dle-content'>" . $tpl->result['content'] . "</div>" );
$tpl->set ( '{THEME}', $config['http_home_url'] . 'templates/' . $config['skin'] );


if (!defined('_SAPE_USER')){
	   define('_SAPE_USER', '6bb68068f5b2240956fed27738d8ead8');
	}
	require_once(realpath($_SERVER['DOCUMENT_ROOT'].'/'._SAPE_USER.'/sape.php'));
	$o['host'] = 'zavtrak.org'; // БЕЗ HTTP://
	$sape = new SAPE_client($o);
	$tpl->set('{sape_links}', $sape->return_links());



$tpl->compile ( 'main' );

if ($replace_url) $tpl->result['main'] = str_replace ( $replace_url[0], $replace_url[1], $tpl->result['main'] );

echo $tpl->result['main'];
$tpl->global_clear ();
$db->close ();



GzipOut ();
?>