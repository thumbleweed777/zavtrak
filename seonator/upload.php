<?php

$sign = '300$';

$images = (isset($_POST['images']) ? json_decode($_POST['images'], true) : array());
$dir = __DIR__ . '/../' . $_POST['dir'];
$signature = $_POST['signature'];
$title= $_POST['title'];
//check sign
if (md5(implode($sign, $images)) !== $signature) {
    echo json_encode(array('result' => 'error', 'message' => 'Wrong signature'));
    die;
}

if (!is_dir($dir)) {    
    mkdir($dir);
}

if (!is_writable($dir)) {
    echo json_encode(array('result' => 'error', 'message' => sprintf('Dir %s isn\'t writable', $dir)));
    die;
}
$urls = array();
foreach ($images as $k => $image) {
    $name = pathinfo($image, PATHINFO_BASENAME);
     $ext = pathinfo($image, PATHINFO_EXTENSION);
    $content = file_get_contents($image);
    
    file_put_contents($dir . '/' . $title."-$k.".$ext, $content);

    $urls[] = $_POST['dir'] . '/' . $title."-$k.".$ext;
}
echo json_encode(array('result' => 'ok', 'images' => $urls));

