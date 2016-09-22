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
define(ROTATOR_DIR, ENGINE_DIR . '/inc/rotator');
define(ROTATOR_TPL_DIR, ROTATOR_DIR . '/template');
define(UPLOAD_DIR, ENGINE_DIR . '/../uploads');


require_once ENGINE_DIR . '/vendor/symfony/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register('sfForm');
require_once ROTATOR_DIR . '/forms/sfWidgetFormSchemaFormatterAdmin.php';


require_once ROTATOR_DIR . '/functions.php';
require_once ENGINE_DIR . '/vendor/util/ctTemplate.class.php';



#$vidConfig = parse_ini_file(ENGINE_DIR . '/data/receptonator.config.ini');


if (!$action) {

    echoheader('', '');

    rotator_config();

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