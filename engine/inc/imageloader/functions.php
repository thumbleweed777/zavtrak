<?php


function cat_save()
{
    global $db;

    $id = intval($_GET['id']);

    $cat_name = $db->safesql($_POST['cat_name']);
    $cat_query = $db->safesql($_POST['cat_query']);
    $cat_limit = intval($_POST['cat_limit']);
    $p_from = $db->safesql($_POST['p_from']);
    $p_to = $db->safesql($_POST['p_to']);

    $allow_main = isset($_POST['allow_main']) && $_POST['allow_main'] == 'yes' ? 1 : 0;
    $get_image = isset($_POST['get_image']) && $_POST['get_image'] == 'yes' ? 1 : 0;

    $query = "UPDATE `" . PREFIX . "_" . getModuleName() . "_cat` SET `title` = '{$cat_name}', `query` = '{$cat_query}', `limit`= '{$cat_limit}', `words`= '' , `allow_main`= '{$allow_main}', `get_image`= '{$get_image}' , `p_from`= '{$p_from}', `p_to`= '{$p_to}' WHERE `id` = '{$id}'";

    $db->query($query);

    header("Location: {$_SERVER['PHP_SELF']}?mod=" . getModuleName() . "&action=list");

}

function cat_full_delete()
{
    global $db;

    $id = intval($_GET['id']);

    $cat = $db->super_query("SELECT * FROM " . PREFIX . "_" . getModuleName() . "_cat   WHERE id='{$id}'");
    $translit = totranslit($cat['title']);

    $db->query("DELETE  FROM " . PREFIX . "_" . getModuleName() . "_cat   WHERE id IN({$id})");
    $db->query("DELETE  FROM `" . PREFIX . "_category` WHERE `id` IN ('{$cat['cat_id']}')");
    $db->query("DELETE  FROM `" . PREFIX . "_" . getModuleName() . "` WHERE `cat_id` IN ('{$cat['id']}')");
    $db->query("DELETE  FROM `" . PREFIX . "_post` WHERE category = '{$cat['cat_id']}'");
    @deleteDir(UPLOAD_DIR . "/recipe/" . $translit);

    header("Content-type: text/html; charset=windows-1251");
    echo 'Категория удалена успешно. <a href="' . $_SERVER['PHP_SELF'] . '?mod=' . getModuleName() . '&action=list">Обновить страницу</a>';

}

function cat_delete()
{
    global $db;

    $id = intval($_GET['id']);
    $query = "DELETE  FROM " . PREFIX . "_" . getModuleName() . "_cat   WHERE id IN({$id})";
    $db->query($query);

    header("Location: {$_SERVER['PHP_SELF']}?mod=" . getModuleName() . "&action=list");

}

function cat_edit()
{
    global $db;

    $id = intval($_GET['id']);
    $query = "SELECT * FROM " . PREFIX . "_" . getModuleName() . "_cat   WHERE id='{$id}'";

    $cat = $db->super_query($query, false);

    $tpl = constructTemplate($path = TAGINATOR_ADMIN_TPL_DIR);

    header("content-type: text/html; charset=windows-1251");
    $html = $tpl->loadTemplate('catEdit', array('cat' => $cat));

    echo $html;
}


function cat_list()
{
    global $db;

    if ($_POST && trim($_POST['cat_name']) !== '' && intval($_POST['cat_limit']) > 0) {

        $cat_name = $db->safesql($_POST['cat_name']);
        $cat_limit = intval($_POST['cat_limit']);
        $cat_words = $db->safesql($_POST['cat_words']);
        $cat_query = $db->safesql($_POST['cat_query']);
        $translit = totranslit($cat_name);
        $allow_main = isset($_POST['allow_main']) && $_POST['allow_main'] == 'yes' ? 1 : 0;
        $get_image = isset($_POST['get_image']) && $_POST['get_image'] == 'yes' ? 1 : 0;
        $p_from = $db->safesql($_POST['p_from']);
        $p_to = $db->safesql($_POST['p_to']);
        umask(0);
        @mkdir(UPLOAD_DIR . "/recipe/" . $translit, 0777);

        $q_add = "INSERT INTO `" . PREFIX . "_category` (`id`, `parentid`, `posi`, `name`, `alt_name`, `icon`, `skin`, `descr`, `keywords`, `news_sort`, `news_msort`, `news_number`, `short_tpl`, `full_tpl`)
        VALUES
	    (null, 0, 1, '{$cat_name}', '{$translit}', '', '', '{$cat_name}', '{$cat_name}', '', '', 0, '', '')";


        $db->query($q_add);
        $last_id = $db->insert_id();

        $db->query("
        INSERT INTO `" . PREFIX . "_" . getModuleName() . "_cat` (`id`, `title`,  `query`, `limit`, `cat_id`, `real_title`, `words`, `allow_main`, `get_image`, `p_from`, `p_to`)
        VALUES 	(null, '{$cat_name}', '{$cat_query}', {$cat_limit}, {$last_id}, '{$cat_name}',  '{$cat_words}', '{$allow_main}', '{$get_image}', '{$p_from}', '{$p_to}')
        ");
    }


    $query = "SELECT c.*, COUNT(v.id) as v_count FROM " . PREFIX . "_" . getModuleName() . "_cat c LEFT JOIN " . PREFIX . "_" . getModuleName() . " v ON v.cat_id = c.id AND v.publish = 1  GROUP BY c.id ORDER BY c.id DESC LIMIT 500";

    $results = $db->super_query($query, true);

    if (!is_array($results))
        $results = array();

    $tpl = constructTemplate($path = TAGINATOR_ADMIN_TPL_DIR);

    $html = $tpl->loadTemplate('list', array('results' => $results));

    echo $html;
}


function taginator_config()
{

    global $db;

    $count_post = $db->super_query("SELECT COUNT(id) as count FROM dle_post");
    $count_img = $db->super_query("SELECT COUNT(id) as count FROM dle_imageloader");


    $tpl = constructTemplate();
    require_once PARSER_DIR . '/forms/ParserConfigForm.php';

    $values = parse_ini_file(DATA_DIR . '/' . getModuleName() . '.config.ini');

    # $values['static_template'] = file_get_contents(PARSER_TPL_DIR . '/full.php');
    $values['on_home_page'] = $values['on_home_page'] ? 1 : null;
    $form = new ParserConfigForm($values);

    if ($_POST) {
        $form->bind($_POST [$form->getName()], $_FILES[$form->getName()]);
        if ($form->isValid()) {
            $form->save();
            $saved = true;
        } else {
            $saved = false;
        }
    }
    $html = $tpl->loadTemplate('configForm', array( 'count_post'=> $count_post, 'count_img' => $count_img, 'form' => $form, 'saved' => isset($saved) ? $saved : null));

    echo $html;
}


function control_icons()
{

    $tpl = constructTemplate(PARSER_TPL_DIR);

    $html = $tpl->loadTemplate('controlIcons', array('var' => 'fuck'));

    echo $html;
}


function constructTemplate($path = PARSER_TPL_DIR)
{
    $tpl = new ctTemplate();
    $tpl->setBaseDir(PARSER_TPL_DIR);

    return $tpl;
}

function deleteDir($dirPath)
{
    if (!is_dir($dirPath)) {
        throw new InvalidArgumentException('$dirPath must be a directory');
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            self::deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}