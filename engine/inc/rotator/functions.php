<?php


function rotator_config()
{


    $tpl = constructTemplate();
    require_once ROTATOR_DIR . '/forms/RotatorConfigForm.php';

    $values = parse_ini_file(DATA_DIR . '/rotator.config.ini');

    $form = new RotatorConfigForm($values);

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



function constructTemplate($path = ROTATOR_TPL_DIR)
{
    $tpl = new ctTemplate();
    $tpl->setBaseDir(ROTATOR_TPL_DIR);

    return $tpl;
}

