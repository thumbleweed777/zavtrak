<?PHP

if (!$lang['admin_logo']) $lang['admin_logo'] = "engine/skins/images/nav.jpg";

$skin_header = <<<HTML
<html>
<head>
<title>Мой самый, самый сайтик )) - $lang[skin_title]</title>
<meta content="text/html; charset={$config['charset']}" http-equiv="content-type" />
<script type="text/javascript" src="engine/skins/default.js"></script>

<style type="text/css">
html,body{
height:100%;
margin:0px;
padding: 0px;
background: #F4F3EE;
}

form {
margin:0px;
padding: 0px;
}

table{
border:0px;
border-collapse:collapse;
}

table td{
padding:0px;
font-size: 11px;
font-family: verdana;
}

a:active,
a:visited,
a:link {
	color: #4b719e;
	text-decoration:none;
	}

a:hover {
	color: #4b719e;
	text-decoration: underline;
	}

.navigation {
	color: #999898;
	font-size: 11px;
	font-family: tahoma;
}

.option {
	color: #717171;
	font-size: 11px;
	font-family: tahoma;
}

.upload input {
	border:1px solid #9E9E9E;
	color: #000000;
	font-size: 11px;
	font-family: Verdana; 
}

.small {
	color: #999898;
}

.navigation a:active,
.navigation a:visited,
.navigation a:link {
	color: #999898;
	text-decoration:none;
	}

.navigation a:hover {
	color: #999898;
	text-decoration: underline;
	}

.list {
	font-size: 11px;
}

.list a:active,
.list a:visited,
.list a:link {
	color: #0B5E92;
	text-decoration:none;
	}

.list a:hover {
	color: #999898;
	text-decoration: underline;
	}

.quick {
	color: #999898;
	font-size: 11px;
	font-family: tahoma;
	padding: 5px;
}

.quick h3 {
	font-size: 18px;
	font-family: verdana;
	margin: 0px;
	padding-top: 5px;
}
.system {
	color: #999898;
	font-size: 11px;
	font-family: tahoma;
	padding-bottom: 10px;
	text-decoration:none;
}

.system h3 {
	font-size: 18px;
	font-family: verdana;
	margin: 0px;
	padding-top: 4px;
}
.system a:active,
.system a:visited,
.system a:link,
.system a:hover {
	color: #999898;
	text-decoration:none;
	}

.quick a:active,
.quick a:visited,
.quick a:link,
.quick a:hover {
	color: #999898;
	text-decoration:none;
	}

.unterline {
	background: url(engine/skins/images/line_bg.gif);
	width: 100%;
	height: 9px;
	font-size: 3px;
	font-family: tahoma;
	margin-bottom: 4px;
} 

.hr_line {
	background: url(engine/skins/images/line.gif);
	width: 100%;
	height: 7px;
	font-size: 3px;
	font-family: tahoma;
	margin-top: 4px;
	margin-bottom: 4px;
}

.edit {
	border:1px solid #9E9E9E;
	color: #000000;
	font-size: 11px;
	font-family: Verdana;
	background: #FFF; 
}

.bbcodes {
	background: #FFF;
	border: 1px solid #9E9E9E;
	color: #666666;
	font-family: Verdana, Tahoma, helvetica, sans-serif;
	padding: 2px;
	vertical-align: middle;
	font-size: 10px; 
	margin:2px;
	height: 21px;
}

.buttons {
	background: #FFF;
	border: 1px solid #9E9E9E;
	color: #666666;
	font-family: Verdana, Tahoma, helvetica, sans-serif;
	padding: 0px;
	vertical-align: absmiddle;
	font-size: 11px; 
	height: 21px;
}

select {
	color: #000000;
	font-size: 11px;
	font-family: Verdana; 
	border:1px solid #9E9E9E;
}

.cat_select {
	color: #000000;
	font-size: 11px;
	font-family: Verdana; 
	border:1px solid #9E9E9E;
	width:316px;
	height:73px;
}

textarea {
	border: #9E9E9E 1px solid;
	color: #000000;
	font-size: 11px;
	font-family: Verdana;
	margin-bottom: 2px;
	margin-right: 0px;
	padding: 0px;
}

.xfields textarea {
width:98%; height:100px;border: #9E9E9E 1px solid; font-size: 11px;font-family: Verdana;
}
.xfields input {
width:350px; height:18px;border: #9E9E9E 1px solid; font-size: 11px;font-family: Verdana;
}
.xfields select {
height:18px; font-size: 11px;font-family: Verdana;
}

.xfields {
height:30px; font-size: 11px;font-family: Verdana;
}
.xprofile textarea {
width:100%; height:90px; font-family:verdana; font-size:11px; border:1px solid #E0E0E0;
}
.xprofile input {
width:250px; height:18px; font-family:verdana; font-size:11px; border:1px solid #E0E0E0;
}
#dropmenudiv{
border:1px solid white;
border-bottom-width: 0;
font:normal 10px Verdana;
background-color: #6497CA;
line-height:20px;
margin:2px;
filter: alpha(opacity=95, enabled=1) progid:DXImageTransform.Microsoft.Shadow(color=#CACACA,direction=135,strength=3);
}

#dropmenudiv a{
display: block;
text-indent: 3px;
border: 1px solid white;
padding: 1px 0;
MARGIN: 1px;
color: #FFF;
text-decoration: none;
font-weight: bold;
}

#dropmenudiv a:hover{ /*hover background color*/
background-color: #FDD08B;
color: #000;
}

#hintbox{ /*CSS for pop up hint box */
position:absolute;
top: 0;
background-color: lightyellow;
width: 150px; /*Default width of hint.*/ 
padding: 3px;
border:1px solid #787878;
font:normal 11px Verdana;
line-height:18px;
z-index:100;
border-right: 2px solid #787878;
border-bottom: 2px solid #787878;
visibility: hidden;
}

.hintanchor{ 
padding-left: 8px;
}

.editor_button {
	float:left;
	cursor:pointer;
	padding-left: 2px;
	padding-right: 2px;
}
.editor_buttoncl {
	float:left;
	cursor:pointer;
	padding-left: 1px;
	padding-right: 1px;
	border-left: 1px solid #BBB;
	border-right: 1px solid #BBB;
}
.editbclose {
	float:right;
	cursor:pointer;
}
	.dle_tabPane{
		height:26px;	/* Height of tabs */
	}
	.dle_aTab{
		border:1px solid #CDCDCD;
		padding:5px;		
		
	}
	.dle_tabPane DIV{
		float:left;
		padding-left:3px;
		vertical-align:middle;
		background-repeat:no-repeat;
		background-position:bottom left;
		cursor:pointer;
		position:relative;
		bottom:-1px;
		margin-left:0px;
		margin-right:0px;
	}
	.dle_tabPane .tabActive{
		background-image:url('engine/skins/images/tl_active.gif');
		margin-left:0px;
		margin-right:0px;	
	}
	.dle_tabPane .tabInactive{
		background-image:url('engine/skins/images/tl_inactive.gif');
		margin-left:0px;
		margin-right:0px;
	}

	.dle_tabPane .inactiveTabOver{
		margin-left:0px;
		margin-right:0px;
	}
	.dle_tabPane span{
		font-family:tahoma;
		vertical-align:top;
		font-size:11px;
		line-height:26px;
		float:left;
	}
	.dle_tabPane .tabActive span{
		padding-bottom:0px;
		line-height:26px;
	}
	
	.dle_tabPane img{
		float:left;
	}

.news_navigation {
	color:#666; 
	border:0px;
	line-height: 25px;
}

.news_navigation a:link,
.news_navigation a:visited,
.news_navigation a:hover,
.news_navigation a:active {
	text-decoration:none;
	color: #636363;
	background:#F9F9F9;
	border:1px solid #E6E6E6;
	padding:3px 6px;
	font-size:11px
}

.news_navigation span {
	text-decoration:none;
	background:#fff;
	padding:3px 6px;
	border:1px solid #E6E6E6;
	color:#ccc;
	font-size:11px;
}
</style>
</head>
<body>
<table align="center" id="main_body" style="width:94%;">
    <tr>
        <td width="4" height="16"><img src="engine/skins/images/tb_left.gif" width="4" height="16" border="0" /></td>
		<td background="engine/skins/images/tb_top.gif"><img src="engine/skins/images/tb_top.gif" width="1" height="16" border="0" /></td>
		<td width="4"><img src="engine/skins/images/tb_right.gif" width="3" height="16" border="0" /></td>
    </tr>
	<tr>
        <td width="4" background="engine/skins/images/tb_lt.gif"><img src="engine/skins/images/tb_lt.gif" width="4" height="1" border="0" /></td>
		<td valign="top" style="padding-top:12px; padding-left:13px; padding-right:13px;" bgcolor="#FAFAFA">
		
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="29"><div class="navigation"><img align="absmiddle" src="engine/skins/images/p1.gif" width="25" height="8" border="0">{$lang['skin_name']} {user} ({group})</div></td>
        <td bgcolor="#EFEFEF" height="29" align="right" style="padding-right:10px;"><div class="navigation"><img align="absmiddle" src="engine/skins/images/p1.gif" width="25" height="8" border="0"><a href="$PHP_SELF?mod=main" class=navigation>$lang[skin_main]</a><img align="absmiddle" src="engine/skins/images/p1.gif" width="25" height="8" border="0"><a href="{$config['http_home_url']}" target="_blank" class=navigation>$lang[skin_view]</a><img align="absmiddle" src="engine/skins/images/p1.gif" width="25" height="8" border="0"><a href="$PHP_SELF?action=logout" class=navigation>$lang[skin_logout]</a></div></td>
    </tr>
</table>

<div style="padding-top:5px;">
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
        <td  width="267"><img src="{$lang['admin_logo']}" width="267" height="99" border="0" usemap="#ImageNav"></td>
        <td background="engine/skins/images/logo_bg.gif">&nbsp;<map name="ImageNav">
<area shape="rect" coords="19, 33, 251, 49" href="$PHP_SELF?mod=addnews&action=addnews">
<area shape="rect" coords="21, 52, 251, 67" href="$PHP_SELF?mod=editnews&action=list">
<area shape="rect" coords="21, 69, 250, 85" href="$PHP_SELF?mod=options&action=options">
</map></td>
        <td width="301"><img src="engine/skins/images/logos.jpg" width="301" height="99" border="0"></td>
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
<!--MAIN area-->
HTML;

$skin_footer = <<<HTML
	 <!--MAIN area-->
<div style="padding-top:5px; padding-bottom:10px;">
<table width="100%">
    <tr>
        <td bgcolor="#EFEFEF" height="40" align="center" style="padding-right:10px;"><div class="navigation"><a href="/" target="_blank">Мой самый, самый сайтик ))</div></td>
    </tr>
</table></div>		
		</td>
		<td width="4" background="engine/skins/images/tb_rt.gif"><img src="engine/skins/images/tb_rt.gif" width="4" height="1" border="0" /></td>
    </tr>
	<tr>
        <td height="16" background="engine/skins/images/tb_lb.gif"></td>
		<td background="engine/skins/images/tb_tb.gif"></td>
		<td background="engine/skins/images/tb_rb.gif"></td>
    </tr>
</table>
<script language="javascript" type="text/javascript">
<!--
function getClientWidth()
{
  return document.compatMode=='CSS1Compat' && !window.opera?document.documentElement.clientWidth:document.body.clientWidth;
}
var main_body_size = getClientWidth();

if (main_body_size > 1300) document.getElementById('main_body').style.width = "1200px";

//-->
</script>
</body>

</html>
HTML;

?>