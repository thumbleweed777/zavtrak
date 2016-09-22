<?php


if (!defined('DATALIFEENGINE')) {
    die("Hacking attempt!");
}
define('VENDOR_DIR', ROOT_DIR . '/engine/vendor');
define('MODULE_DIR', ROOT_DIR . '/engine/inc/komponator');
define('FORM_TPL_DIR', MODULE_DIR . '/template');
define('IMG_UPLOAD_DIR', ROOT_DIR . '/uploads/photos');

require_once VENDOR_DIR . '/util/ctTemplate.class.php';


global $tpl, $config, $db, $cat_info;
#var_dump($cat_info); die;

$ctTemplate = new ctTemplate();
$ctTemplate->setBaseDir(FORM_TPL_DIR);


echoheader("addnews", $lang['addnews']);

$categories = $db->super_query("SELECT c.*,  (SELECT  COUNT(*) FROM " . PREFIX . "_post p WHERE p.category = c.id ) as post_count FROM " . PREFIX . "_category c ", true);


$html = $ctTemplate->loadTemplate('form', array('cats' => $categories));

echo $html;

echofooter("addnews", $lang['addnews']);

