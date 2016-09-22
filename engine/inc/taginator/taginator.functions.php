<?php
/**
 * Module for DLE 7.5 "Taginator", main functions for backend part
 * author: Moroz A.N.
 * skype: str01tel
 * email: netstroix@gmail.com
 */
function clear_all()
{
    global $db;

    $db->query("DELETE  FROM " . PREFIX . "_taginator");
    header('Location: ' . $_SERVER['PHP_SELF'] . '?mod=taginator&action=list', true, 301);
}

function taginator_about()
{
    $tpl = constructTemplate($path = TAGINATOR_ADMIN_TPL_DIR);
    $html = $tpl->loadTemplate('about');

    echo $html;

}


function taginator_add()
{

    $form = new TaginatorAddForm();


    if ($_POST) {
        $form->bind($_POST [$form->getName()], getFiles($form->getName()));
        if ($form->isValid()) {

            $form->save();

        }
    }
    $tpl = constructTemplate($path = TAGINATOR_ADMIN_TPL_DIR);
    $html = $tpl->loadTemplate('tagAdd', array('form' => $form));

    echo $html;

}


function tag_delete($ids)
{
    if (!is_array($ids)) {
        $ids = array(intval($ids));
    }

    global $db;

    foreach ($ids as $id) {
        $id = intval($id);
        $st = $db->super_query("SELECT static_id  FROM " . PREFIX . "_taginator WHERE id='$id'");

        $db->query("DELETE  FROM " . PREFIX . "_taginator WHERE id='$id'");
        $db->query("DELETE  FROM " . PREFIX . "_static WHERE id='{$st['static_id']}'");

    }

    header('Location: ' . $_SERVER['PHP_SELF'] . '?mod=taginator&action=list', true, 301);

}


function tag_edit()
{
    global $db;
    $id = intval($_GET['id']);

    $tag = $db->super_query("SELECT * FROM " . PREFIX . "_taginator WHERE id={$id}");

    $form = new TaginatorForm($tag);

    if ($_POST) {
        $form->bind($_POST [$form->getName()], getFiles($form->getName()));
        if ($form->isValid()) {
            $form->save();
            $saved = true;
        } else {
            $saved = false;
        }
    }

    $tpl = constructTemplate($path = TAGINATOR_ADMIN_TPL_DIR);

    $html = $tpl->loadTemplate('tagEdit', array('form' => $form, 'tag' => $tag, 'saved' => $saved));

    echo $html;
}


function tag_list()
{
    global $tagConfig;

    $query = "SELECT * FROM " . PREFIX . "_taginator ORDER BY id DESC";

    $pager = new Pagination($query, $tagConfig['admin_list_limit'], 15, "mod=taginator&action=list");
    $pager->setDebug(false);

    $results = $pager->paginate();

    if (!is_array($results))
        $results = array();

    $tpl = constructTemplate($path = TAGINATOR_ADMIN_TPL_DIR);

    $html = $tpl->loadTemplate('tagList', array('results' => $results, 'pager' => $pager));

    echo $html;
}

function control_icons()
{

    $tpl = constructTemplate($path = TAGINATOR_ADMIN_TPL_DIR);

    $html = $tpl->loadTemplate('controlIcons', array('var' => 'fuck'));

    echo $html;
}

function taginator_config()
{
    $tpl = constructTemplate($path = TAGINATOR_ADMIN_TPL_DIR);

    $values = parse_ini_file(DATA_DIR . '/taginator.config.ini');

    $values['static_template'] = file_get_contents(ENGINE_DIR.'/modules/taginator/template/static.php');

    $form = new TaginatorConfigForm($values);

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

function constructTemplate($path = TAGINATOR_ADMIN_TPL_DIR)
{
    $tpl = new ctTemplate();
    $tpl->setBaseDir(TAGINATOR_ADMIN_TPL_DIR);

    return $tpl;
}


function getFiles($key = null)
{
    $fixedFileArray = convertFileInformation($_FILES);

    return null === $key ? $fixedFileArray : (isset($fixedFileArray[$key]) ? $fixedFileArray[$key] : array());
}

/**
 * Converts uploaded file array to a format following the $_GET and $POST naming convention.
 *
 * It's safe to pass an already converted array, in which case this method just returns the original array unmodified.
 *
 * @param  array $taintedFiles An array representing uploaded file information
 *
 * @return array An array of re-ordered uploaded file information
 */
function convertFileInformation(array $taintedFiles)
{
    $files = array();
    foreach ($taintedFiles as $key => $data)
    {
        $files[$key] = fixPhpFilesArray($data);
    }

    return $files;
}

function fixPhpFilesArray($data)
{
    $fileKeys = array('error', 'name', 'size', 'tmp_name', 'type');
    $keys = array_keys($data);
    sort($keys);

    if ($fileKeys != $keys || !isset($data['name']) || !is_array($data['name'])) {
        return $data;
    }

    $files = $data;
    foreach ($fileKeys as $k)
    {
        unset($files[$k]);
    }
    foreach (array_keys($data['name']) as $key)
    {
        $files[$key] = fixPhpFilesArray(array(
                                             'error' => $data['error'][$key],
                                             'name' => $data['name'][$key],
                                             'type' => $data['type'][$key],
                                             'tmp_name' => $data['tmp_name'][$key],
                                             'size' => $data['size'][$key],
                                        ));
    }

    return $files;
}


function redirect($path)
{


}