<?php


if(!defined('DATALIFEENGINE')) {
	die("Hacking attempt!");
}
// Показывать пользователям ОС? 1 - Да, 0 - Нет
$onl_options['showos']=1;
// Показывать пользователям группу? 1 - Да, 0 - Нет
$onl_options['showgroup']=1;
// Показывать пользователям браузер? 1 - Да, 0 - Нет
$onl_options['showbrowser']=1;
// Показывать местоаоложение пользователя на сайте? 1 - Да, 0 - Нет
$onl_options['showlocation']=1;

// Где взять базу? Качаем ее отсюда http://www.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz (переодически обновляется)
// Распаковываем и заливаем/копируем GeoLiteCity.dat в engine/modules/geoip/
// Путь к БД Стран и Городов
$onl_options['bdcitycountry'] = ENGINE_DIR."/modules/geoip/GeoLiteCity.dat";

// Показывать пользователям браузер? 1 - Да, 0 - Нет
$onl_options['showcountry']=1;
// Показывать пользователям браузер? 1 - Да, 0 - Нет
$onl_options['showcity']=1;

// Показывать последних посетителей и роботов? 1 - Да, 0 - Нет
$onl_options['lastusershow']=1;
// Количество последних посетителей
$onl_options['lastcounts']=20;

// Время в течении которого, посетитель считается онлайн. (в минутах)
$onl_options['timeonline']=5;

// Табличный стиль, выводимых, списков в онлайн. 1 - Да, 0 - Нет
$onl_options['tablestyle']=1;
// Стиль таблицы в онлайн
$onl_options['tableclass']="onl_table";
// Число колонок при табличном стиле в онлайн.
$onl_options['tablecols']=2;

// Табличный стиль, выводимых, списков в оффлайн. 1 - Да, 0 - Нет
$onl_options['tablelstyle']=1;
// Стиль таблицы в оффлайн
$onl_options['tablelclass']="onl_table";
// Число колонок при табличном стиле в оффлайн.
$onl_options['tablelcols']=2;

// Разделитель между никами в списках (разделителя нету, если выбран табличный стиль)
$onl_options['separator']=", ";

define( 'ASC_AZ', 1000 );
define( 'DESC_AZ', 1001 );
define( 'ASC_NUM', 1002 );
define( 'DESC_NUM', 1003 );

$lclnt = array ( "1" => array ( "file" => ENGINE_DIR."/data/mvcnet.txt",
                                "name" => "SimIX",
                                "masks" => array(),
                              ),
                 "2" => array ( "file" => ENGINE_DIR."/data/allownet.txt",
                                "name" => "SimIX",
                                "masks" => array(),
                              )
               );

function online_username ($id, $nick) {
	switch ($id) {
	case 0 : // Робот
		$user_opentag = '<span class="b_link" onmouseout="className=\'b_link\'" onmouseover="className=\'b_link_on\'">';
		$user_closetag = '</span>';
		break;
	case 1 : // Администратор
		$user_opentag = '<span class="a_link" onmouseout="className=\'a_link\'" onmouseover="className=\'a_link_on\'">';
		$user_closetag = '</span>';
		break;
	case 2 : // Главный редактор
		$user_opentag = '<span class="e_link" onmouseout="className=\'e_link\'" onmouseover="className=\'e_link_on\'">';
		$user_closetag = '</span>';
		break;
	case 3 : // Журналист
		$user_opentag = '<span class="j_link" onmouseout="className=\'j_link\'" onmouseover="className=\'j_link_on\'">';
		$user_closetag = '</span>';
		break;
	case 4 : // Посетитель
		$user_opentag = '<span class="u_link" onmouseout="className=\'u_link\'" onmouseover="className=\'u_link_on\'">';
		$user_closetag = '</span>';
		break;
	}
	return $user_opentag.$nick.$user_closetag;
}

function online_gbm($num) {
	$res = "";
	for ($i = 1;$i <= 32;$i++) { if ($i <= $num) { $res .= "1"; } else { $res .= "0"; } }
	return $res;
}

function online_isipinnet($ip,$net,$mask) {
  $lnet=ip2long($net);
  $lip=ip2long($ip);
  $binnet=str_pad( decbin($lnet),32,"0","STR_PAD_LEFT" );
  $firstpart=substr($binnet,0,$mask);
  $binip=str_pad( decbin($lip),32,"0","STR_PAD_LEFT" );
  $firstip=substr($binip,0,$mask);

  return(strcmp($firstpart,$firstip)==0);
}

function online_isipinnetarray($ip) {
	global $lclnt;
	if (isset($ip)) {
		foreach ($lclnt as $k => $v) {
			foreach($lclnt[$k]['masks'] as $subnet) {
				if (strpos($subnet,"/")) list($net,$mask)=split("/",$subnet); else {
					$net = $subnet;
					$mask = 24;
				}
			    if(online_isipinnet($ip,$net,$mask)){
					return $lclnt[$k]['name'];
				}
			}
		}
	}
	return false;
}

function online_initnetarray() {
	global $lclnt;
	foreach ($lclnt as $k => $v) {
		if (file_exists($lclnt[$k]['file'])) {
			$fh = fopen ($lclnt[$k]['file'], "r");
			$contents = fread ($fh, filesize ($lclnt[$k]['file']));
			fclose ($fh);
			$contents = str_replace("\r", "", $contents);
			$lclnt[$k]['masks'] = explode("\n", $contents);
		}
	}
}

function online_stickysort ($arr, $field, $sort_type, $sticky_fields = array()) {
   $i = 0;
   foreach ($arr as $value) {
       $is_contiguous = true;
       if(!empty($grouped_arr)) {
           $last_value = end($grouped_arr[$i]);

           if(!($sticky_fields == array())) {
               foreach ($sticky_fields as $sticky_field) {
                   if ($value[$sticky_field] <> $last_value[$sticky_field]) {
                       $is_contiguous = false;
                       break;
                   }
               }
           }
       }
       if ($is_contiguous)
           $grouped_arr[$i][] = $value;
       else
           $grouped_arr[++$i][] = $value;
   }
   $code = '';
   switch($sort_type) {
       case ASC_AZ:
           $code .= 'return strcasecmp($a["'.$field.'"], $b["'.$field.'"]);';
           break;
       case DESC_AZ:
           $code .= 'return (-1*strcasecmp($a["'.$field.'"], $b["'.$field.'"]));';
           break;
       case ASC_NUM:
           $code .= 'return ($a["'.$field.'"] - $b["'.$field.'"]);';
           break;
       case DESC_NUM:
           $code .= 'return ($b["'.$field.'"] - $a["'.$field.'"]);';
           break;
   }

   $compare = create_function('$a, $b', $code);

   foreach($grouped_arr as $grouped_arr_key=>$grouped_arr_value)
       usort ( $grouped_arr[$grouped_arr_key], $compare );

   $arr = array();
   foreach($grouped_arr as $grouped_arr_key=>$grouped_arr_value)
       foreach($grouped_arr[$grouped_arr_key] as $grouped_arr_arr_key=>$grouped_arr_arr_value)
           $arr[] = $grouped_arr[$grouped_arr_key][$grouped_arr_arr_key];

   return $arr;
}

function online_changeend($value,$v1,$v2,$v3) {
	$endingret="";
	if (substr($value,-1)==1) $endingret = $v1;
	if (substr($value,-1)==2) $endingret = $v2;
	if (substr($value,-1)==3) $endingret = $v2;
	if (substr($value,-1)==4) $endingret = $v2;
	if (substr($value,-2)==11) $endingret = $v3;
	if (substr($value,-2)==12) $endingret = $v3;
	if (substr($value,-2)==13) $endingret = $v3;
	if (substr($value,-2)==14) $endingret = $v3;
	if (empty($endingret)) $endingret = $v3;
	return $endingret;
}

function online_timeagos($timestamp) {
	global $lang;
	$current_time = time();
	$difference = $current_time - $timestamp;

	$lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

	for ($val = sizeof($lengths) - 1; ($val >= 0) && (($number = $difference / $lengths[$val]) <= 1); $val--);

	if ($val < 0) $val = 0;
	$new_time = $current_time - ($difference % $lengths[$val]);
	$number = floor($number);

	switch ($val) {
		case 0: $stamp = online_changeend($number,$lang['online_stamp01'],$lang['online_stamp02'],$lang['online_stamp03']); break;
		case 1: $stamp = online_changeend($number,$lang['online_stamp11'],$lang['online_stamp12'],$lang['online_stamp13']); break;
		case 2: $stamp = online_changeend($number,$lang['online_stamp21'],$lang['online_stamp22'],$lang['online_stamp23']); break;
		case 3: $stamp = online_changeend($number,$lang['online_stamp31'],$lang['online_stamp32'],$lang['online_stamp33']); break;
		case 4: $stamp = online_changeend($number,$lang['online_stamp41'],$lang['online_stamp42'],$lang['online_stamp43']); break;
		case 5: $stamp = online_changeend($number,$lang['online_stamp51'],$lang['online_stamp52'],$lang['online_stamp53']); break;
		case 6: $stamp = online_changeend($number,$lang['online_stamp61'],$lang['online_stamp62'],$lang['online_stamp63']); break;
		case 5: $stamp = online_changeend($number,$lang['online_stamp71'],$lang['online_stamp72'],$lang['online_stamp73']); break;
	}
	//$text = sprintf("%d %s ", $number, $stamp);
	//Для отсечения секунд
	//if ($val) $text = sprintf("%d %s ", $number, $stamp);
	$text = sprintf("%d %s ", $number, $stamp);
	if (($val >= 1) && (($current_time - $new_time) > 0)){
		$text .= online_timeagos($new_time);
	}
	return $text;
}

function online_skip($text) {
	$text=mysql_escape_string($text);
	$text=stripslashes($text);
	$text = str_replace("'",'`',$text);
	$text = str_replace('"','`',$text);
	return $text;
}

function online_robots($useragent) {
	global $member_id;
	$r_or=false;

	# Выясняем принадлежность к поисковым роботам
	$remap_agents = array (
		'antabot'			=>	'antabot (private)',
		'aport'				=>	'Aport',
		'Ask Jeeves'		=>	'Ask Jeeves',
		'Asterias'			=>	'Singingfish Spider',
		'Baiduspider'		=>	'Baidu Spider',
		'Feedfetcher-Google'=>	'Feedfetcher-Google',
		'GameSpyHTTP'		=>	'GameSpy HTTP',
		'GigaBlast'			=>	'GigaBlast',
		'Gigabot'			=>	'Gigabot',
		'Accoona'			=>	'Google.com',
		'Googlebot-Image'	=>	'Googlebot-Image',
		'Googlebot'			=>	'Googlebot',
		'grub-client'		=>	'Grub',
		'gsa-crawler'		=>	'Google Search Appliance',
		'Slurp'				=>	'Inktomi Spider',
		'slurp@inktomi'		=>	'Hot Bot',

		'lycos'				=>	'Lycos.com',
		'whatuseek'			=>	'What You Seek',
		'ia_archiver'		=>	'Alexa',
		'is_archiver'		=>	'Archive.org',
		'archive_org'		=>	'Archive.org',

		'YandexBlog'		=>	'YandexBlog',
		'YandexSomething'	=>	'YandexSomething',
		'Yandex'			=>	'Yandex',
		'StackRambler'		=>	'Rambler',

		'WebAlta Crawler'	=>	'WebAlta Crawler',

		'Yahoo'				=>	'Yahoo',
		'zyborg@looksmart'	=>	'WiseNut',
		'WebCrawler'		=>	'Fast',
		'Openbot'			=>	'Openfind',
		'TurtleScanner'		=>	'Turtle',
		'libwww'			=>	'Punto',

		'msnbot'			=>  'MSN',
		'MnoGoSearch'		=>  'mnoGoSearch',
		'booch'				=>  'booch_Bot',
		'WebZIP'			=>	'WebZIP',
		'GetSmart'			=>	'GetSmart',
		'NaverBot'			=>	'NaverBot',
		'Vampire'			=>	'Net_Vampire',
		'ZipppBot'			=>	'ZipppBot',

		'W3C_Validator'		=>	'W3C Validator',
		'W3C_CSS_Validator'	=>	'W3C CSS Validator',
	);

	$remap_agents=array_change_key_case($remap_agents, CASE_LOWER);

	$pmatch_agents="";
	foreach ($remap_agents as $k => $v) {
		$pmatch_agents.=$k."|";
	}
	$pmatch_agents=substr_replace($pmatch_agents, '', strlen($pmatch_agents)-1, 1);

	if (preg_match( '/('.$pmatch_agents.')/i', $useragent, $match ))

	if (count($match)) {
		$r_or = @$remap_agents[strtolower($match[1])];
	}
	
	return $r_or;
}

function online_os($useragent) {
	$os = 'Unknown';
	# Выясняем операционную систему
	if(strpos($useragent, "Win") !== false) {
		if(strpos($useragent, "NT 6.0") !== false) $os = 'Windows Vista';
		if(strpos($useragent, "NT 5.2") !== false) $os = 'Windows Server 2003 или XPx64';
		if(strpos($useragent, "NT 5.1") !== false || strpos($useragent, "Win32") !== false || strpos($useragent, "XP")) $os = 'Windows XP';
		if(strpos($useragent, "NT 5.0") !== false) $os = 'Windows 2000';
		if(strpos($useragent, "NT 4.0") !== false || strpos($useragent, "3.5") !== false) $os = 'Windows NT';
		if(strpos($useragent, "Me") !== false) $os = 'Windows Me';
		if(strpos($useragent, "98") !== false) $os = 'Windows 98';
		if(strpos($useragent, "95") !== false) $os = 'Windows 95';
	}

	if(strpos($useragent, "Linux")    !== false
	|| strpos($useragent, "Lynx")     !== false
	|| strpos($useragent, "Unix")     !== false) $os = 'Linux';
	if(strpos($useragent, "Macintosh")!== false
	|| strpos($useragent, "PowerPC")) $os = 'Macintosh';
	if(strpos($useragent, "OS/2")!== false) $os = 'OS/2';
	if(strpos($useragent, "BeOS")!== false) $os = 'BeOS';

	return $os;
}

function online_browser($useragent) {
	$browser_type = "Unknown";
	$browser_version = "";
	# Определяем тип и версию браузера

# php 5.2
# 	if (ereg('MSIE ([0-9].[0-9]{1,2})', $useragent, $version)) {


	if (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $useragent, $version)) {
		$browser_type = "Internet Explorer";
		$browser_version = $version[1];

# php 5.2
# 	} elseif (eregi('Opera ([0-9].[0-9]{1,2})', $useragent, $version)) {

		} elseif (preg_match('/Opera ([0-9].[0-9]{1,2})/', $useragent, $version)) {
		$browser_type = "Opera";
		$browser_version = $version[1];
	} elseif (preg_match('/Opera/i', $useragent)) {
		$browser_type = "Opera";
		$val = stristr($useragent, "opera");

# php 5.2
# 		if (eregi("/", $val)){

		if (preg_match("/\//", $val)){
			$val = explode("/",$val);
			$browser_type = $val[0];
			$val = explode(" ",$val[1]);
			$browser_version  = $val[0];
		} else {
			$val = explode(" ",stristr($val,"opera"));
			$browser_type = $val[0];
			$browser_version  = $val[1];
		}
	} elseif (preg_match('/Firefox\/(.*)/i', $useragent, $version)) {
		$browser_type = "Firefox";
		$browser_version = $version[1];
	} elseif (preg_match('/SeaMonkey\/(.*)/i', $useragent, $version)) {
		$browser_type = "SeaMonkey";
		$browser_version = $version[1];
	} elseif (preg_match('/Minimo\/(.*)/i', $useragent, $version)) {
		$browser_type = "Minimo";
		$browser_version = $version[1];
	} elseif (preg_match('/K-Meleon\/(.*)/i', $useragent, $version)) {
		$browser_type = "K-Meleon";
		$browser_version = $version[1];
	} elseif (preg_match('/Epiphany\/(.*)/i', $useragent, $version)) {
		$browser_type = "Epiphany";
		$browser_version = $version[1];
	} elseif (preg_match('/Flock\/(.*)/i', $useragent, $version)) {
		$browser_type = "Flock";
		$browser_version = $version[1];
	} elseif (preg_match('/Camino\/(.*)/i', $useragent, $version)) {
		$browser_type = "Camino";
		$browser_version = $version[1];
	} elseif (preg_match('/Firebird\/(.*)/i', $useragent, $version)) {
		$browser_type = "Firebird";
		$browser_version = $version[1];
	} elseif (preg_match('/Safari/i', $useragent)) {
		$browser_type = "Safari";
		$browser_version = "";
	} elseif (preg_match('/avantbrowser/i', $useragent)) {
		$browser_type = "Avant Browser";
		$browser_version = "";
	} elseif (preg_match('/America Online Browser [^0-9,.,a-z,A-Z]/i', $useragent)) {
		$browser_type = "Avant Browser";
		$browser_version = "";
	} elseif (preg_match('/libwww/i', $useragent)) {
		if (preg_match('/amaya/i', $useragent)) {
			$browser_type = "Amaya";
			$val = explode("/",stristr($useragent,"amaya"));
			$val = explode(" ", $val[1]);
			$browser_version = $val[0];
		} else {
			$browser_type = "Lynx";
			$val = explode("/",$useragent);
			$this->version = $val[1];
			$browser_version = $val[1];
		}
		
# php 5.2
# } elseif (ereg('Mozilla/([0-9].[0-9]{1,2})', $useragent, $version)) {
		
} elseif (preg_match('/Mozilla\/([0-9].[0-9]{1,2})/', $useragent, $version)) {
		$browser_type = "Netscape";
		$browser_version = $version[1];
	}

	return $browser_type." ".$browser_version;
}



# Сбор необходимой информации

# Узнаем юзер агента посетителя
$onl_useragent = online_skip($_SERVER['HTTP_USER_AGENT']);

# Определяем IP и Proxy, если он есть
$ip=$proxy="0.0.0.0";
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$ip = online_skip($_SERVER['HTTP_X_FORWARDED_FOR']);
	$proxy = online_skip($_SERVER['REMOTE_ADDR']);
} else $ip = online_skip($_SERVER['REMOTE_ADDR']);

$ip = str_replace(",", ".", $ip);
$proxy = str_replace(",", ".", $proxy);
if (empty($proxy)) $proxy = "0.0.0.0";

if (file_exists($onl_options['bdcitycountry'])) {
	if ($member_id['user_id']){
		require_once ENGINE_DIR."/modules/geoip/geoipcity.inc";
		$gi = geoip_open($onl_options['bdcitycountry'], GEOIP_STANDARD);
		if ($proxy!="0.0.0.0") $record = geoip_record_by_addr($gi, $proxy); else $record = geoip_record_by_addr($gi, $ip);
		$onl_country = $record->country_name;
		$onl_city = $record->city;
		geoip_close($gi);
	}
}

online_initnetarray();

# Определяем где сейчас посетитель

$onl_location = "";

if ($subaction == "addcomment") $onl_location .= "%addcomments%"; // Добавляет комментарий в: 
elseif ($subaction == "showfull") $onl_location .= "%readnews%"; // Читает новость: 
elseif ($category_id) $onl_location .= "%incategory%"; // Находится в разделе: 
elseif (!empty($nam_e)) $onl_location .= "%posin%"; // Находится в: 
elseif (empty($nam_e) && empty($titl_e)) $onl_location .= "%mainpage%"; // Находится на главной страницк.

if ($nam_e) $onl_location .= $lang['online_locstart'].trim($nam_e).$lang['online_locend'];
if ($titl_e) $onl_location .= $lang['online_locstart'].trim($titl_e).$lang['online_locend'];

$onl_location = online_skip($onl_location);

if (!$_TIME) $_TIME = time()+($config['date_adjust']*60);
$_TIME=intval($_TIME);

if (!$member_id['user_id']){
	$onl_botname = online_skip(online_robots($onl_useragent));
} else $db->query("UPDATE " . PREFIX . "_users SET lastdate={$_TIME}, country='{$onl_country}', city='{$onl_city}', useragent='{$onl_useragent}', logged_ip='{$ip}', logged_proxy='{$proxy}', location='{$onl_location}' WHERE user_id='{$member_id['user_id']}'");

//if (isset($_COOKIE['lastusername'])) $lastusername = $_COOKIE['lastusername']; else $lastusername = "";
if (isset($_COOKIE['dle_onl_session'])) $onl_session = $_COOKIE['dle_onl_session']; else $onl_session = session_id();

# Выполняем запросы
# Удаляем лишних пользователей из списка в БД 
$datecut = $_TIME - $onl_options['timeonline']*60;
$db->query("DELETE FROM " . PREFIX . "_online WHERE lastdate < $datecut");

# Определяем количество пользователей по критерию и в зависимости от результата, выполняем запрос обноваления или создания записи о пользователе
$onl_query["search_us"]="SELECT COUNT(*) as count FROM " . PREFIX . "_online WHERE session='{$onl_session}'";
$onl_count_user = $db->super_query($onl_query["search_us"]);

if($onl_count_user['count']) {
	$onl_query["update_us"]="UPDATE " . PREFIX . "_online SET uid=".intval($member_id['user_id']).", lastdate={$_TIME}, location='{$onl_location}', useragent='{$onl_useragent}', ip='{$ip}', proxy='{$proxy}' WHERE session='{$onl_session}'";
	$db->query($onl_query["update_us"]);
} else {
	$onl_query["create_us"]="INSERT INTO " . PREFIX . "_online (uid, session, lastdate, location, useragent, ip, proxy ) VALUES(".intval($member_id['user_id']).", '{$onl_session}', {$_TIME}, '{$onl_location}', '{$onl_useragent}', '{$ip}', '{$proxy}')";
	$db->query($onl_query["create_us"]);
	setcookie("dle_onl_session",session_id(), $_TIME+3600*24*365, "/", $domain);
}

# Выбираем пользователей из БД
$datecut = $_TIME - $onl_options['timeonline']*60;
$sql = "SELECT
			user.name AS user_name, user.user_group, user.foto, user.country, user.city,
			online.uid, online.useragent, online.session, online.ip, online.proxy, online.lastdate, online.location
		FROM " . PREFIX . "_online AS online
		LEFT JOIN " . PREFIX . "_users AS user ON(user.user_id = online.uid)
		WHERE online.lastdate > $datecut
		ORDER BY name ASC";
$onl_userlist_q = $db->query($sql);

# Обработка полученных данных из БД
$onl_guests=$onl_users=$onl_robots=$onl_lusers=$onl_lrobots=0;
while($onl_userlist = $db->get_array($onl_userlist_q)) {
	if($onl_userlist['uid']==false) {
		$current_robot = online_skip(online_robots($onl_userlist['useragent']));
		if ($current_robot!="") {
			if ($onl_onlinebots[$current_robot]['lastdate']<$onl_userlist['lastdate']) {
				$onl_onlinebots[$current_robot]['name']=$current_robot;
				$onl_onlinebots[$current_robot]['lastdate']=$onl_userlist['lastdate'];
				$onl_onlinebots[$current_robot]['ip']=$onl_userlist['ip'];
				$onl_onlinebots[$current_robot]['proxy']=$onl_userlist['proxy'];
				$onl_onlinebots[$current_robot]['location']=$onl_userlist['location'];
			}
		} else $onl_guests++;
	} else {
		if ($onl_onlineusers[$onl_userlist['uid']]['lastdate']<$onl_userlist['lastdate']) {
			$onl_onlineusers[$onl_userlist['uid']]['name']=$onl_userlist['user_name'];
			$onl_onlineusers[$onl_userlist['uid']]['lastdate']=$onl_userlist['lastdate'];
			$onl_onlineusers[$onl_userlist['uid']]['group']=$onl_userlist['user_group'];
			$onl_onlineusers[$onl_userlist['uid']]['useragent']=$onl_userlist['useragent'];
			$onl_onlineusers[$onl_userlist['uid']]['foto']=$onl_userlist['foto'];
			$onl_onlineusers[$onl_userlist['uid']]['ip']=$onl_userlist['ip'];
			$onl_onlineusers[$onl_userlist['uid']]['proxy']=$onl_userlist['proxy'];
			$onl_onlineusers[$onl_userlist['uid']]['location']=$onl_userlist['location'];
			$onl_onlineusers[$onl_userlist['uid']]['country']=$onl_userlist['country'];
			$onl_onlineusers[$onl_userlist['uid']]['city']=$onl_userlist['city'];
		}
	}
}

if (count($onl_onlineusers)) {
	//$sticky_fields = array( 'name' );
	//$onl_onlineusers = online_stickysort( $onl_onlineusers, 'age', ASC_AZ, $sticky_fields );
	$onl_onlineusers = online_stickysort($onl_onlineusers, 'name', ASC_AZ);
	foreach ($onl_onlineusers as $uid => $value) {
		if(empty($onl_onlineusers[$uid]['foto'])) $onl_avator='<center><img src="'.$config['http_home_url'].'templates/'.$config['skin'].'/images/noavatar.png" alt="" /></center>';
		else $onl_avator='<center><img src="'.$config['http_home_url'].'uploads/fotos/'.$onl_onlineusers[$uid]['foto'].'" alt="" /></center>';

		if($onl_options['showos']==true || @$member_id['user_group']==1)
		$onl_descr=$lang['online_os'].online_skip(online_os($onl_onlineusers[$uid]['useragent'])).'<br />';

		if($onl_options['showbrowser']==true || @$member_id['user_group']==1)
		$onl_descr.=$lang['online_browser'].online_skip(online_browser($onl_onlineusers[$uid]['useragent'])).'<br />';

		$netname="";
		if ($onl_onlineusers[$uid]['proxy']!="0.0.0.0") $netname = online_isipinnetarray($onl_onlineusers[$uid]['proxy']);
		else $netname = online_isipinnetarray($onl_onlineusers[$uid]['ip']);

		if ($netname!=false) {
			$onl_descr.=$lang['online_net'].$netname.'<br />';
		} elseif (file_exists($onl_options['bdcitycountry'])) {
			if (empty($onl_onlineusers[$uid]['country'])) $onl_onlineusers[$uid]['country']="--";
			if($onl_options['showcountry']==true || @$member_id['user_group']==1)
			$onl_descr.=$lang['online_country'].$onl_onlineusers[$uid]['country'].'<br />';

			if (empty($onl_onlineusers[$uid]['city'])) $onl_onlineusers[$uid]['city']="--";
			if($onl_options['showcity']==true || @$member_id['user_group']==1)
			$onl_descr.=$lang['online_city'].$onl_onlineusers[$uid]['city'].'<br />';
		}

		if(@$member_id['user_group']==1) {
			$onl_descr.='<b>IP:</b>&nbsp;'.$onl_onlineusers[$uid]['ip'].'<br />';
			if ($onl_onlineusers[$uid]['proxy']!="0.0.0.0") $onl_descr.='<b>Proxy:</b>&nbsp;'.$onl_onlineusers[$uid]['proxy'].'<br />';
		}
		if($onl_options['showgroup']==true || @$member_id['user_group']==1)
		$onl_descr.=$lang['online_group'].$user_group[$onl_onlineusers[$uid]['group']]['group_name'].'<br />';

		$onl_descr.=$lang['online_was'].online_timeagos($onl_onlineusers[$uid]['lastdate']).$lang['online_back'].'<br />';

		$onl_onlineusers[$uid]['location'] = str_replace("%addcomments%", $lang['online_paddcomments'], $onl_onlineusers[$uid]['location']); 
		$onl_onlineusers[$uid]['location'] = str_replace("%readnews%", $lang['online_preadnews'], $onl_onlineusers[$uid]['location']); 
		$onl_onlineusers[$uid]['location'] = str_replace("%incategory%", $lang['online_pincategory'], $onl_onlineusers[$uid]['location']); 
		$onl_onlineusers[$uid]['location'] = str_replace("%posin%", $lang['online_pposin'], $onl_onlineusers[$uid]['location']); 
		$onl_onlineusers[$uid]['location'] = str_replace("%mainpage%", $lang['online_pmainpage'], $onl_onlineusers[$uid]['location']); 

		if($onl_options['showlocation']==true || @$member_id['user_group']==1)
		$onl_descr.=$lang['online_location'].$onl_onlineusers[$uid]['location'].'<br />';
		$onl_descr = str_replace("\n", " ", $onl_descr);

		$onl_hint = htmlspecialchars($onl_avator,ENT_QUOTES).htmlspecialchars($onl_descr,ENT_QUOTES);
		$zavhint = "onmouseover=\"showhint('{$onl_hint}', this, event, '180px');\"";

		$current_time = $_TIME-$onl_onlineusers[$uid]['lastdate'];
		$onl_users++;
		if ($config['allow_alt_url'] == "yes")
			$onl_ulink[] = online_username($onl_onlineusers[$uid]['group'], "\n<a ".$zavhint.' href="'.$config['http_home_url'].'user/'.urlencode($onl_onlineusers[$uid]['name']).'/">'.$onl_onlineusers[$uid]['name'].'</a>');
		else
			$onl_ulink[] = online_username($onl_onlineusers[$uid]['group'], "\n<a ".$zavhint.' href="'.$config['http_home_url'].'?subaction=userinfo&amp;user='.urlencode($onl_onlineusers[$uid]['name']).'">'.$onl_onlineusers[$uid]['name'].'</a>');
		unset($onl_descr);
	}
}

//unset ($onl_userlist_q, $onl_onlineusers);
$onl_userlist_q=$onl_onlineusers=array();

$datecut = $_TIME - $onl_options['timeonline']*60;
$sql = "SELECT 
			user_id, name, user_group, foto, lastdate, country, city, useragent, logged_ip, logged_proxy, location
		FROM " . PREFIX . "_users
		WHERE lastdate < $datecut 
		ORDER BY lastdate DESC LIMIT ".intval($onl_options['lastcounts']);
//		ORDER BY name ASC LIMIT ".intval($onl_options['lastcounts']);
$onl_lastusers_q = $db->query($sql);

# Обработка полученных данных из БД
while($onl_lastuser = $db->get_array($onl_lastusers_q)) {
	$onl_lastusers[$onl_lastuser['user_id']]['name']=$onl_lastuser['name'];
	$onl_lastusers[$onl_lastuser['user_id']]['group']=$onl_lastuser['user_group'];
	$onl_lastusers[$onl_lastuser['user_id']]['useragent']=$onl_lastuser['useragent'];
	$onl_lastusers[$onl_lastuser['user_id']]['lastdate']=$onl_lastuser['lastdate'];
	$onl_lastusers[$onl_lastuser['user_id']]['foto']=$onl_lastuser['foto'];
	$onl_lastusers[$onl_lastuser['user_id']]['ip']=$onl_lastuser['logged_ip'];
	$onl_lastusers[$onl_lastuser['user_id']]['proxy']=$onl_lastuser['logged_proxy'];
/*	if (file_exists($onl_options['bdcitycountry']) && empty($onl_lastuser['country'])) {
		$gi = geoip_open($onl_options['bdcitycountry'], GEOIP_STANDARD);
		if ($onl_lastuser['logged_proxy']!="0.0.0.0") $record = geoip_record_by_addr($gi, $onl_lastuser['logged_proxy']); else $record = geoip_record_by_addr($gi, $onl_lastuser['logged_ip']);
		$onl_lastusers[$onl_lastuser['user_id']]['country']=$record->country_name;
		geoip_close($gi);
	} else $onl_lastusers[$onl_lastuser['user_id']]['country']=$onl_lastuser['country'];
	if (file_exists($onl_options['bdcitycountry']) && empty($onl_lastuser['city'])) {
		$gi = geoip_open($onl_options['bdcitycountry'], GEOIP_STANDARD);
		if ($onl_lastuser['logged_proxy']!="0.0.0.0") $record = geoip_record_by_addr($gi, $onl_lastuser['logged_proxy']); else $record = geoip_record_by_addr($gi, $onl_lastuser['logged_ip']);
		$onl_lastusers[$onl_lastuser['user_id']]['city']=$record->city;
		geoip_close($gi);
	} else $onl_lastusers[$onl_lastuser['user_id']]['city']=$onl_lastuser['city'];*/
	$onl_lastusers[$onl_lastuser['user_id']]['country']=$onl_lastuser['country'];
	$onl_lastusers[$onl_lastuser['user_id']]['city']=$onl_lastuser['city'];
	$onl_lastusers[$onl_lastuser['user_id']]['location']=$onl_lastuser['location'];
}
if (count($onl_lastusers)) {
	$onl_lastusers = online_stickysort($onl_lastusers, 'name', ASC_AZ);
	foreach ($onl_lastusers as $uid => $value) {
		if(empty($onl_lastusers[$uid]['foto'])) $onl_avator='<center><img src="'.$config['http_home_url'].'templates/'.$config['skin'].'/images/noavatar.png" alt="" /></center>';
		else $onl_avator='<center><img src="'.$config['http_home_url'].'uploads/fotos/'.$onl_lastusers[$uid]['foto'].'" alt="" /></center>';

		if($onl_options['showos']==true || @$member_id['user_group']==1)
		$onl_descr=$lang['online_os'].online_skip(online_os($onl_lastusers[$uid]['useragent'])).'<br />';

		if($onl_options['showbrowser']==true || @$member_id['user_group']==1)
		$onl_descr.=$lang['online_browser'].online_skip(online_browser($onl_lastusers[$uid]['useragent'])).'<br />';

		$netname="";
		if ($onl_lastusers[$uid]['proxy']!="0.0.0.0") $netname = online_isipinnetarray($onl_lastusers[$uid]['proxy']);
		else $netname = online_isipinnetarray($onl_lastusers[$uid]['ip']);

		if ($netname!=false) {
			$onl_descr.=$lang['online_net'].$netname.'<br />';
		} elseif (file_exists($onl_options['bdcitycountry'])) {
			if (empty($onl_lastusers[$uid]['country'])) $onl_lastusers[$uid]['country']="--";
			if($onl_options['showcountry']==true || @$member_id['user_group']==1)
			$onl_descr.=$lang['online_country'].$onl_lastusers[$uid]['country'].'<br />';

			if (empty($onl_lastusers[$uid]['city'])) $onl_lastusers[$uid]['city']="--";
			if($onl_options['showcity']==true || @$member_id['user_group']==1)
			$onl_descr.=$lang['online_city'].$onl_lastusers[$uid]['city'].'<br />';
		}

		if(@$member_id['user_group']==1) {
			$onl_descr.='<b>IP:</b>&nbsp;'.$onl_lastusers[$uid]['ip'].'<br />';
			if ($onl_lastusers[$uid]['proxy']!="0.0.0.0") $onl_descr.='<b>Proxy:</b>&nbsp;'.$onl_lastusers[$uid]['proxy'].'<br />';
		}
		if($onl_options['showgroup']==true || @$member_id['user_group']==1)
		$onl_descr.=$lang['online_group'].$user_group[$onl_lastusers[$uid]['group']]['group_name'].'<br />';

		$onl_descr.=$lang['online_was'].online_timeagos($onl_lastusers[$uid]['lastdate']).$lang['online_back'].'<br />';

		$onl_lastusers[$uid]['location'] = str_replace("%addcomments%", $lang['online_lpaddcomments'], $onl_lastusers[$uid]['location']); 
		$onl_lastusers[$uid]['location'] = str_replace("%readnews%", $lang['online_lpreadnews'], $onl_lastusers[$uid]['location']); 
		$onl_lastusers[$uid]['location'] = str_replace("%incategory%", $lang['online_lpincategory'], $onl_lastusers[$uid]['location']); 
		$onl_lastusers[$uid]['location'] = str_replace("%posin%", $lang['online_lpposin'], $onl_lastusers[$uid]['location']); 
		$onl_lastusers[$uid]['location'] = str_replace("%mainpage%", $lang['online_lpmainpage'], $onl_lastusers[$uid]['location']); 

		if($onl_options['showlocation']==true || @$member_id['user_group']==1)
		$onl_descr.=$lang['online_location'].$onl_lastusers[$uid]['location'].'<br />';
		$onl_descr = str_replace("\n", " ", $onl_descr);

		$onl_hint = htmlspecialchars($onl_avator,ENT_QUOTES).htmlspecialchars($onl_descr,ENT_QUOTES);
		$zavhint = "onmouseover=\"showhint('{$onl_hint}', this, event, '180px');\"";

		$onl_lusers++;
		if ($config['allow_alt_url'] == "yes")
			$onl_lulink[] = online_username($onl_lastusers[$uid]['group'], "\n<a ".$zavhint.' href="'.$config['http_home_url'].'user/'.urlencode($onl_lastusers[$uid]['name']).'/">'.$onl_lastusers[$uid]['name']."</a>");
		else
			$onl_lulink[] = online_username($onl_lastusers[$uid]['group'], "\n<a ".$zavhint.' href="'.$config['http_home_url'].'?subaction=userinfo&amp;user='.urlencode($onl_lastusers[$uid]['name']).'">'.$onl_lastusers[$uid]['name']."</a>");
	}
}

//unset($onl_lastusers_q,$onl_lastusers);
$onl_lastusers_q=$onl_lastusers=$lclnt=array();

if (count($onl_onlinebots)) {
	//$onl_onlinebots = online_stickysort($onl_onlinebots, 'name', ASC_AZ);
	foreach ($onl_onlinebots as $name => $value) {
		$onl_descr=$lang['online_group'].$lang['online_robots'].'<br />';

		if(@$member_id['user_group']==1) {
			$onl_descr.='<b>IP:</b>&nbsp;'.$onl_onlinebots[$name]['ip'].'<br />';
			if ($onl_onlinebots[$name]['proxy']!="0.0.0.0") $onl_descr.='<b>Proxy:</b>&nbsp;'.$onl_onlinebots[$name]['proxy'].'<br />';
		}
		$onl_descr.=$lang['online_was'].online_timeagos($onl_onlinebots[$name]['lastdate']).$lang['online_back'].'<br />';

		$onl_onlinebots[$name]['location'] = str_replace("%addcomments%", $lang['online_paddcomments'], $onl_onlinebots[$name]['location']); 
		$onl_onlinebots[$name]['location'] = str_replace("%readnews%", $lang['online_preadnews'], $onl_onlinebots[$name]['location']); 
		$onl_onlinebots[$name]['location'] = str_replace("%incategory%", $lang['online_pincategory'], $onl_onlinebots[$name]['location']); 
		$onl_onlinebots[$name]['location'] = str_replace("%posin%", $lang['online_pposin'], $onl_onlinebots[$name]['location']); 
		$onl_onlinebots[$name]['location'] = str_replace("%mainpage%", $lang['online_pmainpage'], $onl_onlinebots[$name]['location']); 

		if($onl_options['showlocation']==true || @$member_id['user_group']==1)
		$onl_descr.=$lang['online_location'].$onl_onlinebots[$name]['location'].'<br />';

		$zavhint = 'onmouseover="showhint(\''.htmlspecialchars($onl_descr,ENT_QUOTES).'\', this, event, \'180px\');"';

		$onl_robots++;
		$onl_blink[] = '<span '.$zavhint.' style="cursor:hand;">'.$name.'</span>';
		unset($onl_descr);
	}
}

//unset($onl_onlinebots);
$onl_onlinebots=array();

$tpl->load_template('online.tpl');

# Составление списка пользователей
$onl_userlist="\n<script type=\"text/javascript\" src=\"".$config['http_home_url']."engine/skins/default.js\"></script>\n";

if ($onl_options['tablestyle']) $onl_userlist .= "<table cellpaddong=\"0\" cellspacing=\"0\" border=\"0\" class=\"{$onl_options['tableclass']}\"><tr>";
if (count($onl_ulink)==0) {
	if ($onl_options['tablestyle']) $onl_userlist .= "<td colspan=\"{$onl_options['tablecols']}\">".$lang['online_notusers']."</td>"; else $onl_userlist .= $lang['online_notusers']; 
}
for($i=0;$i<count($onl_ulink);$i++) {
	if ($onl_options['tablestyle']) {
		if ($i % $onl_options['tablecols'] == 0) $onl_userlist .= "</tr><tr>";
		$onl_userlist .= "<td colspan=\"{$onl_options['tablecols']}\">".$onl_ulink[$i]."</td>"; 
	} else $onl_userlist .= $onl_ulink[$i].$onl_options['separator'];
}
if (!$onl_options['tablestyle']) {
	if(count($onl_ulink)) $onl_userlist=substr_replace($onl_userlist, '', strlen($onl_userlist)-strlen($onl_options['separator']), strlen($onl_options['separator']));
} else $onl_userlist .= "</tr></table>";


if ($onl_options['tablestyle']) $onl_botlist = "<table cellpaddong=\"0\" cellspacing=\"0\" border=\"0\" class=\"{$onl_options['tableclass']}\"><tr>";
if (count($onl_blink)==0) {
	if ($onl_options['tablestyle']) $onl_botlist .= "<td colspan=\"{$onl_options['tablecols']}\">".$lang['online_notbots']."</td>"; else $onl_botlist = $lang['online_notbots']; 
}
for($i=0;$i<count($onl_blink);$i++) {
	if ($onl_options['tablestyle']) {
		if ($i % $onl_options['tablecols'] == 0) $onl_botlist .= "</tr><tr>";
		$onl_botlist .= "<td colspan=\"{$onl_options['tablecols']}\">".$onl_blink[$i]."</td>"; 
	} else $onl_botlist .= $onl_blink[$i].$onl_options['separator'];
}
if (!$onl_options['tablestyle']) {
	if(count($onl_blink)) $onl_botlist=substr_replace($onl_botlist, '', strlen($onl_botlist)-strlen($onl_options['separator']), strlen($onl_options['separator']));
} else $onl_botlist .= "</tr></table>";

if ($onl_options['lastusershow']) {
	if ($onl_options['tablelstyle']) $onl_luserlist = "<table cellpaddong=\"0\" cellspacing=\"0\" border=\"0\" class=\"{$onl_options['tablelclass']}\"><tr>";
	if (count($onl_lulink)==0) {
		if ($onl_options['tablelstyle']) $onl_luserlist .= "<td colspan=\"{$onl_options['tablelcols']}\">".$lang['online_notlusers']."</td>"; else $onl_luserlist = $lang['online_notlusers']; 
	}
	for($i=0;$i<count($onl_lulink);$i++) {
		if ($onl_options['tablelstyle']) {
			if ($i % $onl_options['tablelcols'] == 0) $onl_luserlist .= "</tr><tr>";
			$onl_luserlist .= "<td colspan=\"{$onl_options['tablelcols']}\">".$onl_lulink[$i]."</td>"; 
		} else $onl_luserlist .= $onl_lulink[$i].$onl_options['separator'];
	}
	if (!$onl_options['tablelstyle']) {
		if(count($onl_lulink)) $onl_luserlist=substr_replace($onl_luserlist, '', strlen($onl_luserlist)-strlen($onl_options['separator']), strlen($onl_options['separator']));
	} else $onl_luserlist .= "</tr></table>";

	if ($onl_options['tablestyle']) $onl_lbotlist = "<table cellpaddong=\"0\" cellspacing=\"0\" border=\"0\" class=\"{$onl_options['tableclass']}\"><tr>";
	if (count($onl_lblink)==0) {
		if ($onl_options['tablestyle']) $onl_lbotlist .= "<td colspan=\"{$onl_options['tablelcols']}\">".$lang['online_notlbots']."</td>"; else $onl_lbotlist = $lang['online_notlbots']; 
	}

	$tpl->set('{lusers}',$onl_lusers);
	$tpl->set('{luserlist}',$onl_luserlist);
	$tpl->set("[last]","");
	$tpl->set("[/last]","");
} else $tpl->set_block("'\\[last\\](.*?)\\[/last\\]'si","");

//unset($onl_ulink, $onl_lulink, $onl_blink);
$onl_ulink=$onl_lulink=$onl_blink=array();

$tpl->set('{users}',$onl_users);
$tpl->set('{guest}',$onl_guests);
$tpl->set('{robots}',$onl_robots);
$tpl->set('{all}',($onl_users+$onl_guests+$onl_robots));

$tpl->set('{userlist}',$onl_userlist);
$tpl->set('{botlist}',$onl_botlist);

//unset($onl_userlist, $onl_botlist);
$onl_userlist=$onl_botlist="";

$tpl->compile('online');
$tpl->clear();
?>