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
 Файл: pm_alert.php
-----------------------------------------------------
 Назначение: Уведомление о персональном сообщении
=====================================================
*/
if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}
$row = $db->super_query("SELECT subj, text, user_from FROM " . USERPREFIX . "_pm WHERE user = '$member_id[user_id]' AND folder = 'inbox' ORDER BY pm_read ASC, date DESC LIMIT 0,1");

$lang['pm_alert'] = str_replace ("{user}"  , $member_id['name'], str_replace ("{num}"  , intval($member_id['pm_unread']), $lang['pm_alert']));
$row['subj'] = substr(stripslashes($row['subj']),0,45)." ...";
$row['text'] = str_replace ("<br />", " ", $row['text']);
$row['text'] = substr(strip_tags (stripslashes($row['text']) ),0,340)." ...";


$pm_alert = <<<HTML
<div id="newpm" class="highslide-html-content" style="width: 400px;height: 270px;">
<div id="newpmheader">
<div style='float:right'><a href='#' onclick="return hs.close(this)">[X]</a></div>
<div title="{$lang['pm_mtitle']}" class="highslide-move">{$lang['pm_atitle']}</div></div>
<div style="padding: 0 10px 0px 10px"><br />{$lang['pm_alert']}
<br /><br />
{$lang['pm_asub']} <b>{$row['subj']}</b><br />
{$lang['pm_from']} <b>{$row['user_from']}</b></div>
<div class="highslide-body" style="padding: 10px 10px 0px 10px">
<i>{$row['text']}</i>
	</div>
    <div class="highslide-footer">
		<div style="width:70%;float:left;padding-left:10px;"><a href="{$PHP_SELF}?do=pm">{$lang['pm_aread']}</a> · <a href='#' onclick="return hs.close(this)">{$lang['pm_close']}</a></div>
        <div>
            <span class="highslide-resize">
                <span></span>
            </span>
        </div>
    </div>
</div>
<script type="text/javascript">    
    hs.outlineWhileAnimating = true;
    hs.align = 'center';
	hs.htmlExpand(document.getElementById('newpm'), { contentId: 'newpm', transitions: ['fade'] } );
</script>
HTML;
?>