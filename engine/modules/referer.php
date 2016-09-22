<?php
/*
=====================================================
 The module for DataLife Engine from Konokhov N.
-----------------------------------------------------
 http://getdle.com/
-----------------------------------------------------
 Copyright (c) 2007,2009 Nikolay V. Konokhov
=====================================================
 Данный код защищен авторскими правами
=====================================================
 Файл: referer.php
-----------------------------------------------------
 Назначение: Функции по добавлению переходов
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

include ENGINE_DIR.'/data/referer.perf.php'; # загружаем файл поисковых систем
include ENGINE_DIR.'/data/referer.conf.php'; # загружаем файл конфигурации
require_once ROOT_DIR.'/language/'.$config['langs'].'/referer.lng'; # загружаем файл языка

# Функция определения домена из ссылки
function clear_host ($url) {
    $value = str_replace ('http://', '', $url);
    $value = str_replace ('www.', '', $value);
    $value = explode ('/', $value);
    $value = reset ($value);

    return strtolower ($value);
}

# Функция проверки правильности перехода
function checkurl($url) {

$url=trim($url);
if (strlen($url)==0) return "true";
if (!preg_match("~^(?:(?:https?|ftp|telnet)://(?:[a-z0-9_-]{1,32}".
"(?::[a-z0-9_-]{1,32})?@)?)?(?:(?:[a-z0-9-]{1,128}\.)+(?:com|net|".
"org|mil|edu|arpa|gov|biz|info|aero|inc|name|[a-z]{2})|(?!0)(?:(?".
"!0[^.]|255)[0-9]{1,3}\.){3}(?!0|255)[0-9]{1,3})(?:/[a-z0-9.,_@%&:".
"?+=\~/-]*)?(?:#[^ '\"&<>]*)?$~i",$url,$ok))


return "false";
}
      #  $_SERVER['HTTP_REFERER'] = 'http://www.google.com/search?q=привеи';
	# Получаем информацию
	$datetime = time()+($config['date_adjust']*60); # текущее время
	$request_uri = @isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''; # запрашиваемая страница
	$http_referer = @isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : getenv("http_referer"); # откуда пришел
	$ip = @isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']; # ip
	$homeurl = @isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ''; # домен своего сайта

	if (!strstr($http_referer,"://")) $http_referer="http://".$http_referer;
	$http_referer=preg_replace("~^[a-z]+~ie","strtolower('\\0')",$http_referer);

	# Подгоняем информацию
	$homeurl = clear_host ($homeurl); # домен своего сайта
	$http_referer = str_replace ('www.', '', $http_referer); # вырезаем www.
	$host_referer = clear_host ($http_referer); # домен реферала

    if (checkurl($http_referer) != "false") {

# обрабатываем все google
$google_tog = stristr($host_referer, 'google.');
if($google_tog) $host_referer = "all.google.all";

# обрабатываем все webalta
$webalta_tog = stristr($host_referer, 'webalta.');
if($webalta_tog) $host_referer = "all.webalta.all";

# обрабатываем все yahoo
$yahoo_tog = stristr($host_referer, 'yahoo.');
if($yahoo_tog) $host_referer = "all.yahoo.all";

# обрабатываем все redtram.com
$redtram_tog = stristr($host_referer, 'redtram.com');
if($redtram_tog) $host_referer = "all.redtram.com";

# обрабатываем все mail.ru
$mailru_tog = stristr($host_referer, 'mail.ru');
if($mailru_tog) $host_referer = "all.mail.ru";

# обрабатываем все yandex.ru
$yandex_tog = stristr($host_referer, 'yandex');
if($yandex_tog) $host_referer = "all.yandex.all";

$pack_ignor = array();
if($confms['site_ignor']) $pack_ignor = @explode(", ", $confms['site_ignor']);
array_push($pack_ignor, $homeurl); # добавляем свой сайт в список игнорируемых

# выполняем, если сайт не в игноре и переменная не пуста
if (!in_array($host_referer, $pack_ignor) && trim($http_referer)) {

$is_referer = 'referer'; # ставим начальное значение
if (!in_array($host_referer, $engines)) $is_referer = 'referer'; else $is_referer = 'engine'; # определяем откуда пришёл

# выполняем, для поисковой системы
if ($is_referer == "engine") {

$sw = $engine[$host_referer]['2']; # основа поиска запроса

        $request = urldecode($http_referer);
        $request = stripslashes($request);
        $request = strip_tags($request);
		$request=preg_replace('{[/\'"#@!%.]+}isU','',$request);

#	preg_match($sw."([^&]*)",$request."&",$request);
#	if($request) preg_match("query=([^&]*)",$request."&",$request);

	#filters
	$sw = preg_quote($sw);
	#$request = preg_quote($request);
	#/filters

    preg_match("#".$sw."([^&]*)#i",$request."&",$request);

    $request = isset ($request[1]) ? $request[1] : false;

    if(!$request) preg_match("#query=([^&]*)#i",$request."&",$request);

        $request = strip_tags($request);
        $request = rawurldecode($request);
    #var_dump($request); die;
	 $request = @iconv("utf-8", "windows-1251", $request);

	if (!$request) {
        $request = urldecode($http_referer);
        $request = stripslashes($request);
        $request = strip_tags($request);
		$request=preg_replace('{[/\'"#@!%.]+}isU','',$request);

#	preg_match($sw."([^&]*)",$request."&",$request);
#	if($request) preg_match("query=([^&]*)",$request."&",$request);

    preg_match("#".$sw."([^&]*)#i",$request."&",$request);
    if(!$request) preg_match("#query=([^&]*)#i",$request."&",$request);

        $request = strip_tags($request[1]);
        $request = rawurldecode($request);
	}
 #   var_dump($request); die;

	if (strtolower($request) == "t") $request = '';
	if (!$request) $is_referer = 'referer';

}

	if ($confms['sea_flate'] == "yes" && $is_referer == "engine")
	$http_referer = @base64_encode(gzdeflate($http_referer, 9)); # сжимаем реферал с поисковой системы

# смотрим куда именно попали
if ($subaction == "showfull") $position .= "%news%";
elseif ($category_id) $position .= "%cat%";
elseif (!empty($nam_e)) $position .= "%posin%";
elseif (empty($nam_e)) $position .= "%main%";
if ($nam_e) $position .= $nam_e;
if ($titl_e) $position .= $titl_e;
$position = $db->safesql($position);

if ($is_referer == "engine") {
# выполняем проверку на повторение поисковиков
            $refexist = $db->super_query("SELECT COUNT(*) as count, hits FROM " . PREFIX . "_referer WHERE request = '{$request}' AND host = '{$host_referer}'");
            #подключаем баблонатор
            require_once  ENGINE_DIR.'/modules/taginator.php';
            taginator_execute($refexist['hits'], $request);


if ($refexist['count']) {
$db->query("UPDATE " . PREFIX . "_referer SET hits=hits+1, uri='{$request_uri}', position='{$position}', date='{$datetime}', user_ip='{$ip}' WHERE request = '{$request}' AND host = '{$host_referer}'");
} else {
$db->query("INSERT INTO " . PREFIX . "_referer VALUES('', '{$http_referer}', '{$datetime}', '{$host_referer}', '1', '{$request}', '{$request_uri}', '{$position}', '{$ip}', '{$is_referer}')");
}
} else {
# выполняем проверку на повторение
$refexist = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_referer WHERE referer = '{$http_referer}'");

if ($refexist['count']) {
$db->query("UPDATE " . PREFIX . "_referer SET hits=hits+1, uri='{$request_uri}', request='{$request}', position='{$position}', date='{$datetime}', user_ip='{$ip}' WHERE referer='{$http_referer}'");
} else {
$db->query("INSERT INTO " . PREFIX . "_referer VALUES('', '{$http_referer}', '{$datetime}', '{$host_referer}', '1', '{$request}', '{$request_uri}', '{$position}', '{$ip}', '{$is_referer}')");
}
}

# функция ПереходИнфо™
if ($confms['sea_addi'] == "yes" && $_COOKIE['dle_seatrans'] != "no"){

if($is_referer == "engine") $seaadd = $langms['sea_system'].': '.$engine[$host_referer]['4'].', '.$langms['sea_request'].': '.stripcslashes($request); else $seaadd = $http_referer;

if (strlen($seaadd) > 60)
$seareferadd = substr ($seaadd, 0, 60)."...";
else
$seareferadd = $seaadd;
if ($_REQUEST['seainform'] == "no") {
@set_cookie("dle_seatrans", "no", 365);
$close_all .= $langms['sea_oncookie'];
} else {
$close_all .= <<<HTML
<span style="cursor: pointer;" onclick="window.location='$PHP_SELF?seainform=no'">{$langms['sea_incookie']}</span>
HTML;
}

$ajax .= <<<HTML
<!-- Функция ПереходИнфо™ от модуля "Переходы" (http://getdle.com) -->
<style type="text/css" media="all">
.ipzout {
	background:#ffffe1; border-bottom:1px solid #aca899; padding:6px;
}
.ipzover {
	background:#316ac5; border-bottom:1px solid #aca899; padding:6px;
}
.ipzout .text {
	color:#000; font-size:11px; font-family:tahoma;
}
.ipzover .text {
	color:#fff; font-size:11px; font-family:tahoma;
}
#izp {
	cursor:default; width:100%; border-bottom:1px solid #716f64;
}
.ipzout .closebtn {
	background: url({$config['http_home_url']}engine/skins/referer/close.gif) no-repeat 0px 0px;
}
.ipzover .closebtn {
	background: url({$config['http_home_url']}engine/skins/referer/close.gif) no-repeat 0px -8px;
	cursor: hand;
}
</style>
<div id="izp">
<div onmouseover="this.className='ipzover';" onmouseout="this.className='ipzout';" align="left" class="ipzout">
<div align="right" style='float:right; padding-right: 1px; padding-top: 2px; width:22px;' onclick='document.getElementById("izp").style.display="none"; return false;'><input class="closebtn" type="image" id="submit" src="{$config['http_home_url']}engine/skins/referer/btn.gif" title="{$langms['sea_close']}" style="border: 0px;" /></div>
<div class="text">{$langms['sea_addref']}: {$seareferadd}. {$close_all}</div></div>
</div>
HTML;
}

# Автоматическая очистка базы
if(date('H') == "23" && $confms['max_ref'] != "0") {
$countst = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_referer");
if ($countst['count'] >= $confms['max_ref']) $db->query("TRUNCATE TABLE " . PREFIX . "_referer");
}
}
};

# Вывод блока переходов
if($confms['func_block'] == "yes"){
$block_sea = intval($confms['block_sea']);

if($confms['typ_block']) {
$query = $db->query("SELECT * FROM " . PREFIX . "_referer WHERE type = 'engine' ORDER BY date DESC LIMIT 0,{$block_sea}");

$tpl->load_template('referer.tpl');

while($row = $db->get_row($query)){

	$tpl->set_block("'\\[type-site\\](.*?)\\[/type-site\\]'si","");
	$tpl->set('[type-perf]',"");
        $tpl->set('[/type-perf]',"");

	if(!$engine[$row['host']]['2']) $engine[$row['host']]['2'] = "?";
	if (strlen($row['request']) > $confms['block_link']) $search = substr ($row['request'], 0, $confms['block_link'])."..."; else $search = $row['request'];

	$tpl->set('{search}', $search);
	$tpl->set('{perf}', strip_tags($engine[$row['host']]['3']));
	$tpl->set('{perf-link}', "<a href=\"{$engine[$row['host']]['4']}\" target=\"_blank\">{$engine[$row['host']]['3']}</a>");
	$tpl->set('{letter}', $engine[$row['host']]['1']);
	$tpl->set('{icon}', $engine[$row['host']]['5']);
	$tpl->set('{hits}', $row['hits']);
	$tpl->set('[slink]', "<a href=\"".$row['uri']."\">");
	$tpl->set('[/slink]', "</a>");
#	if ($confms['sea_flate'] == "yes") $row['referer'] = @gzinflate(base64_decode($row['referer']));
	$tpl->set('[elink]', "<a href=\"".$row['referer']."\" target=\"_blank\">");
	$tpl->set('[/elink]', "</a>");

	$tpl->compile('referer');

}

		$tpl->clear();

} else {
$query = $db->query("SELECT * FROM " . PREFIX . "_referer ORDER BY date DESC LIMIT 0,{$block_sea}");

$tpl->load_template('referer.tpl');

while($row = $db->get_row($query)){

	$tpl->set_block("'\\[type-perf\\](.*?)\\[/type-perf\\]'si","");
	$tpl->set('[type-site]',"");
        $tpl->set('[/type-site]',"");

	if ($confms['sea_flate'] == "yes" && $row['type'] == "engine")
#	$row['referer'] = @gzinflate(base64_decode($row['referer']));
	$tpl->set('[rlink]', "<a href=\"".$row['referer']."\" target=\"_blank\">");
	$tpl->set('[/rlink]', "</a>");
	$tpl->set('{hits}', $row['hits']);
	$tpl->set('{domain}', $row['host']);
	$tpl->compile('referer');

		}

		$tpl->clear();
	}
}

if (!$tpl->result['referer']) $tpl->result['referer'] = $langms['no_seatrans'];

?>