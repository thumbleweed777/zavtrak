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
define(TAGINATOR_ADMIN_DIR, ENGINE_DIR . '/inc/taginator');
define(TAGINATOR_ADMIN_TPL_DIR, TAGINATOR_ADMIN_DIR . '/template');
define(UPLOAD_DIR, ENGINE_DIR . '/../uploads');

require_once ENGINE_DIR . '/vendor/symfony/autoload/sfCoreAutoload.class.php';


autoload(
    array(
         TAGINATOR_ADMIN_DIR,
         ENGINE_DIR . '/vendor/util',
    )
);
$tagConfig = parse_ini_file(ENGINE_DIR . '/data/taginator.config.ini');


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
    tag_list();

    echofooter();

} elseif ($action == "edit") {

    echoheader('taginator.png', 'Тагинатор');

    control_icons();

    tag_edit();

    echofooter();

} elseif ($action == "delete") {

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