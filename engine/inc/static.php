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
 Файл: static.php
-----------------------------------------------------
 Назначение: Создание статистических страниц
=====================================================
*/
if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

define ('POSTS_ON_PAGE', 300); // !


if( $member_id['user_group'] > 2 ) {
	msg( "error", $lang['addnews_denied'], $lang['db_denied'] );
}

include_once ENGINE_DIR . '/classes/parse.class.php';

$parse = new ParseFilter( Array (), Array (), 1, 1 );
$parse->allow_php = true;

function SelectSkin($skin) {
	global $lang;
	
	$templates_list = array ();
	
	$handle = opendir( './templates' );
	
	while ( false !== ($file = readdir( $handle )) ) {
		if( is_dir( "./templates/$file" ) and ($file != "." and $file != "..") ) {
			$templates_list[] = $file;
		}
	}
	closedir( $handle );
	
	$skin_list = "<select name=skin_name>";
	$skin_list .= "<option value=\"\">" . $lang['cat_skin_sel'] . "</option>";
	
	foreach ( $templates_list as $single_template ) {
		if( $single_template == $skin ) $selected = " selected";
		else $selected = "";
		$skin_list .= "<option value=\"$single_template\"" . $selected . ">$single_template</option>";
	}
	$skin_list .= '</select>';
	
	return $skin_list;
}

if( $action == "addnew" ) {
	
	echoheader( "static", "static" );
	
	echo "
    <SCRIPT LANGUAGE=\"JavaScript\">
    function preview(){";
	
	if( $config['allow_static_wysiwyg'] == "yes" ) {
		echo "document.getElementById('template').value = tinyMCE.get('template').getContent();";
	}
	
	echo "if(document.static.template.value == '' || document.static.description.value == '' || document.static.name.value == ''){ alert('$lang[static_err_1]'); }
    else{
        dd=window.open('','prv','height=400,width=750,resizable=1,scrollbars=1')
        document.static.mod.value='preview';document.static.target='prv'
        document.static.submit(); dd.focus()
        setTimeout(\"document.static.mod.value='static';document.static.target='_self'\",500)
    }
    }
    onload=focus;function focus(){document.forms[0].name.focus();}
    </SCRIPT>";
	
	if( $config['allow_static_wysiwyg'] == "yes" ) echo "<form method=post name=\"static\" id=\"static\" onsubmit=\"document.getElementById('template').value = tinyMCE.get('template').getContent(); if(document.static.name.value == '' || document.static.description.value == '' || document.static.template.value == ''){alert('$lang[vote_alert]');return false}\" action=\"\">";
	else echo "<form method=post name=\"static\" id=\"static\" onsubmit=\"if(document.static.name.value == '' || document.static.description.value == '' || document.static.template.value == ''){alert('$lang[vote_alert]');return false}\" action=\"\">";
	
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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['static_a']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td width="150" style="padding:2px;">{$lang['static_title']}</td>
        <td style="padding:2px;"><input type="text" name="name" size="25"  class="edit"><a href="#" class="hintanchor" onMouseover="showhint('{$lang[hint_stitle]}', this, event, '250px')">[?]</a></td>
    </tr>
    <tr>
        <td style="padding:2px;">{$lang['static_descr']}</td>
        <td style="padding:2px;"><input type="text" name="description" size="55"  class="edit"><a href="#" class="hintanchor" onMouseover="showhint('{$lang[hint_sdesc]}', this, event, '250px')">[?]</a></td>
    </tr>
HTML;
	
	if( $config['allow_static_wysiwyg'] == "yes" ) {
		
		include (ENGINE_DIR . '/editor/static.php');
	
	} else {
		
		include (ENGINE_DIR . '/inc/inserttag.php');
		
		echo <<<HTML
    <tr>
        <td style="padding:2px;">{$lang['static_templ']}</td>
        <td style="padding-left:2px;">{$bb_code}<textarea style="width:98%; height:300px;" name="template" id="template"  onclick=setFieldName(this.name)></textarea><script type=text/javascript>var selField  = "template";</script></td>
    </tr>
HTML;
	
	}
	
	if( $config['allow_static_wysiwyg'] != "yes" ) $fix_br = "<br /><input type=\"checkbox\" name=\"allow_br\" value=\"1\"> {$lang['static_br_html']}";
	else $fix_br = "";
	$groups = get_groups();
	$skinlist = SelectSkin( '' );
	
	echo <<<HTML
		<tr><td colspan="2"><div class="hr_line"></div></td></tr>
	    <tr>
	        <td>&nbsp;</td>
	        <td>{$lang['add_metatags']}<a href="#" class="hintanchor" onMouseover="showhint('{$lang['hint_metas']}', this, event, '220px')">[?]</a></td>
	    </tr>
	    <tr>
	        <td height="29" style="padding-left:5px;">{$lang['meta_descr']}</td>
	        <td><input type="text" name="descr" id="autodescr" style="width:388px;" class="edit"> ({$lang['meta_descr_max']})</td>
	    </tr>
	    <tr>
	        <td height="29" style="padding-left:5px;">{$lang['meta_keys']}</td>
	        <td><textarea name="keywords" id='keywords' style="width:388px;height:70px;"></textarea></td>
	    </tr>
		<tr><td colspan="2"><div class="hr_line"></div></td></tr>
    <tr>
        <td style="padding:2px;">{$lang['static_tpl']}</td>
        <td style="padding-left:2px;"><input type="text" name="static_tpl" size="20"  class="edit">.tpl<a href="#" class="hintanchor" onMouseover="showhint('{$lang[hint_stpl]}', this, event, '250px')">[?]</a></td>
    </tr>
    <tr>
        <td style="padding:2px;">{$lang['static_skin']}</td>
        <td style="padding:2px;">{$skinlist}<a href="#" class="hintanchor" onMouseover="showhint('{$lang[hint_static_skin]}', this, event, '250px')">[?]</a></td>
    </tr>
    <tr>
        <td style="padding:2px;">{$lang['stat_allow']}</td>
        <td style="padding:2px;"><select name="grouplevel[]" style="width:150px;height:93px;" multiple><option value="all" selected>{$lang['edit_all']}</option>{$groups}</select></td>
    </tr>
    <tr>
        <td style="padding:2px;">&nbsp;</td>
        <td style="padding-left:2px;"><input type="checkbox" name="allow_template" value="1" checked> {$lang['st_al_templ']}{$fix_br}</td>
    </tr>
    <tr>
        <td style="padding:2px;">&nbsp;</td>
        <td><input type="submit" value="{$lang['user_save']}" class="buttons">&nbsp;&nbsp;&nbsp;<input onClick="preview()" type="button" class="buttons" value="{$lang['btn_preview']}" style="width:100px;">
	<input type=hidden name="action" value="dosavenew">
	<input type=hidden name="mod" value="static">
	<input type=hidden name="preview_mode" value="static" >
	<input type="hidden" name="user_hash" value="$dle_login_hash" />
	<br><br></td>
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
	
	echofooter();
} elseif( $action == "dosavenew" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	if( function_exists( "get_magic_quotes_gpc" ) && get_magic_quotes_gpc() ) $_POST['template'] = stripslashes( $_POST['template'] );  
	
	if( $config['allow_static_wysiwyg'] == "yes" or $allow_br != '1' ) {
		$template = $parse->BB_Parse( $parse->code_parse( $_POST['template'] ) );
	} else {
		$template = $parse->BB_Parse( $parse->code_parse( $_POST['template'] ), false );
	}

	$metatags = create_metatags( $template );
	$template = addslashes( $template );
	
	$name = trim( $db->safesql( htmlspecialchars( $_POST['name'] ) ) );
	$descr = trim( $db->safesql( htmlspecialchars( $_POST['description'] ) ) );
	$template = $db->safesql( $template );
	$tpl = trim( totranslit( $_POST['static_tpl'] ) );
	$skin_name = strip_tags( $db->safesql( $_POST['skin_name'] ) );
	
	
	
	if( ! count( $_POST['grouplevel'] ) ) $_POST['grouplevel'] = array ("all" );
	$grouplevel = $db->safesql( implode( ',', $_POST['grouplevel'] ) );
	
	$allow_br = intval( $_POST['allow_br'] );
	$allow_template = intval( $_POST['allow_template'] );
	$added_time = time() + ($config['date_adjust'] * 60);
	
	if( $name == "" or $descr == "" or $template == "" ) msg( "error", $lang['static_err'], $lang['static_err_1'], "javascript:history.go(-1)" );
	
	$db->query( "INSERT INTO " . PREFIX . "_static (name, descr, template, allow_br, allow_template, grouplevel, tpl, metadescr, metakeys, template_folder, date) values ('$name', '$descr', '$template', '$allow_br', '$allow_template', '$grouplevel', '$tpl', '{$metatags['description']}', '{$metatags['keywords']}', '{$skin_name}', '{$added_time}')" );
	$row = $db->insert_id();
	$db->query( "UPDATE " . PREFIX . "_static_files SET static_id='{$row}' WHERE author = '{$member_id['name']}' AND static_id = '0'" );
	
	msg( "info", $lang['static_addok'], $lang['static_addok_1'], "?mod=static" );


	
	if( $_GET['page'] == "rules" ) {
		
		$row = $db->super_query( "SELECT * FROM " . PREFIX . "_static where name='dle-rules-page'" );
		$lang['static_edit'] = $lang['rules_edit'];
		if( ! $row['id'] ) {
			$id = "";
			$row['allow_template'] = "1";
		} else
			$id = $row['id'];
		
		if( ! $config['registration_rules'] ) $lang['rules_descr'] = $lang['rules_descr'] . " <font color=\"red\">" . $lang['rules_check'] . "</font>";
	
	} else {
		
		$row = $db->super_query( "SELECT * FROM " . PREFIX . "_static where id='$id'" );
	}
	
	if( $row['allow_br'] != '1' or $config['allow_static_wysiwyg'] == "yes" ) {
		
		$row['template'] = $parse->decodeBBCodes( $row['template'], true, $config['allow_static_wysiwyg'] );
	
	} else {
		
		$row['template'] = $parse->decodeBBCodes( $row['template'], false );
	
	}
	
	$skinlist = SelectSkin( $row['template_folder'] );
	
	echoheader( "static", "static" );
	
	echo "
    <SCRIPT LANGUAGE=\"JavaScript\">
    function preview(){";
	
	if( $config['allow_static_wysiwyg'] == "yes" ) {
		echo "document.getElementById('template').value = tinyMCE.get('template').getContent();";
	}
	
	echo "if(document.static.template.value == ''){ alert('$lang[static_err_1]'); }
    else{
        dd=window.open('','prv','height=400,width=750,resizable=1,scrollbars=1')
        document.static.mod.value='preview';document.static.target='prv'
        document.static.submit(); dd.focus()
        setTimeout(\"document.static.mod.value='static';document.static.target='_self'\",500)
    }
    }
    </SCRIPT>";
	
	if( $_GET['page'] == "rules" ) {
		
		if( $config['allow_static_wysiwyg'] == "yes" ) echo "<form method=post name=\"static\" id=\"static\" onsubmit=\"document.getElementById('template').value = tinyMCE.get('template').getContent();\" action=\"\">";
		else echo "<form method=post name=\"static\" id=\"static\" action=\"\">";
	
	} else {
		
		if( $config['allow_static_wysiwyg'] == "yes" ) echo "<form method=post name=\"static\" id=\"static\" onsubmit=\"document.getElementById('template').value = tinyMCE.get('template').getContent(); if(document.static.name.value == '' || document.static.description.value == '' || document.static.template.value == ''){alert('$lang[vote_alert]');return false}\" action=\"\">";
		else echo "<form method=post name=\"static\" id=\"static\" onsubmit=\"if(document.static.name.value == '' || document.static.description.value == '' || document.static.template.value == ''){alert('$lang[vote_alert]');return false}\" action=\"\">";
	
	}
	
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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['static_edit']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
HTML;
	
	if( $_GET['page'] == "rules" ) {
		
		echo <<<HTML
    <tr>
        <td width="110" style="padding:2px;">{$lang['static_descr']}</td>
        <td style="padding:2px;" class="navigation">{$lang['rules_descr']}</td>
    </tr>
HTML;
	
	} else {
		
		echo <<<HTML
    <tr>
        <td width="110" style="padding:2px;">{$lang['static_title']}</td>
        <td style="padding:2px;"><input type="text" name="name" size="25"  class="edit" value="{$row['name']}"><a href="#" class="hintanchor" onMouseover="showhint('{$lang[hint_stitle]}', this, event, '250px')">[?]</a></td>
    </tr>
    <tr>
        <td style="padding:2px;">{$lang['static_descr']}</td>
        <td style="padding:2px;"><input type="text" name="description" size="55"  class="edit" value="{$row['descr']}"><a href="#" class="hintanchor" onMouseover="showhint('{$lang[hint_sdesc]}', this, event, '250px')">[?]</a></td>
    </tr>
HTML;
	
	}
	
	if( $config['allow_static_wysiwyg'] == "yes" ) {
		
		include (ENGINE_DIR . '/editor/static.php');
	
	} else {
		
		include (ENGINE_DIR . '/inc/inserttag.php');
		
		echo <<<HTML
    <tr>
        <td style="padding:2px;">{$lang['static_templ']}</td>
        <td style="padding:2px;">{$bb_code}<textarea style="width:98%; height:300px;" name="template" id="template"  onclick=setFieldName(this.name)>{$row['template']}</textarea><script type=text/javascript>var selField  = "template";</script></td>
    </tr>
HTML;
	
	}
	
	if( $row['allow_br'] ) $check = "checked";
	else $check = "";
	if( $row['allow_template'] ) $check_t = "checked";
	else $check_t = "";
	if( $config['allow_static_wysiwyg'] != "yes" ) $fix_br = "<br /><input type=\"checkbox\" name=\"allow_br\" value=\"1\" {$check}> {$lang['static_br_html']}";
	else $fix_br = "";
	$groups = get_groups( explode( ',', $row['grouplevel'] ) );
	if( $row['grouplevel'] == "all" ) $check_all = "selected";
	else $check_all = "";
	
	echo <<<HTML
		<tr><td colspan="2"><div class="hr_line"></div></td></tr>
	    <tr>
	        <td>&nbsp;</td>
	        <td>{$lang['add_metatags']}<a href="#" class="hintanchor" onMouseover="showhint('{$lang['hint_metas']}', this, event, '220px')">[?]</a></td>
	    </tr>
	    <tr>
	        <td height="29" style="padding-left:5px;">{$lang['meta_descr']}</td>
	        <td><input type="text" name="descr" style="width:388px;" class="edit" value="{$row['metadescr']}"> ({$lang['meta_descr_max']})</td>
	    </tr>
	    <tr>
	        <td height="29" style="padding-left:5px;">{$lang['meta_keys']}</td>
	        <td><textarea name="keywords" style="width:388px;height:70px;">{$row['metakeys']}</textarea></td>
	    </tr>
		<tr><td colspan="2"><div class="hr_line"></div></td></tr>
    <tr>
        <td style="padding:2px;">{$lang['static_tpl']}</td>
        <td style="padding:2px;"><input type="text" name="static_tpl" size="20" value="{$row['tpl']}" class="edit">.tpl<a href="#" class="hintanchor" onMouseover="showhint('{$lang[hint_stpl]}', this, event, '250px')">[?]</a></td>
    </tr>
HTML;
	
	if( $_GET['page'] != "rules" ) echo <<<HTML
    <tr>
        <td style="padding:2px;">{$lang['static_skin']}</td>
        <td style="padding:2px;">{$skinlist}<a href="#" class="hintanchor" onMouseover="showhint('{$lang[hint_static_skin]}', this, event, '250px')">[?]</a></td>
    </tr>
    <tr>
        <td style="padding:2px;">{$lang['stat_allow']}</td>
        <td style="padding:2px;"><select name="grouplevel[]" style="width:150px;height:93px;" multiple><option value="all" {$check_all}>{$lang['edit_all']}</option>{$groups}</select></td>
    </tr>
HTML;
	
	echo <<<HTML
    <tr>
        <td style="padding:2px;">&nbsp;</td>
        <td style="padding-left:2px;"><input type="checkbox" name="allow_template" value="1" {$check_t}> {$lang['st_al_templ']}{$fix_br}</td>
    </tr>
    <tr>
        <td style="padding:2px;">&nbsp;</td>
        <td><br>&nbsp;<input type="submit" value="{$lang['user_save']}" class="buttons">&nbsp;&nbsp;&nbsp;<input onClick="preview()" type="button" class="buttons" value="{$lang['btn_preview']}" style="width:100px;">
	<input type="hidden" name="action" value="dosaveedit">
	<input type=hidden name="mod" value="static">
	<input type=hidden name="preview_mode" value="static" >
	<input type="hidden" name="user_hash" value="$dle_login_hash" />
	<input type="hidden" name="id" value="{$id}">
	<br><br></td>
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
	
	echofooter();
} elseif( $action == "dosaveedit" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}

	if( function_exists( "get_magic_quotes_gpc" ) && get_magic_quotes_gpc() ) $_POST['template'] = stripslashes( $_POST['template'] );  
	
	if( $config['allow_static_wysiwyg'] == "yes" or $allow_br != '1' ) {

		if ( $config['allow_static_wysiwyg'] != "yes" )	$_POST['template'] = $parse->code_parse( $_POST['template'] );

		$template = $parse->BB_Parse( $_POST['template'] );

	} else {
		$template = $parse->BB_Parse( $parse->code_parse( $_POST['template'] ), false );
	}
	
	$metatags = create_metatags( $template );
	$template = addslashes( $template );
	
	if( $_GET['page'] == "rules" ) {
		
		$name = "dle-rules-page";
		$descr = $lang['rules_edit'];
	
	} else {
		
		$name = trim( $db->safesql( htmlspecialchars( $_POST['name'] ) ) );
		$descr = trim( $db->safesql( htmlspecialchars( $_POST['description'] ) ) );
		
		if( ! count( $_POST['grouplevel'] ) ) $_POST['grouplevel'] = array ("all" );
		$grouplevel = $db->safesql( implode( ',', $_POST['grouplevel'] ) );
		
		
	
	}
	
	$template = $db->safesql( $template );
	$allow_br = intval( $_POST['allow_br'] );
	$allow_template = intval( $_POST['allow_template'] );
	$tpl = trim( totranslit( $_POST['static_tpl'] ) );
	$skin_name = strip_tags( $db->safesql( $_POST['skin_name'] ) );
	$added_time = time() + ($config['date_adjust'] * 60);
	
	if( $_GET['page'] == "rules" ) {
		
		if( $_POST['id'] ) {
			
			$db->query( "UPDATE " . PREFIX . "_static SET descr='$descr', template='$template', allow_br='$allow_br', allow_template='$allow_template', grouplevel='all', tpl='$tpl', metadescr='{$metatags['description']}', metakeys='{$metatags['keywords']}', template_folder='{$skin_name}' WHERE name='dle-rules-page'" );
		
		} else {
			
			$db->query( "INSERT INTO " . PREFIX . "_static (name, descr, template, allow_br, allow_template, grouplevel, tpl, metadescr, metakeys, template_folder) values ('$name', '$descr', '$template', '$allow_br', '$allow_template', 'all', '$tpl', '{$metatags['description']}', '{$metatags['keywords']}', '{$skin_name}')" );
			$row = $db->insert_id();
			$db->query( "UPDATE " . PREFIX . "_static_files SET static_id='{$row}' WHERE author = '{$member_id['name']}' AND static_id = '0'" );
		
		}
		
		msg( "info", $lang['rules_ok'], $lang['rules_ok'], "?mod=static&action=doedit&page=rules" );
	
	} else {
		
		$id = intval( $_GET['id'] );
		if( $name == "" or $descr == "" or $template == "" ) msg( "error", $lang['static_err'], $lang['static_err_1'], "javascript:history.go(-1)" );
		
		$db->query( "UPDATE " . PREFIX . "_static set name='$name', descr='$descr', template='$template', allow_br='$allow_br', allow_template='$allow_template', grouplevel='$grouplevel', tpl='$tpl', metadescr='{$metatags['description']}', metakeys='{$metatags['keywords']}', template_folder='{$skin_name}', date='{$added_time}' WHERE id='$id'" );
		
		msg( "info", $lang['static_addok'], $lang['static_addok_1'], "?mod=static" );
	
	}
	
	msg( "info", $lang['static_addok'], $lang['static_addok_1'], "?mod=static" );

} elseif( $action == "dodelete" ) {
	
	if( $_REQUEST['user_hash'] == "" or $_REQUEST['user_hash'] != $dle_login_hash ) {
		
		die( "Hacking attempt! User not found" );
	
	}
	
	$id = intval( $_GET['id'] );
	
	$db->query( "DELETE FROM " . PREFIX . "_static WHERE id='$id'" );
	
	$db->query( "SELECT name, onserver FROM " . PREFIX . "_static_files WHERE static_id = '$id'" );
	
	while ( $row = $db->get_row() ) {
		
		if( $row['onserver'] ) {
			
			@unlink( ROOT_DIR . "/uploads/files/" . $row['onserver'] );
		
		} else {
			
			$url_image = explode( "/", $row['name'] );
			
			if( count( $url_image ) == 2 ) {
				
				$folder_prefix = $url_image[0] . "/";
				$dataimages = $url_image[1];
			
			} else {
				
				$folder_prefix = "";
				$dataimages = $url_image[0];
			
			}
			
			@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . $dataimages );
			@unlink( ROOT_DIR . "/uploads/posts/" . $folder_prefix . "thumbs/" . $dataimages );
		}
	
	}
	
	$db->query( "DELETE FROM " . PREFIX . "_static_files WHERE static_id = '$id'" );
	
	msg( "info", $lang['static_del'], $lang['static_del_1'], "$PHP_SELF?mod=static" );

} else {
	echoheader( "static", "static" );
	
	echo '<script language="javascript">
    <!-- begin
    function confirmdelete(id, user){
    var agree=confirm("' . $lang['static_confirm'] . '");
    if (agree)
    document.location="' . $PHP_SELF . '?mod=static&action=dodelete&user_hash=' . $dle_login_hash . '&id="+id;
    }
    // end -->
    </script>';
	
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
        <td bgcolor="#EFEFEF" height="29" style="padding-left:10px;"><div class="navigation">{$lang['static_head']}</div></td>
    </tr>
</table>
<div class="unterline"></div>
<table width="100%">
    <tr>
        <td width="150" style="padding:2px;">{$lang['static_title']}</td>
        <td>{$lang['static_descr']}</td>
        <td width="100" align="center">{$lang['st_views']}</td>
        <td width="200">{$lang['user_action']}</td>
    </tr>
	<tr>
	<form action="#" method="get">
	<input type="text" name="query" value="{$_GET['query']}">
	<input type="hidden" name="mod" value="static">
	<input type="submit" value="Поиск" >
	</form>
	 <td colspan="4"><div class="hr_line"></div></td>
	 </tr>
HTML;

    if (isset($_GET['query'])) {
        $query_search = trim(mysql_escape_string($_GET['query']));
        $where = "WHERE descr LIKE '%{$query_search}%'";
    }else
        $where = "";
	
	/*
	 * Pagination. bobgubko.ru
	 */
	 
	$page = (int)$_GET['page'];
	$page = !$page ? 1 : $page;
	
	$total = $db->super_query ("SELECT COUNT(*) AS count FROM " . PREFIX . "_static $where");
	$total = $total['count'];
	
	$pages_count = ceil ($total / POSTS_ON_PAGE);
	
	$from = ($page - 1) * POSTS_ON_PAGE;
	$to = POSTS_ON_PAGE;
	
	$pages_list = '<div style="margin-bottom:5px;text-align:center;">';
	
	if ($page > 1) {
	  $pages_list .= "<a href='?mod=static&query={$query_search}&page=1' title='На первую страницу'>&lt;&lt; <a href='?mod=static&query={$query_search}&page=" . ($page - 1) . "' title='На предыдущую страницу'>&lt;</a> ";
	} else {
	  $pages_list .= "&lt;&lt; &lt; ";
	}
	
	for ($i = 1; $i <= ceil ($total / POSTS_ON_PAGE); $i++) {
	  
	  if ($i != $page) {
	    $pages_list .= "<a href='?mod=static&query={$query_search}&page={$i}' title='Страница {$i}'>{$i}</a> ";
	    continue;
	  }
	  $pages_list .= "<b title='Страница {$i}. Текущая.'>{$i}</b> ";
	}
	
	if ($page < $pages_count) {
	  $pages_list .= "<a href='?mod=static&query={$query_search}&page=" . ($page + 1) . "' title='На следующую страницу'>&gt;</a> <a href='?mod=static&query={$query_search}&page={$pages_count}' title='На последнюю страницу'>&gt;&gt;";
	} else {
	  $pages_list .= "&gt; &gt;&gt;";
	}
	
	$pages_list .= "</div>";
	
	echo $pages_list;
	
	echo '<div class="unterline"></div>';
	
	$db->query( "SELECT * FROM " . PREFIX . "_static {$where} ORDER BY id DESC LIMIT $from, $to" );

	
	while ( $row = $db->get_row() ) {
		
		if( $row['name'] == "dle-rules-page" ) continue;
		
		if( $config['allow_alt_url'] == "yes" ) $vlink = $config['http_home_url'] . $row['name'] . ".html";
		else $vlink = $config['http_home_url'] . "index.php?do=static&page=" . $row['name'];
		
		echo "<tr><td style=\"padding:2px;\">&nbsp;{$row['name']}</td><td>";
		
		echo $row['descr'] . "</td><td align=\"center\">" . $row['views'] . "</td>";
		echo "<td>
        [<a  class=maintitle href='$vlink' target=\"_blank\">$lang[static_view]</a>]&nbsp;[<a  class=maintitle href='$PHP_SELF?mod=static&action=doedit&id=$row[id]'>$lang[user_edit]</a>]&nbsp;[<a class=maintitle onClick=\"javascript:confirmdelete('$row[id]', '$row[name]'); return(false)\"  href=\"$PHP_SELF?mod=static&action=dodelete&user_hash={$dle_login_hash}&id=$row[id]\">$lang[user_del]</a>]
        </tr><tr><td background=\"engine/skins/images/mline.gif\" height=1 colspan=4></td></tr>";
	
	}
	$db->free();
	
	echo <<<HTML
	<tr><td colspan="4"><div class="hr_line"></div></td></tr>
</table>
&nbsp;&nbsp;&nbsp;<input type="button" value="{$lang['static_new']}" class="bbcodes" onclick="document.location='$PHP_SELF?mod=static&action=addnew'">
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
}
?>