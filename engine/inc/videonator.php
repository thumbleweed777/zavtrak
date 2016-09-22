<?php

/**
 * Module for DLE 7.5 "Taginator", backend part
 * author: Moroz A.N.
 * skype: str01tel
 * email: netstroix@gmail.com
 */


if (!defined('DATALIFEENGINE')) {
    die("Hacking attempt!");
}

if (!$user_group[$member_id['user_group']]['moderation']) {
    msg("error", $lang['addnews_denied'], $lang['addnews_perm']);
}


define(CACHE_DIR, ENGINE_DIR . '/cache');
define(DATA_DIR, ENGINE_DIR . '/data');
define(VIDEONATOR_DIR, ENGINE_DIR . '/inc/videonator');
define(VIDEONATOR_TPL_DIR, VIDEONATOR_DIR . '/template');
define(UPLOAD_DIR, ENGINE_DIR . '/../uploads');


require_once VIDEONATOR_DIR . '/vendor/symfony/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register('sfForm');
require_once VIDEONATOR_DIR . '/forms/sfWidgetFormSchemaFormatterAdmin.php';


require_once VIDEONATOR_DIR . '/functions.php';
include ENGINE_DIR .'/vendor/util/ctTemplate.class.php';



$vidConfig = parse_ini_file(ENGINE_DIR . '/data/videonator.config.ini');


if (!$action) {

    echoheader('taginator.png', 'Тагинатор');

    control_icons();

    echofooter();

} elseif ($action == "config") {

    echoheader('taginator.png', 'Тагинатор');

    control_icons();

    taginator_config();

    echofooter();

} elseif ($action == "list") {

    echoheader('taginator.png', 'Тагинатор');

    control_icons();
    cat_list();

    echofooter();

} elseif ($action == "cat_edit") {

    cat_edit();

}  elseif ($action == "cat_delete") {

    cat_delete();

}elseif ($action == "cat_save") {

    cat_save();

}elseif ($action == "delete") {

    tag_delete($_GET['id']);

} elseif ($action == "batch") {

    switch ($_POST['batch_action']) {
        case('delete'):
            tag_delete($_POST['ids']);
            break;
    }


} elseif ($action == "clear_all") {
    clear_all();

} elseif ($action == "about") {

    echoheader('taginator.png', 'Тагинатор');

    control_icons();

    taginator_about();

    echofooter();

}elseif ($action == "add") {

    echoheader('taginator.png', 'Тагинатор');

    control_icons();

    taginator_add();

    echofooter();

}elseif ($action == "cat_full_delete") {

    cat_full_delete();
}


function autoload($paths)
{

    sfCoreAutoload::register('sfFinder');

    $files = sfFinder::type('file')
            ->prune('template')
            ->name('*.php')
            ->in($paths);
    foreach ($files as $file) {
        require_once $file;
    }

}