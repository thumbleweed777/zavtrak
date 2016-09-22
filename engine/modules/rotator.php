<?php

require_once ENGINE_DIR . '/inc/rotator/Rotator.class.php';

$config_r = array();
if (isset($show_title)) $config_r['show_title'] = $show_title;
if (isset($columns)) $config_r['columns'] = $columns;
if (isset($rows)) $config_r['rows'] = $rows;
if (isset($img_width)) $config_r['img_width'] = $img_width;
if (isset($img_height)) $config_r['img_height'] = $img_height;
if (isset($cats)) $config_r['cats'] = explode(',',$cats) ;
if (isset($title_pos)) $config_r['title_pos'] = $title_pos;


$rotator = new Rotator($config_r);

#var_dump($rotator); die;

echo $rotator;