<?php

@error_reporting(7);
@ini_set('display_errors', true);
@ini_set('html_errors', false);

define('DATALIFEENGINE', true);
define('ROOT_DIR', '../..');
define('ENGINE_DIR', '..');

include ENGINE_DIR.'/data/config.php';

require_once ROOT_DIR.'/language/'.$config['langs'].'/referer.lng';
include ENGINE_DIR.'/data/referer.perf.php';

$config['charset'] = ($langms['charset'] != '') ? $langms['charset'] : $config['charset'];

require_once ENGINE_DIR.'/inc/functions.inc.php';

@header("HTTP/1.0 200 OK");
@header("HTTP/1.1 200 OK");
@header("Cache-Control: no-cache, must-revalidate, max-age=0");
@header("Expires: 0");
@header("Pragma: no-cache");
@header("Content-type: text/css; charset=".$config['charset']);

if (in_array($_REQUEST['site'], $engines)) $_REQUEST['site'] = $engine[$_REQUEST['site']]['0'];

$parse_seo = @file_get_contents('http://getdle.com/seo/index.php?url='.$_REQUEST['site']);

$seo_site = explode("||", $parse_seo);

$info .= <<<HTML
<span style="padding: 3px;">
<img alt="Это скриншот сайта {$_REQUEST['site']}." src="http://images.websnapr.com/?url={$_REQUEST['site']}&size=t&nocache=90" align="left">
PageRang: <b>{$seo_site['0']}</b>; Яндекс тИЦ: <b>{$seo_site['1']}</b>;<br />
Каталог DMOZ: {$seo_site['2']}<br />
Каталог Yandex: {$seo_site['3']}<br /><br />
<a href="http://{$_REQUEST['site']}" target="_blank">Перейти на сайт &rarr;</a>
</span>
HTML;

die($info);

?>