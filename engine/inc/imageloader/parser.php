<?php

setlocale(LC_ALL, "ru_RU.CP1251");
header("Content-Type: text/html; charset=windows-1251");
ini_set('max_execution_time', 0);

function getModuleName()
{
    return 'imageloader';
}

@ini_set('max_execution_time', 0);


ob_start();
echo str_pad('Loading... ', 4096) . "<br />\n";


define('DATALIFEENGINE', 'dle');
$dir = dirname(__FILE__);
define('ROOT_DIR', $dir . '/../../..');
define('VENDOR_DIR', ROOT_DIR . '/engine/vendor');
define('UPLOAD_DIR', ROOT_DIR . '/uploads');
define('MODULE_DIR', ROOT_DIR . '/engine/inc/' . getModuleName());

include ROOT_DIR . '/engine/classes/mysql.class.php';
include ROOT_DIR . '/engine/data/dbconfig.php';
include VENDOR_DIR . '/util/ctTemplate.class.php';


set_include_path(VENDOR_DIR);

require_once VENDOR_DIR . '/Zend/Loader.php';

require_once(VENDOR_DIR . '/simplehtmldom/simple_html_dom.php');

$config = parse_ini_file(ROOT_DIR . '/engine/data/' . getModuleName() . '.config.ini');

if ($config['enable']) die ('Module turn off!');

Zend_Loader::loadClass('Zend_Http_Client');

$q = "SELECT p.*, c.alt_name as cat_alt FROM " . PREFIX . "_post p LEFT JOIN dle_category c ON c.id = p.category  WHERE NOT EXISTS (SELECT null FROM  " . PREFIX . "_" . getModuleName() . " i WHERE i.post_id = p.id)    ORDER BY RAND(p.id) DESC LIMIT 30 ";
$posts = $db->super_query($q, true) ;


foreach ($posts as $post) {
    if (!$imgSrc = getGoogleImg($post['title'], $post['cat_alt'], $post['alt_name'])) {
        continue;
    }
    insertImg($imgSrc, $post);
    createIsLoad($post);
    echo $post['id'] . ' - ' . $post['title'] . "<br>";
}

function insertImg($imgSrc, $post)
{
    global $config, $db;
    $title = str_ireplace(array('\'', '"'), '', $post['title']);
    $imgFull = "<img src='{$imgSrc}' width='{$config['width']}' title='{$title}' alt='{$title}'>";
    $imgShort = "<img src='{$imgSrc}' width='150' title='{$title}' alt='{$title}'>";

    if (preg_match('#thisIsSparta#', $post['short_story'])) {
        $short = str_ireplace('<!--thisIsSparta-->', $imgShort, $post['short_story']);
    } else {
        $short = "<div style='width: 160px; float: left;'>{$imgShort}</div>";
        $short .= $post['short_story'];
    }
    if (preg_match('#thisIsSparta#', $post['full_story'])) {
        $full = str_ireplace('<!--thisIsSparta-->', $imgFull, $post['full_story']);
    } else {
        switch ($config['position']) {
            case('top'):
                $full = "<div style='text-align: center;'>{$imgFull}</div>";
                $full .= $post['full_story'];
                break;
            case('bottom'):
                $full = $post['full_story'];
                $full .= "<div style='text-align: center;'>{$imgFull}</div>";
                break;
            case('left'):
                $full = "<div style='margin: 5px; float: left;'>{$imgFull}</div>";
                $full .= $post['full_story'];
                break;
            case('right'):
                $full = "<div style='margin: 5px; float: right;'>{$imgFull}</div>";
                $full .= $post['full_story'];
                break;
        }
    }

    $short = mysql_real_escape_string($short);
    $full = mysql_real_escape_string($full);
    $db->query("UPDATE `dle_post` SET `short_story` = '{$short}', `full_story` = '{$full}' WHERE `id` = '{$post['id']}' ");
}

function save_thumb($src, $cat_alias, $alias)
{
    $thumb = $src;

    try {
        $client = new Zend_Http_Client();
        $client->setUri($thumb);

        $client->setHeaders('User-Agent', ' Mozilla/5.0 (iPad; U; CPU OS 3_2_1 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Mobile/7B405');
        $image = $client->request('GET');

    } catch (Exception $e) {
        return false;
    }

    $rnd = rand(1, 9999);

    if (!is_writable(ROOT_DIR . '/uploads/recipe/' . $cat_alias)) {
        mkdir(ROOT_DIR . '/uploads/recipe/' . $cat_alias, 0777);
    }
    $fname = ROOT_DIR . '/uploads/recipe/' . $cat_alias . '/' . $alias . '_' . $rnd . '.jpg';
    $result = file_put_contents($fname, $image->getBody());
    if (!getimagesize($fname)) {
        return false;
    }
    return $result ? '/uploads/recipe/' . $cat_alias . '/' . $alias . '_' . $rnd . '.jpg' : false;
}


function getIp()
{
    ob_start();
    $ips = array();
    $ifconfig = system("ifconfig");
    echo $ifconfig;
    $ifconfig = ob_get_contents();
    ob_end_clean();
    $ifconfig = explode(chr(10), $ifconfig);
    for ($i = 0; $i < count($ifconfig); $i++) {
        $t = explode(" ", $ifconfig[$i]);
        if ($t[0] == "\tinet" && $t[1] !== '127.0.0.1' && preg_match('#188\.190#', $t[1])) {
            array_push($ips, $t[1]);
        }
    }


    # return '192.168.2.105';
    return $ips[rand(0, count($ips) - 1)];
}

function getGoogleImg($k, $cat_alias, $alias)
{
    $k = mb_convert_encoding($k, 'UTF-8', 'CP1251');
    $k = str_ireplace(array('"', "'", '&quot;'), '', $k);
     #tbs=isz:m
    $http = new Zend_Http_Client('http://images.google.com/images?&as_q=' . urlencode($k), array('adapter' => 'Zend_Http_Client_Adapter_Curl'));
    $http->getAdapter()->setCurlOption(CURLOPT_INTERFACE, getIp());
    $response = $http->request('GET');
    $html = str_get_html($response->getBody());
    #var_dump($response); 
    foreach ($html->find('a') as $element) {

        $result = $element->href;
        if (preg_match('#(?:http://)?(http(s?)://([^\s]*)\.(jpg|png))#', $result, $imagelink)) {

            $img = save_thumb($imagelink[1], $cat_alias, $alias);
            if ($img) {
                return $img;
            }
        }
    }
    return false;
}

function createIsLoad($post)
{
    global $db;

    $db->query("INSERT INTO `dle_imageloader` (`id`, `is_load`, `post_id`) VALUES (NULL, 1, {$post['id']});");
}