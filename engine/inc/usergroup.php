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
 Файл: usergroup.php
-----------------------------------------------------
 Назначение: Настройка групп пользователей
=====================================================
*/
if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}
if( $member_id['user_group'] != 1 ) {
	msg( "error", $lang['addnews_denied'], $lang['db_denied'] );
}

if( $action == "del" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	$id = intval( $_REQUEST['id'] );
	$grouplevel = intval( $_REQUEST['grouplevel'] );
	
	if( $id < 6 ) msg( "error", $lang['addnews_error'], $lang['group_notdel'], "$PHP_SELF?mod=usergroup" );
	
	$row = $db->super_query( "SELECT count(*) as count FROM " . USERPREFIX . "_users WHERE user_group='$id'" );
	
	if( ! $row['count'] ) {
		$db->query( "DELETE FROM " . USERPREFIX . "_usergroups WHERE id = '$id'" );
		@unlink( ENGINE_DIR . '/cache/system/usergroup.php' );
		clear_cache();
		msg( "info", $lang['all_info'], $lang['group_del'], "$PHP_SELF?mod=usergroup" );
	} else {
		if( $grouplevel and $grouplevel != $id ) {
			$db->query( "UPDATE " . USERPREFIX . "_users set user_group='$grouplevel' WHERE user_group='$id'" );
			$db->query( "DELETE FROM " . USERPREFIX . "_usergroups WHERE id = '$id'" );
			@unlink( ENGINE_DIR . '/cache/system/usergroup.php' );
			clear_cache();
			msg( "info", $lang['all_info'], $lang['group_del'], "$PHP_SELF?mod=usergroup" );
		} else
			msg( "info", $lang['all_info'], "<form action=\"\" method=\"post\">{$lang['group_move']} <select name=\"grouplevel\">" . get_groups( 4 ) . "</select> <input class=\"edit\" type=\"submit\" value=\"{$lang['b_start']}\"></form>", "$PHP_SELF?mod=usergroup" );
	}

} elseif( $action == "doadd" or $action == "doedit" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	if( ! count( $_REQUEST['allow_cats'] ) ) $_REQUEST['allow_cats'][] = "all";
	if( ! count( $_REQUEST['cat_add'] ) ) $_REQUEST['cat_add'][] = "all";
	
	$group_name = $db->safesql( strip_tags( $_REQUEST['group_name'] ) );
	$group_icon = $db->safesql( strip_tags( $_REQUEST['group_icon'] ) );
	$allow_cats = $db->safesql( implode( ',', $_REQUEST['allow_cats'] ) );
	$cat_add = $db->safesql( implode( ',', $_REQUEST['cat_add'] ) );
	
	$allow_admin = intval( $_REQUEST['allow_admin'] );
	$allow_offline = intval( $_REQUEST['allow_offline'] );
	$allow_main = intval( $_REQUEST['allow_main'] );
	$allow_adds = intval( $_REQUEST['allow_adds'] );
	$moderation = intval( $_REQUEST['moderation'] );
	$allow_edit = intval( $_REQUEST['allow_edit'] );
	$allow_all_edit = intval( $_REQUEST['allow_all_edit'] );
	$allow_addc = intval( $_REQUEST['allow_addc'] );
	$allow_editc = intval( $_REQUEST['allow_editc'] );
	$allow_delc = intval( $_REQUEST['allow_delc'] );
	$edit_allc = intval( $_REQUEST['edit_allc'] );
	$del_allc = intval( $_REQUEST['del_allc'] );
	$allow_hide = intval( $_REQUEST['allow_hide'] );
	$allow_pm = intval( $_REQUEST['allow_pm'] );
	$allow_files = intval( $_REQUEST['allow_files'] );
	$allow_feed = intval( $_REQUEST['allow_feed'] );
	$allow_search = intval( $_REQUEST['allow_search'] );
	$allow_rating = intval( $_REQUEST['allow_rating'] );
	$max_pm = intval( $_REQUEST['max_pm'] );
	$max_foto = intval( $_REQUEST['max_foto'] );
	$allow_short = intval( $_REQUEST['allow_short'] );
	$time_limit = intval( $_REQUEST['time_limit'] );
	$rid = intval( $_REQUEST['rid'] );
	$allow_fixed = intval( $_REQUEST['allow_fixed'] );
	$allow_poll = intval( $_REQUEST['allow_poll'] );
	$captcha = intval( $_REQUEST['captcha'] );
	$allow_modc = intval( $_REQUEST['allow_modc'] );
	$max_signature = intval( $_REQUEST['max_signature'] );
	$max_info = intval( $_REQUEST['max_info'] );
	
	$allow_image_upload = intval( $_REQUEST['allow_image_upload'] );
	$allow_file_upload = intval( $_REQUEST['allow_file_upload'] );
	$allow_signature = intval( $_REQUEST['allow_signature'] );
	$allow_url = intval( $_REQUEST['allow_url'] );
	$allow_image = intval( $_REQUEST['allow_image'] );
	$news_sec_code = intval( $_REQUEST['news_sec_code'] );
	
	if( $group_name == "" ) msg( "error", $lang['addnews_error'], $lang['group_err1'], "$PHP_SELF?mod=usergroup&action=add" );
	
	@unlink( ENGINE_DIR . '/cache/system/usergroup.php' );
	
	if( $action == "doadd" ) {
		$db->query( "INSERT INTO " . USERPREFIX . "_usergroups (group_name, allow_cats, allow_adds, cat_add, allow_admin, allow_addc, allow_editc, allow_delc, edit_allc, del_allc, moderation, allow_all_edit, allow_edit, allow_pm, max_pm, max_foto, allow_files, allow_hide, allow_short, time_limit, rid, allow_fixed, allow_feed, allow_search, allow_poll, allow_main, captcha, icon, allow_modc, allow_rating, allow_offline, allow_image_upload, allow_file_upload, allow_signature, allow_url, news_sec_code, allow_image, max_signature, max_info) values ('$group_name', '$allow_cats', '$allow_adds', '$cat_add', '$allow_admin', '$allow_addc', '$allow_editc', '$allow_delc', '$edit_allc', '$del_allc', '$moderation', '$allow_all_edit', '$allow_edit', '$allow_pm', '$max_pm', '$max_foto', '$allow_files', '$allow_hide', '$allow_short', '$time_limit', '$rid', '$allow_fixed', '$allow_feed', '$allow_search', '$allow_poll', '$allow_main', '$captcha', '$group_icon', '$allow_modc', '$allow_rating', '$allow_offline', '$allow_image_upload', '$allow_file_upload', '$allow_signature', '$allow_url', '$news_sec_code', '$allow_image', '$max_signature', '$max_info')" );
		msg( "info", $lang['all_info'], $lang['group_ok1'], "$PHP_SELF?mod=usergroup" );
	} else {
		$id = intval( $_REQUEST['id'] );
		$db->query( "UPDATE " . USERPREFIX . "_usergroups set group_name='$group_name', allow_cats='$allow_cats', allow_adds='$allow_adds', cat_add='$cat_add', allow_admin='$allow_admin', allow_addc='$allow_addc', allow_editc='$allow_editc', allow_delc='$allow_delc', edit_allc='$edit_allc', del_allc='$del_allc', moderation='$moderation', allow_all_edit='$allow_all_edit', allow_edit='$allow_edit', allow_pm='$allow_pm', max_pm='$max_pm', max_foto='$max_foto', allow_files='$allow_files', allow_hide='$allow_hide', allow_short='$allow_short', time_limit='$time_limit', rid='$rid', allow_fixed='$allow_fixed', allow_feed='$allow_feed', allow_search='$allow_search', allow_poll='$allow_poll', allow_main='$allow_main', captcha='$captcha', icon='$group_icon', allow_modc='$allow_modc', allow_rating='$allow_rating', allow_offline='$allow_offline', allow_image_upload='$allow_image_upload', allow_file_upload='$allow_file_upload', allow_signature='$allow_signature', allow_url='$allow_url', news_sec_code='$news_sec_code', allow_image='$allow_image', max_signature='$max_signature', max_info='$max_info' WHERE id='{$id}'" );
		msg( "info", $lang['all_info'], $lang['group_ok2'], "$PHP_SELF?mod=usergroup" );
	}
	clear_cache();
} elseif( $action == "add" or $action == "edit" ) {
	echoheader( "", "" );
	
	if( ! $config['allow_cmod'] ) $warning = "<br /><font color=\"red\">" . $lang['modul_offline'] . "</font>";
	else $warning = "";
	
	if( $action == "add" ) {
		
		$group_name_value = "";
		$group_icon_value = "";
		$allow_admin_no = "checked";
		$allow_offline_no = "checked";
		$allow_adds_yes = "checked";
		$allow_short_yes = "checked";
		$moderation_no = "checked";
		$allow_edit_no = "checked";
		$allow_all_edit_no = "checked";
		$allow_image_upload_yes = "checked";
		$allow_file_upload_yes = "checked";
		$allow_addc_yes = "checked";
		$allow_editc_yes = "checked";
		$allow_delc_yes = "checked";
		$edit_allc_no = "checked";
		$del_allc_no = "checked";
		$allow_hide_yes = "checked";
		$allow_pm_yes = "checked";
		$allow_files_yes = "checked";
		$allow_feed_yes = "checked";
		$allow_search_yes = "checked";
		$allow_rating_yes = "checked";
		$time_limit_no = "checked";
		$allow_fixed_no = "checked";
		$allow_poll_yes = "checked";
		$allow_main_yes = "checked";
		$allow_captcha_no = "checked";
		$allow_modc_no = "checked";
		$allow_signature_yes = "checked";
		$allow_url_yes = "checked";
		$allow_image_no = "checked";
		$news_sec_code_yes = "checked";
		
		$max_pm_value = "20";
		$max_foto_value = "100";
		$max_signature_value = "500";
		$max_info_value = "1000";
		$submit_value = $lang['group_new'];
		$form_title = $lang['group_new1'];
		$form_action = "$PHP_SELF?mod=usergroup&amp;action=doadd";
		$group_list = get_groups( 4 );
		
		$cat_add_value = "selected";
		$allow_cats_value = "selected";
		$categories_list = CategoryNewsSelection( 0, 0, false );
		$cat_add_list = CategoryNewsSelection( 0, 0, false );
	
	} else {
		
		$id = intval( $_REQUEST['id'] );
		$group_name_value = htmlspecialchars( stripslashes( $user_group[$id]['group_name'] ) );
		$group_icon_value = htmlspecialchars( stripslashes( $user_group[$id]['icon'] ) );
		
		if( $user_group[$id]['allow_offline'] ) $allow_offline_yes = "checked";	else $allow_offline_no = "checked";
		if( $user_group[$id]['allow_admin'] ) $allow_admin_yes = "checked";	else $allow_admin_no = "checked";
		if( $user_group[$id]['allow_adds'] ) $allow_adds_yes = "checked"; else $allow_adds_no = "checked";
		if( $user_group[$id]['moderation'] ) $moderation_yes = "checked"; else $moderation_no = "checked";
		if( $user_group[$id]['allow_edit'] ) $allow_edit_yes = "checked"; else $allow_edit_no = "checked";
		if( $user_group[$id]['allow_all_edit'] ) $allow_all_edit_yes = "checked"; else $allow_all_edit_no = "checked";
		if( $user_group[$id]['allow_addc'] ) $allow_addc_yes = "checked"; else $allow_addc_no = "checked";
		if( $user_group[$id]['allow_editc'] ) $allow_editc_yes = "checked"; else $allow_editc_no = "checked";
		if( $user_group[$id]['allow_delc'] ) $allow_delc_yes = "checked"; else $allow_delc_no = "checked";
		if( $user_group[$id]['edit_allc'] ) $edit_allc_yes = "checked"; else $edit_allc_no = "checked";
		if( $user_group[$id]['del_allc'] ) $del_allc_yes = "checked"; else $del_allc_no = "checked";
		if( $user_group[$id]['allow_hide'] ) $allow_hide_yes = "checked"; else $allow_hide_no = "checked";
		if( $user_group[$id]['allow_pm'] ) $allow_pm_yes = "checked"; else $allow_pm_no = "checked";
		if( $user_group[$id]['allow_files'] ) $allow_files_yes = "checked"; else $allow_files_no = "checked";
		if( $user_group[$id]['allow_feed'] ) $allow_feed_yes = "checked"; else $allow_feed_no = "checked";
		if( $user_group[$id]['allow_search'] ) $allow_search_yes = "checked"; else $allow_search_no = "checked";
		if( $user_group[$id]['allow_rating'] ) $allow_rating_yes = "checked"; else $allow_rating_no = "checked";
		if( $user_group[$id]['allow_short'] ) $allow_short_yes = "checked"; else $allow_short_no = "checked";
		if( $user_group[$id]['time_limit'] ) $time_limit_yes = "checked"; else $time_limit_no = "checked";
		if( $user_group[$id]['allow_fixed'] ) $allow_fixed_yes = "checked"; else $allow_fixed_no = "checked";
		if( $user_group[$id]['allow_poll'] ) $allow_poll_yes = "checked"; else $allow_poll_no = "checked";
		if( $user_group[$id]['allow_main'] ) $allow_main_yes = "checked"; else $allow_main_no = "checked";
		if( $user_group[$id]['captcha'] ) $allow_captcha_yes = "checked"; else $allow_captcha_no = "checked";
		if( $user_group[$id]['allow_modc'] ) $allow_modc_yes = "checked"; else $allow_modc_no = "checked";
		if( $user_group[$id]['allow_image_upload'] ) $allow_image_upload_yes = "checked"; else $allow_image_upload_no = "checked";
		if( $user_group[$id]['allow_file_upload'] ) $allow_file_upload_yes = "checked"; else $allow_file_upload_no = "checked";
		if( $user_group[$id]['allow_signature'] ) $allow_signature_yes = "checked"; else $allow_signature_no = "checked";
		if( $user_group[$id]['allow_url'] ) $allow_url_yes = "checked"; else $allow_url_no = "checked";
		if( $user_group[$id]['allow_image'] ) $allow_image_yes = "checked"; else $allow_image_no = "checked";
		if( $user_group[$id]['news_sec_code'] ) $news_sec_code_yes = "checked"; else $news_sec_code_no = "checked";
		
		if( $id == 1 ) $admingroup = "disabled";
		if( $id == 5 ) $gastgroup = "disabled";
		$group_list = get_groups( $user_group[$id]['rid'] );
		
		if( $user_group[$id]['allow_cats'] == "all" ) $allow_cats_value = "selected";
		$categories_list = CategoryNewsSelection( explode( ',', $user_group[$id]['allow_cats'] ), 0, false );
		
		if( $user_group[$id]['cat_add'] == "all" ) $cat_add_value = "selected";
		$cat_add_list = CategoryNewsSelection( explode( ',', $user_group[$id]['cat_add'] ), 0, false );
		
		$max_pm_value = $user_group[$id]['max_pm'];
		$max_foto_value = $user_group[$id]['max_foto'];
		$max_signature_value = $user_group[$id]['max_signature'];
		$max_info_value = $user_group[$id]['max_info'];
		$submit_value = $lang['group_edit'];
		$form_title = $lang['group_edit1'] . $group_name_value;
		$form_action = "$PHP_SELF?mod=usergroup&amp;action=doedit&amp;id=" . $id;
	
	}
	
	echo <<<HTML
<script type="text/javascript" src="engine/skins/tabs.js"></script>
<form action="{$form_action}" method="post">
<input type="hidden" name="user_hash" value="$dle_login_hash" />
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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$form_title}</div></td>
    </tr>
</table>
<div class="unterline"></div>

<div id="dle_tabView1">

<div class="dle_aTab" style="display:none;">
<table width="100%">
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_name']}</b><br /><span class="small">{$lang[hint_gtitle]}</span></td>
        <td width="480" style="padding-top:2px;padding-bottom:2px;"><input class="edit" type="text" size="25" name="group_name" value="{$group_name_value}"></td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_icon']}</b><br /><span class="small">{$lang['hint_gicon']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input class="edit" type="text" size="35" name="group_icon" value="{$group_icon_value}"></td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_ct']}</b></td>
        <td style="padding-top:2px;padding-bottom:2px;"><select name="allow_cats[]" class="cat_select" multiple >
<option value="all" {$allow_cats_value}>{$lang['edit_all']}</option>
{$categories_list}
</select></td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_alct']}</b><br /><span class="small">{$lang['hint_gadc']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><select name="cat_add[]" class="cat_select" multiple >
<option value="all" {$cat_add_value}>{$lang['edit_all']}</option>
{$cat_add_list}
</select></td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_shid']}</b><br /><span class="small">{$lang['hint_gasr']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_short" {$allow_short_yes} value="1"> {$lang['opt_sys_yes']} <input type="radio" name="allow_short" {$allow_short_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_offline']}</b><br /><span class="small">{$lang['hint_goffline']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_offline" {$allow_offline_yes} value="1" {$gastgroup}> {$lang['opt_sys_yes']} <input type="radio" name="allow_offline" {$allow_offline_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_hic']}</b><br /><span class="small">{$lang['hint_gvhide']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_hide" {$allow_hide_yes} value="1" > {$lang['opt_sys_yes']} <input type="radio" name="allow_hide" {$allow_hide_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
 	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
   <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_apm']}</b><br /><span class="small">{$lang['hint_gapm']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_pm" {$allow_pm_yes} value="1" {$gastgroup}> {$lang['opt_sys_yes']} <input type="radio" name="allow_pm" {$allow_pm_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_afil']}</b><br /><span class="small">{$lang['hint_gafile']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_files" {$allow_files_yes} value="1" > {$lang['opt_sys_yes']} <input type="radio" name="allow_files" {$allow_files_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
 	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['a_feed']}</b><br /><span class="small">{$lang['hint_gafeed']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_feed" {$allow_feed_yes} value="1" > {$lang['opt_sys_yes']} <input type="radio" name="allow_feed" {$allow_feed_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
 	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['a_search']}</b><br /><span class="small">{$lang['hint_gasearch']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_search" {$allow_search_yes} value="1" > {$lang['opt_sys_yes']} <input type="radio" name="allow_search" {$allow_search_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_tlim']}</b><br /><span class="small">{$lang['hint_glimit']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="time_limit" {$time_limit_yes} value="1" {$admingroup}{$gastgroup}> {$lang['opt_sys_yes']} <input type="radio" name="time_limit" {$time_limit_no} value="0"> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_rlim']}</b><br /><span class="small">{$lang['hint_grid']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><select name="rid">
           {$group_list}
            </select></td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_mpm']}</b><br /><span class="small">{$lang['hint_gmpm']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input class="edit" type="text" size="10" name="max_pm" value="{$max_pm_value}"></td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_mfot']}</b><br /><span class="small">{$lang['hint_gmphoto']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input class="edit" type="text" size="10" name="max_foto" value="{$max_foto_value}"></td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_max_info']}</b><br /><span class="small">{$lang['hint_max_info']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input class="edit" type="text" size="10" name="max_info" value="{$max_info_value}"></td>
    </tr>
</table>
</div>

<!--news area-->
<div class="dle_aTab" style="display:none;">
<table width="100%">
     <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_poll']}</b><br /><span class="small">{$lang['group_poll_hint']}</span></td>
        <td width="480" style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_poll" {$allow_poll_yes} value="1"> {$lang['opt_sys_yes']} <input type="radio" name="allow_poll" {$allow_poll_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['a_rating']}</b><br /><span class="small">{$lang['hint_garating']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_rating" {$allow_rating_yes} value="1" > {$lang['opt_sys_yes']} <input type="radio" name="allow_rating" {$allow_rating_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
     <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_adds']}</b><br /><span class="small">{$lang['hint_gaad']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_adds" {$allow_adds_yes} value="1" {$gastgroup}> {$lang['opt_sys_yes']} <input type="radio" name="allow_adds" {$allow_adds_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>

     <tr>
        <td style="padding:4px;" class="option"><b>{$lang['opt_sys_news_c']}</b><br /><span class="small">{$lang['hint_scode']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="news_sec_code" {$news_sec_code_yes} value="1" {$admingroup}> {$lang['opt_sys_yes']} <input type="radio" name="news_sec_code" {$news_sec_code_no} value="0" {$gastgroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>


    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_moder']}</b><br /><span class="small">{$lang['hint_gmod']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="moderation" {$moderation_yes} value="1" {$gastgroup}> {$lang['opt_sys_yes']} <input type="radio" name="moderation" {$moderation_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_main']}</b><br /><span class="small">{$lang['group_main_hint']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_main" {$allow_main_yes} value="1" {$gastgroup}> {$lang['opt_sys_yes']} <input type="radio" name="allow_main" {$allow_main_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_fixed']}</b><br /><span class="small">{$lang['hint_gfixed']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_fixed" {$allow_fixed_yes} value="1" {$gastgroup}> {$lang['opt_sys_yes']} <input type="radio" name="allow_fixed" {$allow_fixed_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_edit2']}</b><br /><span class="small">{$lang['hint_gned']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_edit" {$allow_edit_yes} value="1" {$gastgroup}> {$lang['opt_sys_yes']} <input type="radio" name="allow_edit" {$allow_edit_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_edit3']}</b><br /><span class="small">{$lang['hint_gnaed']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_all_edit" {$allow_all_edit_yes} value="1" {$gastgroup}> {$lang['opt_sys_yes']} <input type="radio" name="allow_all_edit" {$allow_all_edit_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['opt_sys_aiu']}</b><br /><span class="small">{$lang['opt_sys_aiud']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_image_upload" {$allow_image_upload_yes} value="1" {$gastgroup}> {$lang['opt_sys_yes']} <input type="radio" name="allow_image_upload" {$allow_image_upload_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['opt_sys_file']}</b><br /><span class="small">{$lang['opt_sys_filed']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_file_upload" {$allow_file_upload_yes} value="1" {$gastgroup}> {$lang['opt_sys_yes']} <input type="radio" name="allow_file_upload" {$allow_file_upload_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
</table>
</div>

<!--comments area-->
<div class="dle_aTab" style="display:none;">
<table width="100%">
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_addc']}</b><br /><span class="small">{$lang['hint_gaс']}</span></td>
        <td width="480" style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_addc" {$allow_addc_yes} value="1" > {$lang['opt_sys_yes']} <input type="radio" name="allow_addc" {$allow_addc_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_signature']}</b><br /><span class="small">{$lang['hint_signature']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_signature" {$allow_signature_yes} value="1" {$gastgroup}> {$lang['opt_sys_yes']} <input type="radio" name="allow_signature" {$allow_signature_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_max_signature']}</b><br /><span class="small">{$lang['hint_max_signature']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input class="edit" type="text" size="10" name="max_signature" value="{$max_signature_value}"></td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_url']}</b><br /><span class="small">{$lang['hint_group_url']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_url" {$allow_url_yes} value="1" > {$lang['opt_sys_yes']} <input type="radio" name="allow_url" {$allow_url_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>

    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_image']}</b><br /><span class="small">{$lang['hint_group_image']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_image" {$allow_image_yes} value="1"> {$lang['opt_sys_yes']} <input type="radio" name="allow_image" {$allow_image_no} value="0"> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>

    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_modc']}</b><br /><span class="small">{$lang['hint_modc']}</span>{$warning}</td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_modc" {$allow_modc_yes} value="1" {$admingroup}> {$lang['opt_sys_yes']} <input type="radio" name="allow_modc" {$allow_modc_no} value="0"> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_editc']}</b><br /><span class="small">{$lang['hint_geс']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_editc" {$allow_editc_yes} value="1" {$gastgroup}> {$lang['opt_sys_yes']} <input type="radio" name="allow_editc" {$allow_editc_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_delc']}</b><br /><span class="small">{$lang['hint_gdс']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_delc" {$allow_delc_yes} value="1" {$gastgroup}> {$lang['opt_sys_yes']} <input type="radio" name="allow_delc" {$allow_delc_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['opt_sys_code']}</b><br /><span class="small">{$lang['opt_sys_codeсd']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="captcha" {$allow_captcha_yes} value="1"> {$lang['opt_sys_yes']} <input type="radio" name="captcha" {$allow_captcha_no} value="0"> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>

    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_allc']}</b><br /><span class="small">{$lang['hint_gaeс']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="edit_allc" {$edit_allc_yes} value="1" {$gastgroup}> {$lang['opt_sys_yes']} <input type="radio" name="edit_allc" {$edit_allc_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_dllc']}</b><br /><span class="small">{$lang['hint_gadс']}</span></td>
        <td style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="del_allc" {$del_allc_yes} value="1" {$gastgroup}> {$lang['opt_sys_yes']} <input type="radio" name="del_allc" {$del_allc_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
</table>
</div>

<!--admin area-->
<div class="dle_aTab" style="display:none;">
<table width="100%">
    <tr>
        <td style="padding:4px;" class="option"><b>{$lang['group_aadm']}</b><br /><span class="small">{$lang['hint_gadmin']}</span></td>
        <td width="480" style="padding-top:2px;padding-bottom:2px;"><input type="radio" name="allow_admin" {$allow_admin_yes} value="1" {$gastgroup}> {$lang['opt_sys_yes']} <input type="radio" name="allow_admin" {$allow_admin_no} value="0" {$admingroup}> {$lang['opt_sys_no']}</td>
    </tr>
	<tr><td background="engine/skins/images/mline.gif" height=1 colspan=2></td></tr>
</table>
</div>

</div>

<table width="100%">
    <tr>
        <td style="padding:4px;"><input class="bbcodes" type="submit" value="$submit_value"></td>
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
</form>
<script type="text/javascript">
initTabs('dle_tabView1',Array('{$lang['tabs_gr_all']}', '{$lang['tabs_gr_news']}', '{$lang['tabs_gr_comments']}', '{$lang['tabs_gr_admin']}'),0, '100%');
</script>
HTML;
	
	echofooter();
} else {
	echoheader( "", "" );
	
	$db->query( "SELECT user_group, count(*) as count FROM " . USERPREFIX . "_users GROUP BY user_group" );
	while ( $row = $db->get_row() )
		$count_list[$row['user_group']] = $row['count'];
	$db->free();
	foreach ( $user_group as $group ) {
		$count = intval( $count_list[$group['id']] );
		$entries .= "
    <tr>
    <td height=22 class=\"list\">&nbsp;&nbsp;<b>{$group['id']}</b></td>
    <td class=\"list\">{$group['group_name']}</td>
    <td class=\"list\" align=\"center\">$count</td>
    <td class=\"list\" align=\"center\"><a onClick=\"return dropdownmenu(this, event, MenuBuild('" . $group['id'] . "'), '150px')\" href=\"#\"><img src=\"engine/skins/images/browser_action.gif\" border=\"0\"></a></td>
     </tr>
	<tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=4></td></tr>";
	}
	
	echo <<<HTML
<div style="padding-top:5px;padding-bottom:2px;">
<script type="text/javascript" src="engine/ajax/menu.js"></script>
<script language="javascript" type="text/javascript">
<!--
function MenuBuild( m_id ){

var menu=new Array()

menu[0]='<a href="?mod=usergroup&action=edit&id=' + m_id + '" >{$lang['group_sel1']}</a>';
if (m_id > 5) {
menu[1]='<a href="?mod=usergroup&action=del&user_hash={$dle_login_hash}&id=' + m_id + '" >{$lang['group_sel2']}</a>';
}
else {
menu[1]='<a href="#" onclick="return false;"><font color="black">{$lang['group_sel3']}</font></a>';
}

return menu;
}
//-->
</script>
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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['group_list']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td style="padding:2px;">
<table width="100%">
  <tr>
   <td width=50>&nbsp;&nbsp;ID</td>
   <td>{$lang['group_name']}</td>
   <td width=100 align="center">{$lang['group_sel4']}</td>
   <td width=70 align="center">&nbsp;</td>
  </tr>
	<tr><td colspan="4"><div class="hr_line"></div></td></tr>
	{$entries}
	<tr><td colspan="4"><div class="hr_line"></div></td></tr>
  <tr><td colspan="4"><a href="?mod=usergroup&action=add"><input onclick="document.location='?mod=usergroup&action=add'" type="button" class="buttons" value="{$lang['group_sel5']}"></a></td></tr>
</table>
	</td>
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
</form>
HTML;
	
	echofooter();
}
?>