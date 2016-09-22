<?php
require_once ROOT_DIR.'/engine/classes/templates.class.php';

$tpl = new dle_template;

 if ($_POST['preview_mode'] == "static" AND $_POST['skin_name'])
 {
	if (@is_dir(ROOT_DIR.'/templates/'.$_POST['skin_name']))
		{
			$config['skin'] = $_POST['skin_name'];
		}

 }

$tpl->dir = ROOT_DIR.'/templates/'.$config['skin'];

$tpl->load_template('preview.css');

echo <<<HTML
<html><title>Предварительный просмотр</title>
<meta content="text/html; charset={$config['charset']}" http-equiv=Content-Type>
<style type="text/css">
{$tpl->copy_template}
</style>
<body>
<script type="text/javascript" src="{$config['http_home_url']}engine/classes/highslide/highslide.js"></script>
<script type="text/javascript">    
    hs.graphicsDir = '{$config['http_home_url']}engine/classes/highslide/graphics/';
    hs.outlineType = 'rounded-white';
    hs.numberOfImagesToPreload = 0;
    hs.showCredits = false;
</script>
HTML;

$tpl->clear();

echo <<<HTML
<script language="javascript" type="text/javascript">
<!--
function ShowBild(sPicURL) {
	window.open('{$config['http_home_url']}engine/modules/imagepreview.php?image='+sPicURL, '', 'resizable=1,HEIGHT=200,WIDTH=200, scrollbars=yes');
};

function ShowOrHide(d1) {
	  if (d1 != '') DoDiv(d1);
};

function DoDiv(id) {
	  var item = null;
	  if (document.getElementById) {
		item = document.getElementById(id);
	  } else if (document.all){
		item = document.all[id];
	  } else if (document.layers){
		item = document.layers[id];
	  }
	  if (!item) {
	  }
	  else if (item.style) {
		if (item.style.display == "none"){ item.style.display = ""; }
		else {item.style.display = "none"; }
	  }else{ item.visibility = "show"; }
};
//-->
</script>
HTML;

include_once ENGINE_DIR.'/classes/parse.class.php';

$parse = new ParseFilter(Array(), Array(), 1, 1);


if ($_POST['preview_mode'] == "static" ) {

	if( function_exists( "get_magic_quotes_gpc" ) && get_magic_quotes_gpc() ) $_POST['template'] = stripslashes( $_POST['template'] );  

	if ($config['allow_static_wysiwyg'] == "yes" OR $allow_br != '1'){

		if ( $config['allow_static_wysiwyg'] != "yes" )	$_POST['template'] = $parse->code_parse( $_POST['template'] );

		$template = $parse->BB_Parse($_POST['template']);

	} else {

		$template = $parse->BB_Parse($parse->code_parse($_POST['template']), false);

	}

	$template = addslashes( $template );

	$descr = trim(htmlspecialchars(stripslashes($_POST['description'])));

	if ($_GET['page'] == "rules" ) $descr = $lang['rules_edit'];

	if ($_POST['allow_template']) {


		if ($_POST['static_tpl'] == "" )
	    	$tpl->load_template('static.tpl');
		else
	    	$tpl->load_template($_POST['static_tpl'].'.tpl');

	    $tpl->set('{static}', stripslashes( $template ) );
	    $tpl->set('{description}', $descr);
	   	$tpl->set('{views}', "0");
		$tpl->set('{pages}', "");

	    $tpl->set('[print-link]',"<a href=#>");
	    $tpl->set('[/print-link]',"</a>");

		$tpl->set('{THEME}', $config['http_home_url'].'templates/'.$config['skin']);


		$tpl->copy_template = "<fieldset style=\"border-style:solid; border-width:1; border-color:black;\"><legend> <span style=\"font-size: 10px; font-family: Verdana\">{$lang['preview_static']}</span> </legend>".$tpl->copy_template."</fieldset>";
		$tpl->compile('template');
		echo $tpl->result['template'];

	} else {

		echo "<fieldset style=\"border-style:solid; border-width:1; border-color:black;\"><legend> <span style=\"font-size: 10px; font-family: Verdana\">{$lang['preview_static']}</span> </legend>".$template."</fieldset>";

	}


} else {

$title = stripslashes($parse->process($_POST['title']));

if ( $config['allow_admin_wysiwyg'] == "yes" ) $parse->allow_code = false;

$full_story = $parse->process($_POST['full_story']);
$short_story = $parse->process($_POST['short_story']);

if ($config['allow_admin_wysiwyg'] == "yes" OR $allow_br != '1'){

	$full_story = $parse->BB_Parse($full_story);
	$short_story = $parse->BB_Parse($short_story);

} else {

	$full_story = $parse->BB_Parse($full_story, false);
	$short_story = $parse->BB_Parse($short_story, false);

}

		if (!count($category)) { $my_cat = "---"; $my_cat_link = "---";} else {

		$my_cat = array (); $my_cat_link = array ();
	
			foreach ($category as $element) {
				if ($element) { $my_cat[] = $cat[$element];
								$my_cat_link[] = "<a href=\"#\">{$cat[$element]}</a>";
				}
			}
		$my_cat = stripslashes(implode (', ', $my_cat));
		$my_cat_link = stripslashes(implode (', ', $my_cat_link));
		}


    $tpl->load_template('shortstory.tpl');

    $tpl->set('{title}', $title);
    $tpl->set('{views}', 0);
    $tpl->set('{date}', langdate($config['timestamp_active'], time()));
    $tpl->set('[link]',"<a href=#>");
    $tpl->set('[/link]',"</a>");
    $tpl->set('{comments-num}', 0);
    $tpl->set('[full-link]', "<a href=#>");
    $tpl->set('[/full-link]', "</a>");
    $tpl->set('[com-link]', "<a href=#>");
    $tpl->set('[/com-link]', "</a>");
	$tpl->set('{rating}', "");
	$tpl->set('{approve}', "");
	$tpl->set('{author}', "--");
    $tpl->set('{category}', $my_cat);
    $tpl->set('{favorites}', '');
    $tpl->set('{link-category}', $my_cat_link);
    if($cat_icon[$category[0]] != ""){ $tpl->set('{category-icon}', $cat_icon[$category[0]]); }
    else{ $tpl->set('{category-icon}', "{THEME}/dleimages/no_icon.gif"); }
	$tpl->set_block("'\\[tags\\](.*?)\\[/tags\\]'si","");
	$tpl->set('{tags}',  "");

	$tpl->set('{edit-date}',  "");
	$tpl->set('{editor}',  "");
	$tpl->set('{edit-reason}',  "");
	$tpl->set_block("'\\[edit-date\\](.*?)\\[/edit-date\\]'si","");
	$tpl->set_block("'\\[edit-reason\\](.*?)\\[/edit-reason\\]'si","");

    $tpl->set('[mail]',"");
    $tpl->set('[/mail]',"");
    $tpl->set('{news-id}', "ID Unknown");
    $tpl->set('{php-self}', $PHP_SELF);

	$tpl->set_block("'\[hide\](.*?)\[/hide\]'i","\\1");
	if ( strpos( $tpl->copy_template, "[group=" ) !== false) {
		$tpl->copy_template = preg_replace( "#\\[group=5\\](.*?)\\[/group\\]#is","", $tpl->copy_template);
		$tpl->copy_template = preg_replace( "#\\[group=(.+?)\\](.*?)\\[/group\\]#is","\\2", $tpl->copy_template);
	}
	$tpl->copy_template = preg_replace( "#\\[category=(.+?)\\](.*?)\\[/category\\]#is","\\2", $tpl->copy_template);

	$tpl->set_block("'\\[edit\\].*?\\[/edit\\]'si","");

    $xfieldsaction = "templatereplacepreview";
    $xfieldsinput = $tpl->copy_template;
    include(ENGINE_DIR.'/inc/xfields.php');
    $tpl->copy_template = $xfieldsoutput;

    $tpl->set('{short-story}', stripslashes($short_story));
    $tpl->set('{full-story}', stripslashes($full_story));
	$tpl->set('{THEME}', $config['http_home_url'].'templates/'.$config['skin']);


$tpl->copy_template = "<fieldset style=\"border-style:solid; border-width:1; border-color:black;\"><legend> <span style=\"font-size: 10px; font-family: Verdana\">{$lang['preview_short']}</span> </legend>".$tpl->copy_template."</fieldset>";
$tpl->compile('shortstory');
echo $tpl->result['shortstory'];

    $tpl->load_template('fullstory.tpl');

	
# php 5.3
# 	if((strlen($full_story) < 13) and (!eregi("\{short-story\}", $tpl->copy_template)) ){ $full_story = $short_story; }

	if((strlen($full_story) < 13) and (!preg_match("/\{short-story\/i}", $tpl->copy_template)) ){ $full_story = $short_story; }

    $tpl->set('{title}', $title);
    $tpl->set('{views}', 0);
    $tpl->set('{date}', langdate($config['timestamp_active'], time()));
    $tpl->set('[link]',"<a href=#>");
    $tpl->set('[/link]',"</a>");
    $tpl->set('{comments-num}', 0);
    $tpl->set('[full-link]', "<a href=#>");
    $tpl->set('[/full-link]', "</a>");
    $tpl->set('[com-link]', "<a href=#>");
    $tpl->set('[/com-link]', "</a>");
	$tpl->set('{rating}', "");
	$tpl->set('{author}', "--");
    $tpl->set('{category}', $my_cat);
    $tpl->set('{link-category}', $my_cat_link);
    $tpl->set('{related-news}', "");

    if($cat_icon[$category[0]] != ""){ $tpl->set('{category-icon}', $cat_icon[$category[0]]); }
    else{ $tpl->set('{category-icon}', "{THEME}/dleimages/no_icon.gif"); }

    $tpl->set('{pages}', '');
    $tpl->set('{favorites}', '');
    $tpl->set('[mail]',"");
    $tpl->set('[/mail]',"");
    $tpl->set('{poll}', '');
    $tpl->set('{news-id}', "ID Unknown");
    $tpl->set('{php-self}', $PHP_SELF);

	$tpl->set_block("'\[hide\](.*?)\[/hide\]'i","\\1");
	if ( strpos( $tpl->copy_template, "[group=" ) !== false) {
		$tpl->copy_template = preg_replace( "#\\[group=5\\](.*?)\\[/group\\]#is","", $tpl->copy_template);
		$tpl->copy_template = preg_replace( "#\\[group=(.+?)\\](.*?)\\[/group\\]#is","\\2", $tpl->copy_template);
	}
	$tpl->copy_template = preg_replace( "#\\[category=(.+?)\\](.*?)\\[/category\\]#is","\\2", $tpl->copy_template);

	$tpl->set_block("'\\[edit\\].*?\\[/edit\\]'si","");
	$tpl->set_block("'{banner_(.*?)}'si","");
	$tpl->set('{edit-date}',  "");
	$tpl->set('{editor}',  "");
	$tpl->set('{edit-reason}',  "");
	$tpl->set_block("'\\[edit-date\\](.*?)\\[/edit-date\\]'si","");
	$tpl->set_block("'\\[edit-reason\\](.*?)\\[/edit-reason\\]'si","");
	$tpl->set_block("'\\[tags\\](.*?)\\[/tags\\]'si","");
	$tpl->set('{tags}',  "");

    $tpl->set('[print-link]',"<a href=#>");
    $tpl->set('[/print-link]',"</a>");

    $xfieldsaction = "templatereplacepreview";
    $xfieldsinput = $tpl->copy_template;
    include(ENGINE_DIR.'/inc/xfields.php');
    $tpl->copy_template = $xfieldsoutput;

    $tpl->set('{short-story}', stripslashes($short_story));
    $tpl->set('{full-story}', stripslashes($full_story));
	$tpl->set('{THEME}', $config['http_home_url'].'templates/'.$config['skin']);


$tpl->copy_template = "<fieldset style=\"border-style:solid; border-width:1; border-color:black;\"><legend> <span style=\"font-size: 10px; font-family: Verdana\">{$lang['preview_full']}</span> </legend>".$tpl->copy_template."</fieldset>";
$tpl->compile('fullstory');
echo $tpl->result['fullstory'];

}

?>
</body></html>