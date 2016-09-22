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
 Файл: templates.php
-----------------------------------------------------
 Назначение: Управление шаблонами
=====================================================
*/
if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

if( $member_id['user_group'] != 1 ) {
	msg( "error", $lang['opt_denied'], $lang['opt_denied'] );
}

if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
	
	header( "Location: $PHP_SELF?mod=templates&user_hash=" . $dle_login_hash );

}

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
      поиск всех шаблонов
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
$templates_list = array ();
if( ! $handle = opendir( ROOT_DIR . "/templates" ) ) {
	die( $lang['opt_errfo'] );
}
while ( false !== ($file = readdir( $handle )) ) {
	if( is_dir( ROOT_DIR . "/templates/$file" ) and ($file != "." and $file != "..") ) {
		$templates_list[] = $file;
	}
}
closedir( $handle );
sort($templates_list);


$language_list = array ();
if( ! $handle = opendir( ROOT_DIR . "/language" ) ) {
	die( $lang['opt_errfo'] );
}
while ( false !== ($file = readdir( $handle )) ) {
	if( is_dir( ROOT_DIR . "/language/$file" ) and ($file != "." and $file != "..") ) {
		$language_list[] = $file;
	}
}
closedir( $handle );

if( $_REQUEST['subaction'] == "language" ) {
	
	$allow_save = false;
	
	$_REQUEST['do_template'] = trim( totranslit($_REQUEST['do_template'], false, false) );
    $_REQUEST['do_language'] = trim( totranslit($_REQUEST['do_language'], false, false) );
	
	if( $_REQUEST['do_template'] != "" and $_REQUEST['do_language'] != "" ) {
		$config["lang_" . $_REQUEST['do_template']] = $_REQUEST['do_language'];
		$allow_save = true;
	
	} elseif( $config["lang_" . $_REQUEST['do_template']] and $_REQUEST['do_language'] == "" ) {
		unset( $config["lang_" . $_REQUEST['do_template']] );
		$allow_save = true;
	}
	
	if( $allow_save ) {
		
		if( $auto_detect_config ) $config['http_home_url'] = "";
		
		$handler = fopen( ENGINE_DIR . '/data/config.php', "w" );
		fwrite( $handler, "<?PHP \n\n//System Configurations\n\n\$config = array (\n\n" );
		foreach ( $config as $name => $value ) {
			fwrite( $handler, "'{$name}' => \"{$value}\",\n\n" );
		}
		fwrite( $handler, ");\n\n?>" );
		fclose( $handler );
	
	}

}

if( $subaction == "new" ) {
	echoheader( "options", $lang['opt_newtemp'] );
	
	echo "<form method=post action=\"$PHP_SELF\"><table width=100%><tr><td height=\"150\"><center>$lang[opt_newtemp_1] <select name=base_template>";
	foreach ( $templates_list as $single_template ) {
		echo "<option value=\"$single_template\">$single_template</option>";
	}
	echo '</select> ' . $lang[opt_msgnew] . ' <input class=edit type=text name=template_name> <br /><br /><input type="submit" value="' . $lang['b_start'] . '" class="buttons">
        <input type=hidden name=mod value=templates>
        <input type=hidden name=action value=templates>
        <input type=hidden name=subaction value=donew>
        <input type=hidden name=user_hash value="' . $dle_login_hash . '">
        </td></tr></table></form>';
	echofooter();
	exit();
}

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
      Создания нового шаблона
 ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
if( $subaction == "donew" ) {
	
	function open_dir($dir, $newdir) { //The function that will copy the files
		if( file_exists( $dir ) && file_exists( $newdir ) ) {
			$open_dir = opendir( $dir );
			while ( false !== ($file = readdir( $open_dir )) ) {
				if( $file != "." && $file != ".." ) {
					if( @filetype( $dir . "/" . $file . "/" ) == "dir" ) {
						if( ! file_exists( $newdir . "/" . $file . "/" ) ) {
							mkdir( $newdir . "/" . $file . "/" );
							@chmod( $newdir . "/" . $file, 0777 );
							open_dir( $dir . "/" . $file . "/", $newdir . "/" . $file . "/" );
						}
					} else {
						copy( $dir . "/" . $file . "/", $newdir . "/" . $file );
						@chmod( $newdir . "/" . $file, 0666 );
					}
				}
			}
		}
	}
	
# php 5.2
# 	if( ! eregi( "^[a-z0-9_-]+$", $template_name ) ) {

	if( ! preg_match( "/^[a-z0-9_-]+$/i", $template_name ) ) {
		msg( "error", $lang['opt_error'], $lang['opt_error_1'], "$PHP_SELF?mod=templates&subaction=new&user_hash={$dle_login_hash}" );
	}
	
	$result = @mkdir( ROOT_DIR . "/templates/" . $template_name, 0777 );
	@chmod( ROOT_DIR . "/templates/" . $template_name, 0777 );
	
	if( ! $result ) msg( "error", $lang['opt_error'], $lang['opt_cr_err'], "$PHP_SELF?mod=templates&subaction=new&user_hash={$dle_login_hash}" );
	else open_dir( ROOT_DIR . "/templates/" . $base_template, ROOT_DIR . "/templates/" . $template_name );
	
	msg( "info", $lang['opt_info'], $lang['opt_info_1'], "$PHP_SELF?mod=templates&user_hash={$dle_login_hash}" );
}
/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
      Подготовка к удалению
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
if( $subaction == "delete" ) {
	if( strtolower( $do_template ) == "default" OR strtolower( $do_template ) == "smartphone" OR strtolower( $do_template ) == '' ) {
		msg( "Error", $lang['opt_error'], $lang['opt_error_4'], "$PHP_SELF?mod=templates&user_hash={$dle_login_hash}" );
	}
	$msg = "<form method=post action=\"$PHP_SELF\">$lang[opt_info_2] <b>$do_template</b>?<br><br>
        <input class=bbcodes type=submit value=\" $lang[opt_yes] \"> &nbsp;<input class=bbcodes onClick=\"document.location='$PHP_SELF?mod=templates';\" type=button value=\"$lang[opt_no]\">
        <input type=hidden name=mod value=templates>
        <input type=hidden name=subaction value=dodelete>
        <input type=hidden name=do_template value=\"$do_template\">
        <input type=hidden name=user_hash value=\"$dle_login_hash\">
        </form>";
	
	msg( "info", $lang['opt_info_3'], $msg );
}
/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
      Удаление шаблона
 ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
if( $subaction == "dodelete" ) {
	if( strtolower( $do_template ) == "default" OR strtolower( $do_template ) == "smartphone" ) {
		msg( "Error", $lang['opt_error'], $lang['opt_error_4'], "$PHP_SELF?mod=templates&user_hash={$dle_login_hash}" );
	}
	
#php 5.2
# 	if( ! eregi( "^[a-z0-9_-]+$", $do_template ) ) {


	if( ! preg_match( "/^[a-z0-9_-]+$/i", $do_template ) ) {
		msg( "error", $lang['opt_error'], $lang['opt_error_1'], "$PHP_SELF?mod=templates&user_hash={$dle_login_hash}" );
	}
	
	listdir( ROOT_DIR . "/templates/" . $do_template );
	
	msg( "info", $lang['opt_info_3'], $lang['opt_info_4'], "$PHP_SELF?mod=templates&user_hash={$dle_login_hash}" );
}

// ********************************************************************************
// Запись изменений
// ********************************************************************************
if( $action == "dosavetemplates" ) {
	extract( $_POST, EXTR_SKIP );
	
	if( $do_template == "" or ! $do_template ) {
		$do_template = "Default";
	}
	
	function save_template($tpl_name, $text) {
		global $do_template;
		
		$handle = fopen( ROOT_DIR . '/templates/' . $do_template . DIRECTORY_SEPARATOR . $tpl_name, "w" );
		fwrite( $handle, $text );
		fclose( $handle );
	
	}
	
	$templates_names = array ("edit_main" => "main.tpl", "edit_vote" => "vote.tpl", "edit_active" => "shortstory.tpl", "edit_comment" => "comments.tpl", "edit_form" => "addcomments.tpl", "edit_full" => "fullstory.tpl", "edit_prev_next" => "navigation.tpl", "edit_user" => "userinfo.tpl", "edit_addnews" => "addnews.tpl", "edit_search" => "search.tpl", "edit_searchresult" => "searchresult.tpl", "edit_stats" => "stats.tpl", "edit_error" => "info.tpl", "edit_reg" => "registration.tpl", "edit_pass" => "lostpassword.tpl", "edit_mail" => "feedback.tpl", "edit_pm" => "pm.tpl", "edit_offline" => "offline.tpl", "edit_static" => "static.tpl", "edit_poll" => "poll.tpl", "edit_speedbar" => "speedbar.tpl", "edit_tagscloud" => "tagscloud.tpl" );
	
	foreach ( $templates_names as $template => $template_file ) {
		save_template( $template_file, stripslashes( $$template ) );
	}
	
	clear_cache();
	msg( "info", $lang['opt_editok'], $lang['opt_editok_1'], "$PHP_SELF?mod=templates&user_hash={$dle_login_hash}&do_template=$do_template" );
}

/* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
      Редактирование шаблона
 ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ */
$show_delete_link = '';

if( $do_template == '' or ! $do_template ) {
	$do_template = $config['skin'];
} elseif( $do_template != $config['skin'] AND $do_template != "smartphone" ) {
	$show_delete_link = "[<a class=\"maintitle\" href=\"$PHP_SELF?mod=templates&subaction=delete&user_hash={$dle_login_hash}&do_template=$do_template\">$lang[opt_dellink]</a>]";
}

function load_template($tpl_name) {
	global $do_template;
	
	$text = @file_get_contents( ROOT_DIR . '/templates/' . $do_template . DIRECTORY_SEPARATOR . $tpl_name );
	
	return htmlspecialchars( $text, ENT_QUOTES );

}

$tr_hidden = " style='display:none'";

$templates_names = array ("template_main" => "main.tpl", "template_vote" => "vote.tpl", "template_active" => "shortstory.tpl", "template_comment" => "comments.tpl", "template_form" => "addcomments.tpl", "template_full" => "fullstory.tpl", "template_prev_next" => "navigation.tpl", "template_user" => "userinfo.tpl", "template_addnews" => "addnews.tpl", "template_search" => "search.tpl", "template_searchresult" => "searchresult.tpl", "template_stats" => "stats.tpl", "template_error" => "info.tpl", "template_reg" => "registration.tpl", "template_pass" => "lostpassword.tpl", "template_mail" => "feedback.tpl", "template_pm" => "pm.tpl", "template_offline" => "offline.tpl", "template_static" => "static.tpl", "template_poll" => "poll.tpl", "template_speedbar" => "speedbar.tpl", "template_tagscloud" => "tagscloud.tpl" );

foreach ( $templates_names as $template => $template_file ) {
	$$template = load_template( $template_file );
}

echoheader( "options", $lang['opt_theads'] );

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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['opt_edit_head']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td width="140" style="padding:4px;">{$lang['opt_theads']}</td>
        <td style="padding:4px;"><b>{$do_template}</b></td>
    </tr>
    <tr>
        <td style="padding:4px;">{$lang['opt_sys_al']}</td>
        <td style="padding:4px;"><form method="post" action="{$PHP_SELF}?mod=templates">
        <select name="do_language">
		<option value="">{$lang['sys_global']}</option>
HTML;

foreach ( $language_list as $single_language ) {
	if( $single_language == $config["lang_" . $do_template] ) {
		echo "<option selected value=\"$single_language\">$single_language</option>";
	} else {
		echo "<option value=\"$single_language\">$single_language</option>";
	}
}

echo <<<HTML
		</select>&nbsp;&nbsp;<input type="submit" value="{$lang['b_select']}" class="buttons"><input type=hidden name=user_hash value="$dle_login_hash"><input type="hidden" name="subaction" value="language"><input type="hidden" name="do_template" value="{$do_template}"></form>
		</td>
    </tr>
    <tr>
        <td style="padding:4px;">{$lang['opt_newtepled']}</td>
        <td style="padding:4px;"><form method="post" action="{$PHP_SELF}?mod=templates"><select name="do_template">
HTML;

foreach ( $templates_list as $single_template ) {
	if( $single_template == $do_template ) {
		echo "<option selected value=\"$single_template\">$single_template</option>";
	} else {
		echo "<option value=\"$single_template\">$single_template</option>";
	}
}

echo <<<HTML
</select>&nbsp;&nbsp;<input type="submit" value="{$lang['b_start']}" class="buttons">&nbsp;&nbsp;<a onClick="javascript:Help('templates')" class="maintitle" href="#">{$lang['opt_temphelp']}</a><input type=hidden name=user_hash value="$dle_login_hash"><input type="hidden" name="action" value="templates"></form></td>
    </tr>
    <tr>
        <td style="padding:2px;">&nbsp;</td>
        <td><br>[<a class="maintitle" href="{$PHP_SELF}?mod=templates&subaction=new&action=templates&user_hash={$dle_login_hash}">{$lang['opt_enewtepl']}</a>]&nbsp;
    {$show_delete_link}
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

<form method=post action="{$PHP_SELF}">
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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['opt_edteil']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
HTML;

echo '<table width="100%">';

echo '<tr> <!- start main -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'main-news1\',\'main-news2\')">' . $lang['opt_ss_m'] . '</a></b><br />' . $lang['opt_ss_m1'] . '
    </tr>
    <tr id=\'main-news1\' ' . $tr_hidden . '>
    <td width="210" valign="top" style="padding: 5px">
    <b>{headers}<br />
    <b>{AJAX}<br />
    {THEME}<br />
    {login}<br />
    {vote}<br />
    {changeskin}<br />
    {calendar}<br />
    {topnews}<br />
    {archives}<br />
    {info}<br />
    {content}<br />
	{custom}<br />
    <td width="500" valign="top" style="padding: 5px">
	' . $lang['opt_ss_m_1'] . '
    </tr>
    <tr id=\'main-news2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_main">' . $template_main . '</textarea>
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr> <!-- End main -->';

echo '<tr> <!- start active news -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'active-news1\',\'active-news2\')">' . $lang['opt_ss_h'] . '</a></b><br />' . $lang['opt_ss_d'] . '
    </tr>
    <tr id=\'active-news1\' ' . $tr_hidden . '>
    <td width="210" valign="top" style="padding: 5px">
    <b>{title}<br />
    {news-id}<br />
    {short-story}<br />
    {full-story}<br />
    {author}<br />
    {date}<br />
    {comments-num}<br />
    {category}<br />
	{category-id}<br />
	{category-icon}<br />
	{views}<br />
	{favorites}<br />
	[edit] </b>и<b> [/edit]<br />
	{link-category}<br />
    [full-link] </b>и<b> [/full-link]<br />
    [com-link] </b>и<b> [/com-link]<br />
    [xfvalue_x]<br />
    [xfgiven_x] [xfvalue_x] [/xfgiven_x]<br />
    <td width="500" valign="top" style="padding: 5px">
	' . $lang['opt_ss_d_1'] . '
    </tr>
    <tr id=\'active-news2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_active">' . $template_active . '</textarea>
    <br />
    &nbsp;
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- End active news -->

<tr> <!-- Start full story -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'full-story1\',\'full-story2\')" >' . $lang['opt_fs_h'] . '</a></b><br />' . $lang['opt_fs_d'] . '
    </tr>
    <tr id=\'full-story1\' ' . $tr_hidden . '>
    <td width="210" valign="top" style="padding: 5px">
    <b> {title}<br />
	{poll}<br />
    {full-story}<br />
    {short-story}</b><b><br />
    {author}<br />
    {date}<br />
    {rating}<br />
    {comments-num}<br />
    {category}<br />
	{category-id}<br />
	{category-icon}<br />
	{pages}<br />
	{views}<br />
	{favorites}<br />
	[edit] </b>и<b> [/edit]<br />
	{link-category}<br />
    [print-link]</b> и <b>[/print-link]<br />
    [com-link]</b> и <b>[/com-link]<br />
    [xfvalue_x]<br />
    [xfgiven_x] [xfvalue_x] [/xfgiven_x]<br />
    <td width="500" valign="top" style="padding: 5px">
	' . $lang['opt_fs_d_1'] . '
    </tr>
    <tr id=\'full-story2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_full">' . $template_full . '</textarea>
    <br />
    &nbsp;
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- End full story -->

    <tr><!-- Start stats -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'poll1\',\'poll2\')">' . $lang['templ_poll'] . '</a></b><br />' . $lang['templ_poll_i'] . '
    </tr>
    <tr id=\'poll1\' ' . $tr_hidden . '>
    <td valign="top" style="padding: 5px">
    <b>{title}<br />
    {question}<br />
    {list}<br />
    {votes}<br />
    <td width="500" valign="top" style="padding: 5px">
    ' . $lang['templ_poll_е'] . '
    </tr>
    <tr id=\'poll2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_poll">' . $template_poll . '</textarea>
    </tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- End stats -->

    <tr><!-- Start addnews -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'addnews1\',\'addnews2\')">' . $lang['opt_an_h'] . '</a></b><br />' . $lang['opt_an_d'] . '
    </tr>
    <tr id=\'addnews1\' ' . $tr_hidden . '>
    <td valign="top" colspan="2" style="padding: 5px">
    ' . $lang['opt_an_d_1'] . '
    </tr>
    <tr id=\'addnews2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_addnews">' . $template_addnews . '</textarea>
    <br />
    </tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- End addnews -->

<tr> <!-- Start comment -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'comment1\',\'comment2\')" >' . $lang['opt_c_h'] . '</a></b><br />' . $lang['opt_c_d'] . '
    </tr>
    <tr id=\'comment1\' ' . $tr_hidden . '>
    <td width="210" valign="top" style="padding: 5px">
    <b>{author}<br />
    {mail}<br />
    {date}<br />
    {comment}<br />
    {comment-id}<br />
    {ip}<br />
	{foto}<br />
	{icq}<br />
	{land}<br />
	{fullname}<br />
	{registration}<br />
	{signature}<br />
	[signature] </b>и<b> [/signature]<br />
    [com-edit] </b>и<b> [/com-edit]<br />
    [com-del] </b>и<b> [/com-del]<br />
    <td width="500" valign="top" style="padding: 5px">
    ' . $lang['opt_c_d_1'] . '
    </tr>
    <tr id=\'comment2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_comment">' . $template_comment . '</textarea>
    <br />
    &nbsp;
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- End comment -->

<tr> <!-- Start add comment form -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'add-comment-form1\',\'add-comment-form2\')" >' . $lang['opt_fc_h'] . '</a></b><br />' . $lang['opt_fc_d'] . '
    </tr>
    <tr id=\'add-comment-form1\' ' . $tr_hidden . '>
    <td valign="top" colspan="2" style="padding: 5px">
	' . $lang['opt_fc_d_1'] . '
    </tr>
    <tr id=\'add-comment-form2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_form">' . $template_form . '</textarea>
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- End add comment form -->

<tr> <!-- Start previous & next -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'previous-next1\',\'previous-next2\')" >' . $lang['opt_n_h'] . '</a></b><br />' . $lang['opt_n_d'] . '
    </tr>
    <tr id=\'previous-next1\' ' . $tr_hidden . '>
    <td valign="top" style="padding: 5px">
    <b> [prev-link] </b>and<b> [/prev-link]<br />
    [next-link] </b>and<b> [/next-link]<br />
    {pages}
    <td valign="top" style="padding: 5px">
    ' . $lang['opt_n_d_1'] . '
    </tr>

    <tr id=\'previous-next2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_prev_next">' . $template_prev_next . '</textarea>
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- End previous & next -->

<tr> <!-- Start vote -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'vote1\',\'vote2\')" >' . $lang['opt_r_v'] . '</a></b><br />' . $lang['opt_r_vd'] . '
    </tr>
    <tr id=\'vote1\' ' . $tr_hidden . '>
    <td valign="top" style="padding: 5px">
    <b>{title}<br />
    {list}<br />
    {vote_id}<br />
    {votes}<br /></b>
	<b>[votelist]</b> и <b>[/votelist]</b><br />
	<b>[voteresult]</b> и <b>[/voteresult]</b><br />
    <td valign="top" style="padding: 5px">
    ' . $lang['opt_r_v_1'] . '
    </tr>

    <tr id=\'vote2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_vote">' . $template_vote . '</textarea>
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- End vote -->

<tr> <!-- Start registration -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'reg1\',\'reg2\')" >' . $lang['opt_r_h'] . '</a></b><br />' . $lang['opt_r_d'] . '
    </tr>
    <tr id=\'reg1\' ' . $tr_hidden . '>
    <td valign="top" style="padding: 5px">
    <b>[registration]</b> и <b>[/registration]</b><br />
	<b>[validation]</b> и <b>[/validation]</b><br />
    <td valign="top" style="padding: 5px">
    ' . $lang['opt_r_d_1'] . '
    </tr>

    <tr id=\'reg2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_reg">' . $template_reg . '</textarea>
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- End registration -->

<tr> <!-- Start userinfo -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'user1\',\'user2\')" >' . $lang['opt_u_h'] . '</a></b><br />' . $lang['opt_u_d'] . '
    </tr>
    <tr id=\'user1\' ' . $tr_hidden . '>
    <td valign="top" style="padding: 5px">
    <b>{usertitle}<br />
    {info}<br />
    {editmail}<br />
    {comm_num}<br />
    {news_num}<br />
    {status}<br />
    {rate}<br />
    {foto}<br />
    {registration}<br />
    {editinfo}<br />
    {hidemail}<br />
    {fullname}<br />
    {land}<br />
    {icq}<br />
    {comments}<br />
	[not-logged]</b> и <b>[/not-logged]</b>
    <td valign="top" style="padding: 5px">
    ' . $lang['opt_u_d_1'] . '
    </tr>

    <tr id=\'user2\' ' . $tr_hidden . '>
    <td colspan="2" >
    <textarea rows="15" style="width:100%;" name="edit_user">' . $template_user . '</textarea>
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- End userinfo -->

    <tr><!-- Start search -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'search1\',\'search2\')">' . $lang['opt_se_hf'] . '</a></b><br />' . $lang['opt_se_df'] . '
    </tr>
    <tr id=\'search1\' ' . $tr_hidden . '>
    <td valign="top" style="padding: 5px">
    <b>{searchtable}<br />
    {searchmsg}<br />
    [searchmsg] </b>и<b> [/searchmsg]<br />
    <td width="500" valign="top" style="padding: 5px">
    ' . $lang['opt_se_d_1f'] . '
    </tr>
    <tr id=\'search2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_search">' . $template_search . '</textarea>
    </tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- stop search -->

    <tr><!-- Start search -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'searchresult1\',\'searchresult2\')">' . $lang['opt_se_h'] . '</a></b><br />' . $lang['opt_se_d'] . '
    </tr>
    <tr id=\'searchresult1\' ' . $tr_hidden . '>
    <td valign="top" style="padding: 5px">
    <b>{result-date}<br />
    {result-title}<br />
    {result-author}<br />
    {result-text}<br />
    {result-comments}</b><br />
    <b>[result-link] </b>и<b> [/result-link]</b><br />
    <b>[searchposts] </b>и<b> [/searchposts]</b><br />
    <b>[searchcomments] </b>и<b> [/searchcomments]</b><br />
    <b>[fullresult] </b>и<b> [/fullresult]</b><br />
    <b>[shortresult] </b>и<b> [/shortresult]</b><br />
    <td width="500" valign="top" style="padding: 5px">
    ' . $lang['opt_se_d_1'] . '
    </tr>
    <tr id=\'searchresult2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_searchresult">' . $template_searchresult . '</textarea>
    </tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- stop search -->


    <tr><!-- Start stats -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'stats1\',\'stats2\')">' . $lang['opt_st_h'] . '</a></b><br />' . $lang['opt_st_d'] . '
    </tr>
    <tr id=\'stats1\' ' . $tr_hidden . '>
    <td valign="top" style="padding: 5px">
    <b>{datenbank}<br />
    {news_num}<br />
    {news_allow}<br />
    {news_main}<br />
    {news_moder}<br />
    {comm_num}<br />
    {user_num}<br />
    {user_banned}<br />
    {topusers}</b>
    <td width="500" valign="top" style="padding: 5px">
    ' . $lang['opt_st_d_1'] . '
    </tr>
    <tr id=\'stats2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_stats">' . $template_stats . '</textarea>
    </tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- End stats -->

<tr> <!-- Start error -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'error1\',\'error2\')" >' . $lang['opt_er_h'] . '</a></b><br />' . $lang['opt_er_d'] . '
    </tr>
    <tr id=\'error1\' ' . $tr_hidden . '>
    <td valign="top" colspan="2" style="padding: 5px">
	' . $lang['opt_er_d_1'] . '
    </tr>
    <tr id=\'error2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_error">' . $template_error . '</textarea>
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- End error -->

<tr> <!-- Start lostpass -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'lost1\',\'lost2\')" >' . $lang['opt_pa_h'] . '</a></b><br />' . $lang['opt_pa_d'] . '
    </tr>
    <tr id=\'lost1\' ' . $tr_hidden . '>
    <td valign="top" colspan="2" style="padding: 5px">
	' . $lang['opt_pa_d_1'] . '
    </tr>
    <tr id=\'lost2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_pass">' . $template_pass . '</textarea>
   </tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- End error -->

<tr> <!-- Start feedback -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'mail1\',\'mail2\')" >' . $lang['opt_sm_h'] . '</a></b><br />' . $lang['opt_sm_d'] . '
    </tr>
    <tr id=\'mail1\' ' . $tr_hidden . '>
    <td valign="top" colspan="2" style="padding: 5px">
	' . $lang['opt_sm_d_1'] . '
    </tr>
    <tr id=\'mail2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_mail">' . $template_mail . '</textarea>
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- End feedbak -->

<tr> <!-- Start pm -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'pm1\',\'pm2\')" >' . $lang['opt_pm_h'] . '</a></b><br />' . $lang['opt_pm_d'] . '
    </tr>
    <tr id=\'pm1\' ' . $tr_hidden . '>
    <td valign="top" style="padding: 5px">
    <b>[inbox] и [/inbox]<br />
    [outbox] и [/outbox]<br />
    [new_pm] и [/new_pm]<br />
    [pmlist] и [/pmlist]<br />
    {pmlist}<br />
    [newpm] и [/newpm]<br />
    {author}<br />
    {subj}<br />
    {bbcode}<br />
    {text}<br />
    [readpm] и [/readpm]<br />
    [reply] и [/reply]<br />
    [del] и [/del]<br />
	</b>
    <td width="500" valign="top" style="padding: 5px">
    ' . $lang['opt_pm_d_1'] . '
    </tr>
    <tr id=\'pm2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_pm">' . $template_pm . '</textarea>
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- End pm -->

<tr> <!-- Start offline -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'off1\',\'off2\')" >' . $lang['opt_sm_offline'] . '</a></b><br />' . $lang['opt_sm_offd'] . '
    </tr>
    <tr id=\'off1\' ' . $tr_hidden . '>
    <td valign="top" colspan="2" style="padding: 5px">
	&nbsp;
    </tr>
    <tr id=\'off2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_offline">' . $template_offline . '</textarea>
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- End offline -->

<tr> <!-- Start offline -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'sp1\',\'sp2\')" >' . $lang['opt_sm_speed'] . '</a></b><br />' . $lang['opt_sm_speedd'] . '
    </tr>
    <tr id=\'sp1\' ' . $tr_hidden . '>
    <td valign="top" colspan="2" style="padding: 5px">
	&nbsp;
    </tr>
    <tr id=\'sp2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_speedbar">' . $template_speedbar . '</textarea>
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- End offline -->

<tr> <!-- Start static -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'st1\',\'st2\')" >' . $lang['opt_sm_static'] . '</a></b><br />' . $lang['opt_sm_statd'] . '
    </tr>
    <tr id=\'st1\' ' . $tr_hidden . '>
    <td valign="top" colspan="2" style="padding: 5px">
	' . $lang['opt_sm_stat_1'] . '
    </tr>
    <tr id=\'st2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_static">' . $template_static . '</textarea>
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- End static -->

<tr> <!-- Start tagscloud -->
    <td height="40"  style="padding: 5px;" colspan="2">
    <b><a class="main" href="javascript:ShowOrHide(\'cl1\',\'cl2\')" >' . $lang['opt_sm_cl_1'] . '</a></b><br />' . $lang['opt_sm_cl_3'] . '
    </tr>
    <tr id=\'cl1\' ' . $tr_hidden . '>
    <td valign="top" colspan="2" style="padding: 5px">
	' . $lang['opt_sm_cl_2'] . '
    </tr>
    <tr id=\'cl2\' ' . $tr_hidden . '>
    <td colspan="2">
    <textarea rows="15" style="width:100%;" name="edit_tagscloud">' . $template_tagscloud . '</textarea>
</tr><tr>
        <td colspan="2"><div class="hr_line"></div></td>
    </tr><!-- End tagscloud -->';

echo <<<HTML
</table>
    <input type="hidden" name="mod" value="templates">
    <input type="hidden" name="action" value="dosavetemplates">
    <input type="hidden" name="do_template" value="{$do_template}">
    <input type=hidden name=user_hash value="{$dle_login_hash}">
    &nbsp;&nbsp;<input type="submit" value="{$lang['user_save']}" class="buttons">
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

echofooter();
?>