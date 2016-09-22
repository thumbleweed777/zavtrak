<?php


function cat_save()
{
    global $db;

    $id = intval($_GET['id']);

    $cat_name = $db->safesql($_POST['cat_name']);
    $cat_query = $db->safesql($_POST['cat_query']);
    $cat_limit = intval($_POST['cat_limit']);
    $cat_words = $db->safesql($_POST['cat_words']);
    $cat_cont_path = $db->safesql($_POST['cont_path']);

    $query = "UPDATE `" . PREFIX . "_receptonator_cat` SET `title` = '{$cat_name}', `query` = '{$cat_query}', `limit`= '{$cat_limit}', `words`= '{$cat_words}' , `cont_path`= '{$cat_cont_path}' WHERE `id` = '{$id}'";

    $db->query($query);

    header("Location: {$_SERVER['PHP_SELF']}?mod=receptonator&action=list");

}

function cat_full_delete()
{
    global $db;

    $id = intval($_GET['id']);

    $cat = $db->super_query("SELECT * FROM " . PREFIX . "_receptonator_cat   WHERE id='{$id}'");
    $translit = totranslit($cat['title']);

    $db->query("DELETE  FROM " . PREFIX . "_receptonator_cat   WHERE id IN({$id})");
    $db->query("DELETE  FROM `" . PREFIX . "_category` WHERE `id` IN ('{$cat['cat_id']}')");
    $db->query("DELETE  FROM `" . PREFIX . "_receptonator` WHERE `cat_id` IN ('{$cat['id']}')");
    $db->query("DELETE  FROM `" . PREFIX . "_post` WHERE category = '{$cat['cat_id']}'");
    @deleteDir(UPLOAD_DIR . "/" . $translit);

    header("Content-type: text/html; charset=windows-1251");
    echo 'Категория удалена успешно. <a href="'.$_SERVER['PHP_SELF'].'?mod=receptonator&action=list">Обновить страницу</a>';

}

function cat_delete()
{
    global $db;

    $id = intval($_GET['id']);

    $query = "DELETE  FROM " . PREFIX . "_receptonator_cat   WHERE id IN({$id})";

    $db->query($query);

    header("Location: {$_SERVER['PHP_SELF']}?mod=receptonator&action=list");

}

function cat_edit()
{
    global $db;

    $id = intval($_GET['id']);

    $query = "SELECT * FROM " . PREFIX . "_receptonator_cat   WHERE id='{$id}'";

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
        $cat_cont_path = $db->safesql($_POST['cont_path']);

        umask(0);
        @mkdir(UPLOAD_DIR . "/" . $translit, 0777);

        $q_add = "INSERT INTO `" . PREFIX . "_category` (`id`, `parentid`, `posi`, `name`, `alt_name`, `icon`, `skin`, `descr`, `keywords`, `news_sort`, `news_msort`, `news_number`, `short_tpl`, `full_tpl`)
        VALUES
	    (null, 0, 1, '{$cat_name}', '{$translit}', '', '', '{$cat_name}', '{$cat_name}', '', '', 0, '', '')";


        $db->query($q_add);
        $last_id = $db->insert_id();

        $db->query("
        INSERT INTO `" . PREFIX . "_receptonator_cat` (`id`, `title`,  `query`, `limit`, `cat_id`, `real_title`, `words`, `cont_path`)
        VALUES 	(null, '{$cat_name}', '{$cat_query}', {$cat_limit}, {$last_id}, '{$cat_name}',  '{$cat_words}', '{$cat_cont_path}')
        ");
    }


    $query = "SELECT c.*, COUNT(v.id) as v_count FROM " . PREFIX . "_receptonator_cat c LEFT JOIN " . PREFIX . "_receptonator v ON v.cat_id = c.id AND v.publish = 1  GROUP BY c.id ORDER BY c.id DESC LIMIT 500";

    $results = $db->super_query($query, true);

    if (!is_array($results))
        $results = array();

    $tpl = constructTemplate($path = TAGINATOR_ADMIN_TPL_DIR);

    $html = $tpl->loadTemplate('list', array('results' => $results));

    echo $html;
}


function get_num_content($path)
{

    $files = glob('{' . UPLOAD_DIR . '/content/' . $path . '/*.html' . ',' . UPLOAD_DIR . '/content/' . $path . '/*.txt' . '}', GLOB_BRACE);


    $c = count($files);

    return ($files) ? $c : 0;
}


function taginator_config()
{


    $tpl = constructTemplate();
    require_once VIDEONATOR_DIR . '/forms/VideonatorConfigForm.php';

    $values = parse_ini_file(DATA_DIR . '/receptonator.config.ini');

    $values['static_template'] = file_get_contents(VIDEONATOR_TPL_DIR . '/full.php');

    $form = new VideonatorConfigForm($values);

    if ($_POST) {
        $form->bind($_POST [$form->getName()], $_FILES[$form->getName()]);
        if ($form->isValid()) {
            $form->save();
            $saved = true;
        } else {
            $saved = false;
        }
    }
    $html = $tpl->loadTemplate('configForm', array('form' => $form, 'saved' => isset($saved) ? $saved : null));

    echo $html;
}


function control_icons()
{

    $tpl = constructTemplate(VIDEONATOR_TPL_DIR);

    $html = $tpl->loadTemplate('controlIcons', array('var' => 'fuck'));

    echo $html;
}


function constructTemplate($path = VIDEONATOR_TPL_DIR)
{
    $tpl = new ctTemplate();
    $tpl->setBaseDir(VIDEONATOR_TPL_DIR);

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