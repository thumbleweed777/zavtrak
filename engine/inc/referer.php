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
 Назначение: Вывод списка переходов
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

include (ENGINE_DIR.'/data/referer.perf.php');
include (ENGINE_DIR.'/data/referer.conf.php');
include_once ROOT_DIR.'/language/'.$config['langs'].'/referer.lng';

//--------------------------------------------
// Начало функций модуля
//--------------------------------------------
function style_sea() {
echo <<<HTML
<style type="text/css">
.refer-del { border: #dbe0ea 1px solid; background-color: #f6f7f9; padding: 4px; }
</style>
HTML;
}
function opentable() {
echo <<<HTML
<table width="100%">
    <tr>
        <td width="4"><img src="engine/skins/images/tl_lo.gif" width="4" height="4" border="0"></td>
        <td background="engine/skins/images/tl_oo.gif"><img src="engine/skins/images/tl_oo.gif" width="1" height="4" border="0"></td>
        <td width="6"><img src="engine/skins/images/tl_ro.gif" width="6" height="4" border="0"></td>
    </tr>
    <tr>
        <td background="engine/skins/images/tl_lb.gif"><img src="engine/skins/images/tl_lb.gif" width="4" height="1" border="0"></td>
        <td style="padding:5px;" bgcolor="#FFFFFF">
HTML;
}
function closetable() {
echo <<<HTML
    </td>
        <td background="engine/skins/images/tl_rb.gif"><img src="engine/skins/images/tl_rb.gif" width="6" height="1" border="0"></td>
    </tr>
    <tr>
        <td><img src="engine/skins/images/tl_lu.gif" width="4" height="6" border="0"></td>
        <td background="engine/skins/images/tl_ub.gif"><img src="engine/skins/images/tl_ub.gif" width="1" height="6" border="0"></td>
        <td><img src="engine/skins/images/tl_ru.gif" width="6" height="6" border="0"></td>
    </tr>
</table>
HTML;
}
function tableheader($value) {
echo <<<HTML
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$value}</div></td>
    </tr>
</table>
HTML;
 unterline();
}
function unterline() {
echo <<<HTML
<div class="unterline"></div>
HTML;
}
function jseascript() {
global $langms, $dle_login_hash;
echo <<<HTML
<script type="text/javascript" src="engine/ajax/menu.js"></script>
<script type="text/javascript" src="engine/ajax/dle_ajax.js"></script>
<script language="javascript" type="text/javascript">
<!--
var ajax = new dle_ajax();
function MenuBuild( mid, host ){

var menu=new Array()
menu[0]='<a onclick="checkinfo(\'' + host + '\', \'' + mid + '\'); return false;" href="#" title="{$langms['sea_m_00']}">{$langms['sea_tp']}</a>';
menu[1]='<a onClick="document.location=\'$PHP_SELF?mod=referer&action=all&host=' + host + '\'; return(false)" href="#"  title="{$langms['sea_m_01']}">{$langms['sea_inc_01']}</a>';
menu[2]='<a onClick="javascript:confirmdelete(' + mid + '); return(false)" href="#" title="{$langms['sea_m_02']}">{$langms['sea_inc_02']}</a>';

return menu;
}

function checkupd (){
	document.getElementById( 'update' ).innerHTML = '<div style="background: lightyellow;border:1px dotted rgb(190,190,190); padding: 5px;margin-top: 7px;margin-right: 10px;"><img src="engine/skins/referer/loading.gif" border="0" align="absmiddle"> Пожалуйста, подождите ...</div>';
 
	var varsString = "";

	ajax.requestFile = "engine/ajax/referer.upd.php";
	ajax.element = 'update';
	ajax.method = 'POST';

	ajax.sendAJAX(varsString);

	return false;
}

function checkinfo ( host, id ){
	document.getElementById( 'main_box' + id ).innerHTML = '<img src="engine/skins/referer/loading.gif" border="0" align="absmiddle"> Пожалуйста, подождите...';

	var varsString = "site=" + host;

	ajax.requestFile = "engine/ajax/referer.php";
	ajax.element = 'main_box' + id;
	ajax.method = 'POST';

	ajax.sendAJAX(varsString);

	return false;
}
function confirmdelete(id){

document.getElementById( 'refer-' + id ).innerHTML = '<div style="padding-top:4px;padding-bottom:1px;"><img src="engine/skins/referer/loading.gif" border="0" align="absmiddle"></div>';

	var varsString = "id=" + id;

	ajax.setVar("user_hash", "{$dle_login_hash}");
	ajax.requestFile = "engine/ajax/referer.del.php";
	ajax.element = 'refer-' + id;
	ajax.method = 'POST';

	ajax.sendAJAX(varsString);

	return false;

}
//-->
</script>
HTML;
}
function jseascript2() {
global $langms, $dle_login_hash;
echo <<<HTML
<script type="text/javascript" src="engine/ajax/menu.js"></script>
<script type="text/javascript" src="engine/ajax/dle_ajax.js"></script>
<script language="javascript" type="text/javascript">
<!--
var ajax = new dle_ajax();
function MenuBuild( mid, host ){

var menu=new Array()
menu[0]='<a onclick="checkinfo(\'' + host + '\', \'' + mid + '\'); return false;" href="#" title="{$langms['sea_m_00']}">{$langms['sea_tp']}</a>';
menu[1]='<a onClick="javascript:confirmdelete(' + mid + '); return(false)" href="#">{$langms['sea_inc_02']}</a>';

return menu;
}
function checkinfo ( host, id ){
	document.getElementById( 'main_box' + id ).innerHTML = '{$langms['sea_inc_09']} ...';

	var varsString = "site=" + host;

	ajax.requestFile = "engine/ajax/referer.php";
	ajax.element = 'main_box' + id;
	ajax.method = 'POST';

	ajax.sendAJAX(varsString);

	return false;
}
function confirmdelete(id){
document.getElementById( 'refer-' + id ).innerHTML = '<div style="padding-top:4px;padding-bottom:1px;"><img src="engine/skins/referer/loading.gif" border="0" align="absmiddle"></div>';

	var varsString = "id=" + id;

	ajax.setVar("user_hash", "{$dle_login_hash}");
	ajax.requestFile = "engine/ajax/referer.del.php";
	ajax.element = 'refer-' + id;
	ajax.method = 'POST';

	ajax.sendAJAX(varsString);

	return false;

}
//-->
</script>
HTML;
}
function seamenutrans() {
global $langms;
echo <<<HTML
<form action="$PHP_SELF?mod=referer" method=post>
<select name="action" style="color: #000002; font-size: 11px; font-family: tahoma;">
<option>{$langms['sea_inc_04']}</option>
<option value="graph">{$langms['sea_graph']}</option>
<option value="options">{$langms['sea_inc_05']}</option>
<option value="about">{$langms['sea_about']}</option>
<option value="cerdel">{$langms['sea_inc_07']}</option>
</select>
HTML;
}

function referer_navig() {
global $langms;
opentable();
if ($_REQUEST['action']) {
tableheader("<div style=\"float:left;\">".$langms['referer_navig']."</div><div style=\"float:right; padding-right: 6px;\"><a href=\"$PHP_SELF?mod=referer\">Вернуться на главную</a></div>");
} else {
tableheader($langms['referer_navig']);
}
echo <<<HTML
<table width="100%">
  <tr>
	<td width="50%"><div class="quick"><a href="$PHP_SELF?mod=referer&action=options"><img src="engine/skins/referer/options.png" border="0" align="left"><h3>{$langms['sea_inc_05']}</h3>{$langms['sea_opt_info']}</a></div></td>
	<td width="50%"><div class="quick"><a href="$PHP_SELF?mod=referer&action=graph"><img src="engine/skins/referer/graph.png" border="0" align="left"><h3>{$langms['sea_graph']}</h3>{$langms['sea_graph_info']}</a></div></td>
  </tr>
  <tr>
	<td width="50%"><div class="quick"><a href="$PHP_SELF?mod=referer&action=cerdel"><img src="engine/skins/referer/clear.png" border="0" align="left"><h3>{$langms['sea_clear']}</h3>{$langms['sea_clear_info']}</a></div></td>
	<td width="50%"><div class="quick"><a href="$PHP_SELF?mod=referer&action=about"><img src="engine/skins/referer/about.png" border="0" align="left"><h3>{$langms['sea_info']}</h3>{$langms['sea_info_info']}</a></div></td>
  </tr>
</table>
HTML;
closetable();
}
//--------------------------------------------
// Конец функций модуля
//--------------------------------------------

//--------------------------------------------
// Настройка модуля
//--------------------------------------------
if ($_REQUEST['action'] == "options") {
function showRow($title="", $description="", $field="", $line="no", $gomod=false) {
        global $lang;
echo <<<HTML
<tr>
<td style="padding:4px" class="option"><b>{$title}</b><br />
<span class=small>{$description}</span> </td>
<td width=394 align=middle>{$field}</td></tr>
HTML;
if($gomod) echo "<tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=2></td></tr>";
                if($line == "yes") echo "<tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=2></td></tr>";
                        $bg = ""; $i++;
    }
function makeDropDown($options, $name, $selected, $off)
    {
        $output = "<select $off size=1 name=\"$name\">\r\n";
        foreach($options as $value=>$description)
        {
          $output .= "<option value=\"{$value}\"";
          if($selected == $value){ $output .= " selected "; }
          $output .= ">{$description}</option>\n";
        }
        $output .= "</select>";
        return $output;
    }
if ($confms['func_block'] == "no") { $disofftwo = "disabled><input type=hidden name='save_config[block_link]' value='{$confms['block_link']}'"; $disoffone = "disabled><input type=hidden name='save_config[block_sea]' value='{$confms['block_sea']}'"; }
if ($confms['types'] !== "0") $disoff = "disabled";

    echoheader("", "");
    referer_navig();
    opentable();
    tableheader("{$langms['sea_set_title']}");

echo <<<HTML
<form action="$PHP_SELF?mod=referer&action=options" method="post">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
HTML;
foreach ($user_group as $group) $sys_group_arr[$group['id']] = $group['group_name'];
showRow($langms['sea_set_max'], $langms['sea_set_max_i'], "<input type=text style='text-align: center;' size=30 class=edit name='save_config[max_ref]' value='{$confms['max_ref']}'>", "", "basic");
showRow($langms['sea_addi'], $langms['sea_addi_i'], makeDropDown(array("yes"=>$langms['sea_yes'],"no"=>$langms['sea_no']), "save_config[sea_addi]", "{$confms['sea_addi']}", ""), "", "all");
showRow($langms['sea_flate'], $langms['sea_flate_i'], makeDropDown(array("yes"=>$langms['sea_yes'],"no"=>$langms['sea_no']), "save_config[sea_flate]", "{$confms['sea_flate']}", ""), "", "all");
showRow($langms['sea_ignor'], $langms['sea_ignor_i'], "<textarea class=edit style='width:250px;height:50px;' name='save_config[site_ignor]'>{$confms['site_ignor']}</textarea>", "", "all");
showRow($langms['sea_sort'], $langms['sea_sort_i'], makeDropDown(array("date"=>$langms['sea_sort_date'],"url"=>$langms['sea_sort_url'],"hits"=>$langms['sea_sort_hits']), "save_config[sea_sort]", "{$confms['sea_sort']}", "", "") , "", "all");
showRow($langms['sea_msort'], $langms['sea_msort_i'], makeDropDown(array("ASC"=>$langms['sea_msort_asc'],"DESC"=>$langms['sea_msort_desc']), "save_config[sea_msort]", "{$confms['sea_msort']}", "", "") , "", "all");
showRow($langms['sea_f_block'], $langms['sea_f_block_i'], makeDropDown(array("yes"=>$langms['sea_yes'],"no"=>$langms['sea_no']), "save_config[func_block]", "{$confms['func_block']}", "") , "", "all");
showRow($langms['sea_set_b_t'], $langms['sea_set_b_t_i'], makeDropDown(array("0"=>$langms['sea_set_types_all'],"1"=>$langms['sea_set_types_p']), "save_config[typ_block]", "{$confms['typ_block']}", "") , "", "all");
showRow($langms['sea_block_sea'], $langms['sea_block_sea_i'], "<input type=text style='text-align: center;' size=30 class=edit name='save_config[block_sea]' value='{$confms['block_sea']}' {$disoffone}>", "", "basic");
foreach ($user_group as $group) $sys_group_arr[$group['id']] = $group['group_name'];
showRow($langms['sea_block_link'], $langms['sea_block_link_i'], "<input type=text style='text-align: center;' size=30 class=edit name='save_config[block_link]' value='{$confms['block_link']}' {$disofftwo}>", "", "basic");
echo <<<HTML
</td>
  </tr>
  <tr>
    <td colspan="3">
<div class="hr_line"></div>
</td>
  </tr>
  <tr>
    <td colspan="3"><input type=hidden name=mod value="referer"><input type=hidden name=action value="save">
        <input type=hidden name=savecfg value="savecfg"><input type=submit class=edit value="{$langms['sea_save']}"> <input type=button value="{$langms['func_msg']}" class=edit onclick="window.location='$PHP_SELF?mod=referer'"></td>
  </tr>
</table></form>
HTML;
closetable();
echofooter();

//--------------------------------------------
// Сохранение настроек модуля
//--------------------------------------------
} elseif ($_REQUEST['action'] == "save") {
if ($_REQUEST['savecfg'] != "savecfg") include ENGINE_DIR.'/data/referer.conf.php';
if($_REQUEST['savecfg'] == "savecfg")
{
    $find[]     = "'\r'";
        $replace[]      = "";
    $find[]     = "'\n'";
        $replace[]      = "";

        $handler = fopen(ENGINE_DIR.'/data/referer.conf.php', "w");
        fwrite($handler, "<?PHP \r\n\$confms = array (\r\n");
        foreach($save_config as $name => $value)
        {
                $value=trim(stripslashes ($value));
                $value=htmlspecialchars ($value, ENT_QUOTES);
                $value = preg_replace($find,$replace,$value);
                fwrite($handler, "'{$name}' => \"{$value}\",\r\n");
        }

        fwrite($handler, ");\r\n?>");
        fclose($handler);

        clear_cache ();
msg("info", $langms['sea_info'], "{$langms['sea_save_conf']}<br><br><input type='button' value=\"{$langms['sea_main']}\" class='bbcodes' onclick=\"window.location='$PHP_SELF?mod=referer'\"> &nbsp; <input type='button' value=\"  {$langms['sea_back']}  \" class='bbcodes' onclick=\"window.location='$PHP_SELF?mod=referer&action=options'\">", "");
 }

//--------------------------------------------
// Запрос на очистку базы
//--------------------------------------------
 } elseif ($_REQUEST['action'] == "cerdel") {

$sel .= <<<HTML
<select name="type" style="color: #000002; font-size: 11px; font-family: tahoma;">
<option value="all">все</option>
<option value="engine">поисковые</option>
<option value="referer">обычные</option>
</select>
HTML;

msg("info", $langms['sea_info'], "<form method=\"post\"><input type=hidden name=mod value=\"referer\"><input type=hidden name=action value=\"goclear\">{$langms['sea_del_n']} {$sel} {$langms['sea_del_k']}<br /><br /><input type='submit' value='   {$langms['sea_yes']}   ' class='edit'> &nbsp; <input type='button' value='  {$langms['sea_no']}  ' class='edit' onclick=\"window.location='$PHP_SELF?mod=referer'\"></form>", "");

//--------------------------------------------
// Очистка базы
//--------------------------------------------
 } elseif ($_REQUEST['action'] == "goclear") {

if ($_REQUEST['type'] == "referer") {
$db->query ("DELETE FROM " . PREFIX . "_referer WHERE type = 'referer'");
} elseif ($_REQUEST['type'] == "engine") {
$db->query ("DELETE FROM " . PREFIX . "_referer WHERE type = 'engine'");
} else {
$db->query("TRUNCATE TABLE " . PREFIX . "_referer");
}

msg("info", $langms['sea_info'], $langms['sea_goclear'], "$PHP_SELF?mod=referer");

//--------------------------------------------
// Самые популярные сайты
//--------------------------------------------
 } elseif ($_REQUEST['action'] == "graph") {

$result = $db->query("SELECT host, hits FROM " . PREFIX . "_referer");

while($row = $db->get_array($result)){
if ($row['host']) {
$one_host = explode(' ', $row['host']);
foreach ($one_host as $host) {
if (isset($site[$host]))
$site[$host] = ($site[$host]) + ($row['hits']);
else
$site[$host] = $row['hits']; }
}}

@arsort($site);
@reset($site);

echo($site[key]);

$site = @array_slice($site, 0, 15);

if(!$site) $site = array();
foreach ($site as $key => $value) {
if (in_array($key, $engines)) $key = $engine[$key]['0'];
$data .= "{$key}={$value}||";

}

$x = 40;
$y = 15;

$total = 0;
$max = 0;

$items= explode("||",$data);

while (list($key,$item) = each($items))
{
if ($item)
{
$pos = strpos($item,"=");
$value = substr($item,$pos+1,strlen($item));
$total = $total + $value;
}
}
reset($items);

$title = $items[key];
while (list($key,$item) = each($items))
{
if ($item)
{
$pos = strpos($item,"=");
$item_title = substr($item,0,$pos);
$value = substr($item,$pos+1,strlen($item));

if ($total<>0) {
$procent = intval(round(($value/$total)*100))."%";
} else {
$procent = "0%";
}
$content .= <<<HTML
      <tr>
        <td width="100">{$item_title}</td>
        <td class="value"><img src="engine/skins/referer/bar.png" alt="" width="{$procent}" height="16" />{$procent} ({$value})</td>
      </tr>
      <tr><td background=engine/skins/images/mline.gif height=1 colspan=2></td></tr>
HTML;

}



}
$summa = @array_sum($site);
    echoheader("", "");
	referer_navig();
    opentable();
    tableheader($langms['sea_graph']);

echo <<<HTML
<style type="text/css">
td.value {
	background-image: url(engine/skins/referer/bg_fade.png);
	background-repeat: repeat-x;
	background-position: left top;
	border-bottom: none;
	background-color:transparent;


}

.value img {
	vertical-align: middle;
	margin: 5px 5px 5px 0;
}

.gtable {
	color: #999898;
	background-image: url(engine/skins/referer/bg.png);
	background-repeat:repeat-x;
	background-position:left top;
	width: 100%;

}
.gtable td {
	padding-left:3px;
}

</style>

<table width=100% border=0>
HTML;
 if (!$data) {
  echo "<tr><td height=30 align=center class=navigation>- {$langms['sea_m_no']} -</td></tr>";
 } else {
echo <<<HTML
<tr><td height=30 align=center>
 <table width="100%" cellspacing="0" cellpadding="0" class="gtable">
{$content}
 </table>
</td></tr>
HTML;
 }
 echo "</table>";

echo <<<HTML
<div class="hr_line"></div><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
HTML;

if(!$summa) $summa = 0;

echo<<<HTML
  <td>{$langms['sea_graph_i']}: {$summa}</td><td align="right"><table border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td style="padding:4px" class=navigation>{$langms['sea_you_action']}:</td>
      <td style="padding:4px">
HTML;
seamenutrans();
echo <<<HTML
</td>
      <td style="padding:4px"><input type=submit class=edit value=" {$langms['sea_execute']} "></form></td>
    </tr>
  </table>
  </td>
  </tr>
</table>
HTML;

    closetable();
    echofooter();

//--------------------------------------------
// О модуле
//--------------------------------------------
 } elseif ($_REQUEST['action'] == "about") {

    echoheader("", "");
	referer_navig();
	jseascript();

    opentable();
    tableheader($langms['sea_robots']);

     foreach ($engines as $key => $value)
     {
       		$robots .= "<img src=\"engine/skins/referer/".$engine[$value]['5']."\" align=\"absmiddle\" border=\"0\"> ".$engine[$value]['3']." &nbsp;";
     }

echo <<<HTML
<div class="navigation" style="padding: 1px;">
{$robots}
</div>
HTML;

    closetable();

    opentable();
    tableheader($langms['sea_about']);

echo <<<HTML
<div class="navigation" style="padding: 1px;">
<strong>{$langms['sea_name_mod']}</strong>: Переходы <br />
<strong>{$langms['sea_ver_mod']}</strong>: {$langms['version']}<br />
<strong>{$langms['sea_authomod']}</strong>: <a href="http://vkontakte.ru/id30100905" target="_blank">Konokhov N. aka ko1yan</a><br />
<strong>{$langms['sea_sitemod']}</strong>: <a href="http://getdle.com/" target="_blank">GetDLE Lab Group</a><br />
<strong>Icons by</strong>: <a href="http://dryicons.com/" target="_blank">DryIcons</a><br /><br />Пожелания к модулю, <a href="http://refer.reformal.ru/" target="_blank">оставляйте здесь</a>.<br /><br />
<div class="hr_line"></div>
<input type="button" class="edit" value="{$langms['sea_down_ver']}" onclick="checkupd(); return false;"> <span id="update"></span>

</div>
HTML;

    closetable();

    opentable();
    tableheader($langms['sea_donate']);

echo <<<HTML
<div class="navigation" style="padding: 1px;">
<strong>Переходы</strong> — бесплатный модуль для движка DataLife Engine, однако его развитие отнимает много времени. Ваши пожертвования будут способствывать дальнейшему совершенствованию модуля! Спасибо, всем кто поможет в развитие модуля. <br /> Кошельки WebMoney: <strong>R209700962040</strong>, <strong>Z175599618079</strong> и <strong>E298466561225</strong>; Яндекс.Деньги: <strong>41001163321026</strong>.
</div>
HTML;

    closetable();
    echofooter();

//--------------------------------------------
// Показ переходов одного хоста и поиск
//--------------------------------------------
 } elseif ($_REQUEST['action'] == "all") {

$host = $_REQUEST['host'];

if(!$host) msg("info", $langms['sea_info'], $langms['sea_search_no'], "$PHP_SELF?mod=referer");

if($_REQUEST['search'] == "search") {
$titlehost = $langms['sea_all_title']." \"".$host."\"";
$where_sql = "WHERE (host like '%$host%' or url like '%$host%' or search like '%$host%')";
} else {
if($engine[$host]['4']) $titlehost = $langms['sea_allperf']." ".$engine[$host]['4']; else $titlehost = $langms['sea_allhost']." www.".$host;
$where_sql = "WHERE host = '$host'";
}

    echoheader("", "");
	referer_navig();
    jseascript2();
    style_sea();
if (stristr($host, 'google')) $host='google';
$result = $db->query("SELECT * FROM " . PREFIX . "_referer {$where_sql} order by date DESC");
$total_hits = 0;
while($row = $db->get_array($result)){
$row['position'] = str_replace("%news%", $langms['sea_readnews'], $row['position']);
$row['position'] = str_replace("%cat%", $langms['sea_incat'], $row['position']);
$row['position'] = str_replace("%posin%", $langms['sea_posin'], $row['position']);
$row['position'] = str_replace("%main%", $langms['sea_mainpage'], $row['position']);

$siteurls = '<a href="'.$config['http_home_url'].substr($row['uri'], 1).'" target="_blank">'.$row['position'].'</a>';

$datetime = langdate("j M, H:i:s", $row['date']);
$total_hits = $total_hits+$row['hits'];
if (!$row['user_ip']) $ip = "not detected"; else $ip = "<a href=\"$PHP_SELF?mod=iptools&action=find&ip={$row['user_ip']}\">{$row['user_ip']}</a>";

if ($row['request']) {
if (strlen($row['request']) > 55) $linksa = substr(stripslashes($row['request']), 0, 55)."...";
else
$linksa = stripslashes($row['request']);

if ($confms['sea_flate'] == "yes")
$row['referer'] = @gzinflate(base64_decode($row['referer']));

$links = "<a href=\"{$engine[$row['host']]['4']}\" target=_blank><img src=\"engine/skins/referer/{$engine[$row['host']]['5']}\" align=\"absmiddle\" border=\"0\"></a> {$engine[$row['host']]['3']}: <a href=\"{$row['referer']}\" target=_blank><font color=#000>{$linksa}</font></a>";
} else {
if (strlen($row['referer']) > 55) $linksa = substr ($row['referer'], 0, 55)."...";
else
$linksa = $row['referer'];
$links = "<a href=\"{$row['referer']}\" target=_blank><font color=#000>{$linksa}</font></a>";
}

$entries .= <<<HTML
<tr>
    <td style="padding: 2px;"><div id="refer-{$row['id']}">{$links}<br /><span class="navigation" id=main_box{$row['id']}>{$siteurls}</span></div></td>
    <td width="150" align="center">{$datetime}</td>
<td width="110" align="center">{$ip}</td>
    <td width="85" align="center">{$row['hits']}</td>
    <td width="60" align="center"><a onClick="return dropdownmenu(this, event, MenuBuild('{$row['id']}', '{$row['host']}'), '150px')" href="#"><img src="engine/skins/images/browser_action.gif" border="0"></a></td>
  </tr>
<tr><td background=engine/skins/images/mline.gif height=1 colspan=6></td></tr>
HTML;
}
    opentable();
    tableheader($titlehost);
 echo "<table width=100% border=0>";
 if (!$entries) {
  echo "<tr><td align=center height=30 colspan=5 align=center class=navigation>- {$langms['sea_all_no']} -</td></tr>";
 } else {
echo <<<HTML
   <tr>
    <td style="padding: 3px;">{$langms['sea_all_trans']}:</td>
    <td>
      <div align="center">{$langms['sea_all_time']}</div>
    </td>
<td>
      <div align="center">{$langms['sea_all_ip']}</div>
    </td>
    <td>
      <div align="center">{$langms['sea_all_tran']}</div>
    </td>
    <td align="right" style="padding: 3px;">{$langms['sea_action']}</td>
  </tr>
<tr><td colspan=5><div class="hr_line"></div></td></tr>
HTML;
  echo $entries;
 }
 echo "</table>";

echo <<<HTML
<div class="hr_line"></div><table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
HTML;
echo<<<HTML
  <td>{$langms['sea_all_a']}: {$total_hits}</td><td align="right"><table border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td style="padding:4px" class=navigation>{$langms['sea_you_action']}:</td>
      <td style="padding:4px">
HTML;
seamenutrans();
echo <<<HTML
</td>
      <td style="padding:4px"><input type=submit class=edit value=" {$langms['sea_execute']} "></form></td>
    </tr>
  </table>
  </td>
  </tr>
</table>
HTML;

    closetable();
    echofooter();

//--------------------------------------------
// Просмотр списка переходов
//--------------------------------------------
 } else {
    echoheader("", "");
    $iconv_status = function_exists('iconv') ? 'yes' : 'no';
if($iconv_status == "no"){
opentable();
tableheader($langms['sea_m_work']);
echo <<<HTML
<div style="padding-left: 5px;" class=navigation>{$langms['sea_m_work_i']}</div>
HTML;
closetable(); }

        jseascript();
        style_sea();

	referer_navig();

if(!intval($sea_per_page)){ $sea_per_page = 50; }
if (!isset($start_from)) $start_from = 0;
if (!$confms['sea_sort']) $confms['sea_sort'] = "date";
if (!$confms['sea_msort']) $confms['sea_msort'] = "DESC";
$total_hits = 0;
$numbers = 50;

$result = $db->query("SELECT * FROM " . PREFIX . "_referer ORDER BY {$confms['sea_sort']} {$confms['sea_msort']} LIMIT $start_from,$numbers");

    $flag = 1;
    if($start_from == "0"){ $start_from = ""; }
    $i = $start_from;
    $entries_showed = 0;

$entries = "";
while($row = $db->get_array($result)){

$row['position'] = str_replace("%news%", $langms['sea_readnews'], $row['position']);
$row['position'] = str_replace("%cat%", $langms['sea_incat'], $row['position']);
$row['position'] = str_replace("%posin%", $langms['sea_posin'], $row['position']);
$row['position'] = str_replace("%main%", $langms['sea_mainpage'], $row['position']);

$siteurls = '<a href="'.$config['http_home_url'].substr($row['uri'], 1).'" target="_blank">'.$row['position'].'</a>';

if($row['type'] == "1") $row['link'] = $sea_perf[$row['host']]['link'];

$datetime = langdate("j M, H:i:s", $row['date']);
$total_hits = $total_hits+$row['hits'];

if (!$row['user_ip']) $ip = "not detected"; else $ip = "<a href=\"$PHP_SELF?mod=iptools&action=find&ip={$row['user_ip']}\">{$row['user_ip']}</a>";

        $i++;

if ($row['request']) {
if (strlen($row['request']) > 55) $linksa = substr(stripslashes(urldecode($row['request'])), 0, 55);
else
$linksa = stripslashes(urldecode($row['request']));

if ($confms['sea_flate'] == "yes")
$row['referer'] = @gzinflate(base64_decode($row['referer']));

$links = "<a href=\"{$engine[$row['host']]['4']}\" target=_blank><img src=\"engine/skins/referer/{$engine[$row['host']]['5']}\" align=\"absmiddle\" border=\"0\"></a> {$engine[$row['host']]['3']}: <a href=\"{$row['referer']}\" target=_blank><font color=#000>{$linksa}</font></a>";
} else {
if (strlen(urldecode($row['referer'])) > 55) $linksa = substr (urldecode($row['referer']), 0, 55)."...";
else
$linksa = urldecode($row['referer']);
$links = "<a href=\"{$row['referer']}\" target=\"_blank\"><font color=#000>{$linksa}</font></a>";
}

$entries .= <<<HTML
<tr>
    <td style="padding: 2px;"><div id="refer-{$row['id']}">{$links}<br /><span class="navigation" id="main_box{$row['id']}">{$siteurls}</span></div></td>
    <td width="150" align="center">{$datetime}</td>
<td width="110" align="center">{$ip}</td>
    <td width="85" align="center">{$row['hits']}</td>
    <td width="60" align="center"><a onClick="return dropdownmenu(this, event, MenuBuild('{$row['id']}', '{$row['host']}'), '150px')" href="#"><img src="engine/skins/images/browser_action.gif" border="0"></a></td>
  </tr>
<tr><td background=engine/skins/images/mline.gif height=1 colspan=6></td></tr>
HTML;

$entries_showed ++;
if($i >= $sea_per_page + $start_from){ break; }
}
$query_count = "SELECT COUNT(*) as count from " . PREFIX . "_referer";
$result_count = $db->super_query($query_count);
$all_count = $result_count['count'];
    opentable();

tableheader("{$langms['sea_typ']}");

 echo "<table width=100% border=0>";
 if (!$entries) {
  echo "<tr><td align=center height=30 colspan=5 align=center class=navigation>- {$langms['sea_m_no']} -</td></tr>";
 } else {
echo <<<HTML
   <tr>
    <td style="padding: 3px;">{$langms['sea_all_trans']}:</td>
    <td>
      <div align="center">{$langms['sea_all_time']}</div>
    </td>
<td>
      <div align="center">{$langms['sea_all_ip']}</div>
    </td>
    <td>
      <div align="center">{$langms['sea_all_tran']}</div>
    </td>
    <td align="right" style="padding: 3px;">{$langms['sea_action']}</td>
  </tr>
<tr><td colspan=5><div class="hr_line"></div></td></tr>
HTML;
  echo $entries;
 }
 echo "</table>";

echo <<<HTML
<div class="hr_line"></div><table width="100%">
  <tr>
HTML;

$npp_nav ="";

if($start_from > 0)
{
        $previous = $start_from - $sea_per_page;
        $npp_nav .= "<a href=\"$PHP_SELF?mod=referer&start_from=$previous&sea_per_page=$sea_per_page\">&lt;&lt; {$langms['sea_page_back']}</a>";
        //$tmp = 1;
}
if($all_count > $sea_per_page){
$npp_nav .= "<font class=navigation> [ </font>";
$enpages_count = @ceil($all_count/$sea_per_page);
$enpages_start_from = 0;
$enpages = "";
for($j=1;$j<=$enpages_count;$j++){
if($enpages_start_from != $start_from){ $enpages .= "<a class=maintitle href=\"$PHP_SELF?mod=referer&start_from=$enpages_start_from&sea_per_page=$sea_per_page\">$j</a> "; }
                else{ $enpages .= "<span class=navigation> $j </span>"; }
        $enpages_start_from += $sea_per_page;
        }
        $npp_nav .= $enpages;
        $npp_nav .= "<font class=navigation> ] </font>";
        }

if($all_count > $i)
{
        $how_next = $all_count - $i;
        if($how_next > $sea_per_page){ $how_next = $sea_per_page; }
        $npp_nav .= "<a href=\"$PHP_SELF?mod=referer&start_from=$i&sea_per_page=$sea_per_page\">{$langms['sea_page_here']} $how_next &gt;&gt;</a>";
}

if($entries_showed != 0){
echo <<<HTML
<td>{$npp_nav}</td>
HTML;
}
echo<<<HTML
  <td align="right"><table border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td style="padding:4px" class=navigation>{$langms['sea_you_action']}:</td>
      <td style="padding:4px">
HTML;
seamenutrans();
echo <<<HTML
</td>
<td style="padding:4px"><input type=submit class=edit value=" {$langms['sea_execute']} "></form></td>
    </tr>
  </table>
  </td>
  </tr>
</table>
HTML;
    closetable();
    Opentable();
    tableheader("{$langms['sea_shown']}: <strong>{$entries_showed}</strong> {$langms['sea_all_transit']}: <strong>{$all_count}</strong>");

$queryday = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_referer WHERE FROM_UNIXTIME(date) > NOW() - INTERVAL 1 DAY;");
$queryhour = $db->super_query("SELECT COUNT(*) as count FROM " . PREFIX . "_referer WHERE FROM_UNIXTIME(date) > NOW() - INTERVAL 1 HOUR;");

echo <<<HTML
<div style="padding: 5px;">
  <table width="100%" border="0" cellspacing="0" cellpadding="3">
  <tr>
    <td rowspan="3">
      <form
method=post>
        <input type=hidden name="mod" value="referer">
        <input type=hidden name="action" value="all">
	<input type=hidden name="search" value="search">
        <input class="edit" name="host" type="text" size="35">
        <input class="edit" type="submit" value=" {$langms['sea_search']} "

name="submit">
      </form>
    </td>
  </tr>
  <tr></tr>
  <tr>
    <td class=navigation>
      <div align="right" style="padding-right: 10px;">{$langms['sea_st_hour']} {$queryhour['count']}<br />
{$langms['sea_st_day']} {$queryday['count']}</div>
    </td>
  </tr>
</table>
</div>
</div>
HTML;
    closetable();
    echofooter();
};
?>
