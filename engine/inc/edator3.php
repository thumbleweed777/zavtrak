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

function getModuleName()
{
    return 'edator3';
}


define(CACHE_DIR, ENGINE_DIR . '/cache');
define(DATA_DIR, ENGINE_DIR . '/data');
define(PARSER_DIR, ENGINE_DIR . '/inc/'.getModuleName());
define(PARSER_TPL_DIR, PARSER_DIR . '/template');
define(UPLOAD_DIR, ENGINE_DIR . '/../uploads');
define(UTILS_DIR, ENGINE_DIR . '/vendor/util');

require_once ENGINE_DIR . '/vendor/symfony/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register('sfForm');
require_once PARSER_DIR . '/forms/sfWidgetFormSchemaFormatterAdmin.php';


require_once PARSER_DIR . '/functions.php';
require_once UTILS_DIR . '/ctTemplate.class.php';



#$vidConfig = parse_ini_file(ENGINE_DIR . '/data/cardonator.config.ini');


if (!$action) {

    echoheader('taginator.png', 'Тагинатор');

    control_icons();
    cat_list();

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

