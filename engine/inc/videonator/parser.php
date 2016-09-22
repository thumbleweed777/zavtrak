<?php

setlocale(LC_ALL, "ru_RU.CP1251");

define('DATALIFEENGINE', 'dle');
$dir = dirname(__FILE__);
define('ROOT_DIR', $dir . '/../../..');
define('VENDOR_DIR', $dir . '/vendor');
#var_dump(ROOT_DIR.'/engine/classes/mysql.class.php'); die;

include ROOT_DIR . '/engine/classes/mysql.class.php';
include ROOT_DIR . '/engine/data/dbconfig.php';
include ROOT_DIR . '/engine/vendor/util/ctTemplate.class.php';

set_include_path(VENDOR_DIR);

require_once $dir . '/vendor/Zend/Loader.php';

$videoConfig = parse_ini_file(ROOT_DIR . '/engine/data/videonator.config.ini');


if ($videoConfig['enable']) die ('Module turn off!');

Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_YouTube');

$q = "SELECT c.*, COUNT(v.id) as v_count FROM dle_videonator_cat c LEFT JOIN " . PREFIX . "_videonator v ON v.cat_id = c.id    GROUP BY c.id ORDER BY c.id DESC  ";
$categories = $db->super_query($q, true);

$yt = new Zend_Gdata_YouTube();
/* @var $query Zend_Gdata_YouTube_VideoQuery */


foreach ($categories as $cat) {

    if ($cat['v_count'] >= $cat['limit']) continue;

    $cat['query'] = iconv('cp1251', 'utf-8', $cat['query']);
    $query = $yt->newVideoQuery();

    #author filter
    if (preg_match('#\[user\]([^\[]+)\[/user\]#is', $cat['query'], $u)) {

        $cat['query'] = trim(preg_replace('#\[user\]([^\[]+)\[/user\]#is', '', $cat['query']));
        $username = $u[1];
        $query->author = $username;
    }


    #cat filter
    if (preg_match('#\[category=([^\[]+)\]#is', $cat['query'], $filter_cat)) {
        $cat['query'] = preg_replace('#\[category=([^\[]+)\]#is', '', $cat['query']);
        $query->category = $filter_cat[1];
    }

    $query->videoQuery = $cat['query'];

    $query->startIndex = 1;
    $query->maxResults = 50;
    if ($cat['v_count'] > 0) {
        $query->time = 'this_month';
    }
    $query->orderBy = 'relevance';

    $videoFeed = $yt->getVideoFeed($query);

    /* @var $videoEntry Zend_Gdata_YouTube_VideoEntry */
    /* @var $comment Zend_Gdata_Extension_Comments */
    /* @var $commentEntry Zend_Gdata_YouTube_CommentEntry */

    if ($cat['limit'] <= 50) {
        $maxResult = $cat['limit'];
    } else {
        $maxResult = 50;
    }

    $results = $videoFeed->getTotalResults()->getText();


    $videoNums = $cat['v_count'];

    for ($i = 1; $i <= $results; $i += 50) {

        #  $query = $yt->newVideoQuery();


        $query->startIndex = $i;

        if ($cat['v_count'] > 0) {
            $query->time = 'this_month';
        }


        $videoFeed = $yt->getVideoFeed($query);

        var_dump($i, $videoFeed->count());

        foreach ($videoFeed as $videoEntry) {


            if (video_exist($videoEntry->getVideoId())) continue;


            $res_add = add_video($videoEntry, $cat);
            if ($res_add)
                add_code($cat['id'], $videoEntry->getVideoId());

            if ($res_add) $videoNums++;

            if ($videoNums >= $cat['limit']) {

                break(2);
                break;
            }

            #  echo "\n\n\n";
        }

    }


}


function add_video(Zend_Gdata_YouTube_VideoEntry $video, $cat)
{
    global $db, $videoConfig, $yt;

    $title = $db->safesql(mb_convert_encoding($video->getVideoTitle(), 'cp1251', 'utf-8'));
    $description = $db->safesql(mb_convert_encoding($video->getVideoDescription(), 'cp1251', 'utf-8'));

    $description = str_ireplace('http://', '', $description);

    if (!$videoConfig['description']) $description = '';

    if (!stop_list($description) || !stop_list($title)) {
        echo $title . " - Stop key find! \n";
        return false;
    }

    if (!key_exist($cat['words'], $description) && !key_exist($cat['words'], $title)) {
        echo $title . " - Keywords dont find! \n";
        return false;
    }

    if ($videoConfig['before_title'])
        $title = trim($videoConfig['before_title'] . ' ' . lcfirstt($title) . ' ' . $videoConfig['after_title']);

    $author = 'admin';
    #  echo $author[0]->getText(); die;
    $alias = totranslit($title);

    $image_url = save_thumb($video, totranslit($cat['real_title']), $alias);

    # var_dump($image_url); die;

    if (!$image_url) return false;


    $short = strlen($description) > 70 ? substr($description, 0, 70) . '...' : $description;

    $code = $video->getVideoId();

    $tmp = new ctTemplate();
    $tmp->setBaseDir(ROOT_DIR . '/engine/inc/videonator/template');

    $short_story = $tmp->loadTemplate('short', array('short' => $short, 'image' => $image_url, 'title' => $title));
    $full_story = $tmp->loadTemplate('full', array('title' => $title, 'full' => $description, 'image' => $image_url, 'code' => $code));

    $full_story = tag_word_filter($full_story);


    $on_home_page = $videoConfig['on_home_page'] ? 1 : 0;

    $date = getRandDate();

    $q = "INSERT INTO `" . PREFIX . "_post` (`id`, `autor`, `date`, `short_story`, `full_story`, `xfields`, `title`, `descr`, `keywords`, `category`, `alt_name`, `comm_num`, `allow_comm`, `allow_main`, `allow_rate`, `approve`, `fixed`, `rating`, `allow_br`, `vote_num`, `news_read`, `votes`, `access`, `expires`, `symbol`, `flag`, `editdate`, `editor`, `reason`, `view_edit`, `tags`)
    VALUES
    	(null, '{$author}', '{$date}' , '{$short_story}', '{$full_story}', '', '{$title}', '{$title}', '{$title}', '{$cat['cat_id']}', '{$alias}', 1, 1, '{$on_home_page}', 1, 1, 0, 0, 1, 0, 0, 0, '', '0000-00-00', '', 1, '', '', '', 0, '');
    ";

    $db->query($q);
    $post_id = $db->insert_id();

    echo $title . "- OK \n";
    /* @var $commentEntry Zend_Gdata_YouTube_CommentEntry */
    $c_i = 0;
    if ($videoConfig['comments']) {

        try {
            $commentFeed = $yt->getVideoCommentFeed($video->getVideoId());
            foreach ($commentFeed as $commentEntry) {

                $authors = $commentEntry->getAuthor();
                $author = $authors[0]->name->text;


                # echo mb_convert_encoding($commentEntry->getText(), 'cp1251', 'utf-8') . "\n\n\n";
                $user_id = comment_create($post_id, $commentEntry->getContent()->getText(), $author, $date);

                $c_i++;
                if ($c_i >= $videoConfig['num_comments']) break;
            }
        } catch (Exception $e) {

        }
    }


    return true;
}

function video_exist($code)
{
    global $db;

    $ex = $db->super_query("SELECT * FROM `" . PREFIX . "_videonator` WHERE code = '{$code}' LIMIT 1");

    if ($ex) return true;
    else return false;
}


function save_thumb(Zend_Gdata_YouTube_VideoEntry $video, $cat_alias, $alias)
{
    $thumbs = $video->getVideoThumbnails();

    $thumb = $thumbs[0]['url'];

    $image = file_get_contents($thumb);

    if (!$image) return false;

    $result = file_put_contents(ROOT_DIR . '/uploads/' . $cat_alias . '/' . $alias . '.jpg', $image);

    return $result ? '/uploads/' . $cat_alias . '/' . $alias . '.jpg' : false;
}


function add_code($cat_id, $code)
{
    global $db;


    $q = "INSERT INTO `" . PREFIX . "_videonator`
        (`id`, `cat_id`, `code`)
     VALUES
    	(null,  {$cat_id}, '{$code}');
    ";
    $db->query($q);

}


function random_pic()
{
    $files = glob(ROOT_DIR . '/uploads/fotos/avatars/*.*');
    #   var_dump($files);
    $file = array_rand($files);

    preg_match('#avatars/.+#i', $files[$file], $match);
    # var_dump($files); die;
    return $match[0];
}


function comment_create($post_id, $text, $login, $date)
{
    global $db;

    $text = $db->safesql(iconv( 'utf-8', 'cp1251',  $text));
    $login = $db->safesql(iconv( 'utf-8', 'cp1251',  $login));

    if (!stop_list($text)) return false;

    $exist = $db->super_query("SELECT user_id  FROM `" . PREFIX . "_users` WHERE name = '{$login}' LIMIT 1");


    if ($exist) $user_id = $exist['user_id'];

    $avatar = random_pic();

    if (!$exist) {

        $q = "INSERT INTO `" . PREFIX . "_users` (`email`, `password`, `name`, `user_id`, `news_num`, `comm_num`, `user_group`, `lastdate`, `reg_date`, `banned`, `allow_mail`, `info`, `signature`, `foto`, `fullname`, `land`, `country`, `city`, `icq`, `favorites`, `pm_all`, `pm_unread`, `time_limit`, `xfields`, `allowed_ip`, `hash`, `useragent`, `logged_ip`, `logged_proxy`, `restricted`, `restricted_days`, `restricted_date`, `location`)
    VALUES
    	( '{$login}@yandex.ru', MD5('{$login}'), '{$login}' , null, 0, 0, 0, NOW(), NOW(), '', 1, '', '', '{$avatar}', '', '', '', '', '', '', 0, 0, '', '', '', '', 'Opera/9.64 (Windows NT 5.1; U; ru) Presto/2.1.1', '88.135.115.51', '0.0.0.0', 0, 0, '', '');
    ";
        $db->query($q);
        $user_id = $db->insert_id();
    }

    $date = date("Y-m-d H:i:s", strtotime("+ 2400 minutes", strtotime($date)));


    $q = "INSERT INTO `" . PREFIX . "_comments` (`id`, `post_id`, `user_id`, `date`, `autor`, `email`, `text`, `ip`, `is_register`, `approve`)
    VALUES
    	(null, $post_id, $user_id, '{$date}', '$login', '', '{$text}', '', 1, 1);
    ";

    $db->query($q);


    return $user_id;
}

function totranslit($var, $lower = true, $punkt = true)
{
    $NpjLettersFrom = "àáâãäåçèêëìíîïðñòóôöû³";
    $NpjLettersTo = "abvgdeziklmnoprstufcyi";
    $NpjBiLetters = array("é" => "j", "¸" => "yo", "æ" => "zh", "õ" => "x", "÷" => "ch", "ø" => "sh", "ù" => "shh", "ý" => "ye", "þ" => "yu", "ÿ" => "ya", "ú" => "", "ü" => "", "¿" => "yi", "º" => "ye");

    $NpjCaps = "ÀÁÂÃÄÅ¨ÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÛÝÞß¯ª²";
    $NpjSmall = "àáâãäå¸æçèéêëìíîïðñòóôõö÷øùüúûýþÿ¿º³";

    $var = str_replace(".php", "", $var);
    $var = trim(strip_tags($var));
    $var = preg_replace("/\s+/ms", "-", $var);
    $var = strtr($var, $NpjCaps, $NpjSmall);
    $var = strtr($var, $NpjLettersFrom, $NpjLettersTo);
    $var = strtr($var, $NpjBiLetters);

    if ($punkt) $var = preg_replace("/[^a-z0-9\_\-.]+/mi", "", $var);
    else $var = preg_replace("/[^a-z0-9\_\-]+/mi", "", $var);

    $var = preg_replace('#[\-]+#i', '-', $var);

    if ($lower) $var = strtolower($var);

    if (strlen($var) > 50) {

        $var = substr($var, 0, 50);

        if (($temp_max = strrpos($var, '-'))) $var = substr($var, 0, $temp_max);

    }

    return $var;
}

function tag_word_filter($string)
{

    preg_match_all('#\{%(.+)%\}#Us', $string, $match);

    foreach ($match[1] as $key => $words) {
        $w_array = explode('|', $words);
        $w_count = count($w_array) - 1;
        $string = str_replace($match[0][$key], $w_array[rand(0, $w_count)], $string);
    }

    return $string;
}


function stop_list($request)
{
    global $videoConfig;

    $stops = explode(',', $videoConfig['stop_list']);

    if (count($stops) == 1 && trim($stops[0]) == '') return true;

    foreach ($stops as $stop) {
        $stop = trim(preg_quote($stop, '#'));
        if (preg_match("#{$stop}#i", $request)) {
            return false;
        }
    }
    return true;
}

function key_exist($words, $request)
{

    $keys = explode(',', $words);

    if (count($keys) == 1 && trim($keys[0]) == '') return true;

    foreach ($keys as $key) {
        $key = trim(preg_quote($key, '#'));
        if (preg_match("#{$key}#i", $request)) {
            return true;
        }
    }
    return false;
}

function lcfirstt($str)
{
    $str = trim($str);
    $str[0] = strtolower($str[0]);
    return (string)$str;
}

function getRandDate()
{
    global $videoConfig;

    if (!$videoConfig['publish_date']) $videoConfig['publish_date'] = 1;

    $rand = rand(1, $videoConfig['publish_date']);

    return date("Y-m-d H:i:s", strtotime("+ {$rand} minutes", strtotime(date("Y-m-d H:i:s"))));
}