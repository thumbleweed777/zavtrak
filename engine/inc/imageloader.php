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
    return 'imageloader';
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

    taginator_config();

    echofooter();

} elseif ($action == "config") {

    echoheader('taginator.png', 'Тагинатор');

    control_icons();

    taginator_config();

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

