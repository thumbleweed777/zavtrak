<?php

setlocale(LC_ALL, "ru_RU.CP1251");
header("Content-Type: text/html; charset=cp1251");


define('DATALIFEENGINE', 'dle');
$dir = dirname(__FILE__);
define('ROOT_DIR', $dir . '/../../..');
define('VENDOR_DIR', ROOT_DIR . '/engine/vendor');
define('UPLOAD_DIR', ROOT_DIR . '/uploads');
define('MODULE_DIR', ROOT_DIR . '/engine/inc/receptonator');

#var_dump(ROOT_DIR.'/engine/classes/mysql.class.php'); die;

include ROOT_DIR . '/engine/classes/mysql.class.php';
include ROOT_DIR . '/engine/data/dbconfig.php';
include ROOT_DIR . '/engine/vendor/util/ctTemplate.class.php';

set_include_path(VENDOR_DIR);

require_once MODULE_DIR . '/rewrite.php';

require_once VENDOR_DIR . '/Zend/Loader.php';

require_once(VENDOR_DIR . '/simplehtmldom/simple_html_dom.php');


$config = parse_ini_file(ROOT_DIR . '/engine/data/receptonator.config.ini');



#$rew_text = "Грецкий орех 130 г Изюм темный 130 г  Морковь 300 г Шоколад 220 г  Яйцо куриное 4 шт. Сахар-песок 100 г  Мука пшеничная 200 г Разрыхлитель теста 10 г  Корица молотая 1 ч.л. Имбирь молотый 1 ч.л.  Какао-порошок 30 г Подсолнечное масло рафинированное 120 мл  Сливочное масло 5 г Сухари панировочные 2 ст.л.  Маскарпоне 500 г Сахарная пудра 30 г  Бренди 30 мл Миндаль 40 г";

#$rewrite = new Rewrite();

#var_dump($rewrite->rewrite($rew_text)); die;


#var_dump(get_ip_array()); die;


if ($config['enable']) die ('Module turn off!');

Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_YouTube');

$q = "SELECT c.*, COUNT(v.id) as v_count FROM dle_receptonator_cat c LEFT JOIN " . PREFIX . "_receptonator v ON v.cat_id = c.id AND v.publish = 1     GROUP BY c.id ORDER BY c.id DESC  ";
$categories = $db->super_query($q, true);


$api_key = "AIzaSyCPOgDj5jRt1IBfthH-5v2loHoGDzdPOaw";

echo '<pre>';


foreach ($categories as $cat) {


    if ($cat['v_count'] >= $cat['limit']) continue;


    $cat_count = $cat['v_count'];


    $q = iconv('cp1251', 'utf-8', $cat['query']);
    $arg_arr = array(
        'q' => $q,
        'count' => $cat['limit'],
        'as_occt' => 128,
        'skin' => 1
    );


    $html = file_get_html('http://povaru.com/search?' . http_build_query($arg_arr));

    $links = $html->find('a[href^=http://povaru.com/viewrecipe/]');

    $html->__destruct();
    unset($html);

    $recipes = array();
    foreach ($links as $link) {

        $recipes[] = $link->href;
    }


    $c_p = count($recipes);

    echo "Всего найдено {$c_p} рецептов \n";

    #var_dump($placess); die;

    $rewrite = new Rewrite();

    foreach ($recipes as $recipe) {

        preg_match('#\d+$#', $recipe, $cid);
        $cid = $cid[0];

        if (place_exist($cid, $cat['id'])) {
            echo "{$recipe} - exist \n";
            continue;
        }

        if ($cat_count + 1 > $cat['limit']) {
            continue 2;
        }
    #   $recipe = 'http://povaru.com/viewrecipe/9442096157236281254';

        $html = file_get_html($recipe, $use_include_path = false, $context=null, $offset = -1, $maxLen=-1, $lowercase = true, $forceTagsClosed=true, $target_charset = DEFAULT_TARGET_CHARSET, $stripRN=false, $defaultBRText=DEFAULT_BR_TEXT);

     #   $text = preg_replace("#\s{3,10}|\n#iUs", "<br />\n", convert1251($html->find('#overlay_ingredients_pre .ingredient', 0)->innertext)) ;

       # var_dump($recipe, $text); die;

        $post['title'] = convert1251($html->find('span#title', 0)->plaintext);
        $post['ingredients'] = $rewrite->rewrite(convert1251($html->find('#ingridients', 0)->plaintext));
        $post['method'] = $rewrite->rewrite(convert1251($html->find('#descr_overlay', 0)->plaintext));


        if (strlen(trim($post['title'])) < 10 || strlen(trim($post['ingredients'])) < 50 || strlen(trim($post['method'])) < 100) {
            add_code($cat['id'], $cid, false);
            echo "{$recipe} - Название, метод, ингредиенты - пустое \n";
            continue;
        }


        $post['photos'] = array();

        preg_match_all('#http%3A%2F%2F[^"]+?\.(jpg|png|gif|bmp|jpeg)#iUs', $html->innertext, $photos);

        foreach ($photos[0] as $imgg) {
           # $post['photos'][] = str_ireplace(array('%2F', '%3A', '%20'), array('/', ':', ''), $imgg);

             $post['photos'][] = urldecode($imgg);
        }

        if (count($post['photos']) == 0) continue;

        #var_dump($post['photos']);

        if (post_exist($post['title'])) {
            echo $post['title'] .  "- дубль <br>";
            continue;
        }
        $res_add = add_place($post, $cat, $cid);
        if ($res_add) {
            $cat_count++;
            add_code($cat['id'], $cid, true);
        }
        $html->clear();

        $html->__destruct();
        $html = null;
        unset($html);
    }

}

function post_exist($title)
{
    global $db;
    $title = mysql_real_escape_string($title);

    $ex = $db->super_query("SELECT * FROM `" . PREFIX . "_post" . "` WHERE title = '{$title}' LIMIT 1");

    if ($ex) return true;
    else return false;
}

function add_place($post, $cat, $cid)
{
    global $db, $config;

    $title = $db->safesql($post['title']);


    if (!stop_list($post['ingredients']) || !stop_list($post['method']) || !stop_list($post['title'])) {
        echo $title . " - Stop key find! \n";

        add_code($cat['id'], $cid);
        return false;
    }

    $title = str_ireplace('Рецепт', '', $post['title']);
    $title = str_ireplace('рецепт', '', $title);
    $title = str_ireplace('рецепты', '', $title);
    $title = str_ireplace('Рецепты', '', $title);
    $title = str_ireplace('скачать', '', $title);
    $title = str_ireplace('?', '', $title);
    $title = str_ireplace('?', '', $title);
    $post['n_title'] = $title;


    if ($config['before_title'])
        $title = trim($config['before_title'] . ' ' . lcfirstt($title) . ' ' . $config['after_title']);

    if ($config['before_title'])
        $n_title = trim($config['before_title'] . ' ' . lcfirstt($title) . ' ' . $config['after_title']);

    $author = 'admin';
    #  echo $author[0]->getText(); die;
    $alias =  $cat['alias'] = totranslit($title);


    $tmp = new ctTemplate();
    $tmp->setBaseDir(ROOT_DIR . '/engine/inc/receptonator/template');

    $short_story = $tmp->loadTemplate('short', array('post' => $post, ));
    $full_story = $tmp->loadTemplate('full', array('post' => $post,  'cat' => $cat));

    $full_story = tag_word_filter($full_story);
    $short_story = tag_word_filter($short_story);

    $full_story = $db->safesql($full_story);
    $short_story = $db->safesql($short_story);


   # $date = getRandDate();

    $q = "INSERT INTO `" . PREFIX . "_post` (`id`, `autor`, `date`, `short_story`, `full_story`, `xfields`, `title`, `descr`, `keywords`, `category`, `alt_name`, `comm_num`, `allow_comm`, `allow_main`, `allow_rate`, `approve`, `fixed`, `rating`, `allow_br`, `vote_num`, `news_read`, `votes`, `access`, `expires`, `symbol`, `flag`, `editdate`, `editor`, `reason`, `view_edit`, `tags`)
    VALUES
    	(null, '{$author}', NOW() , '{$short_story}', '{$full_story}', '', '{$title}', '{$title}', '{$title}', '{$cat['cat_id']}', '{$alias}', 1, 1, 1, 1, 1, 0, 0, 1, 0, 0, 0, '', '0000-00-00', '', 1, '', '', '', 0, '');
    ";

    $db->query($q);
    $post_id = $db->insert_id();

    echo $title . "- OK \n";


    return true;
}

function place_exist($code, $cat_id = null)
{
    global $db;

    $w = is_numeric($cat_id) ? "AND cat_id='{$cat_id}'" : "";

    $ex = $db->super_query("SELECT * FROM `" . PREFIX . "_receptonator` WHERE code = '{$code}' $w LIMIT 1");

    if ($ex) return true;
    else return false;
}


function get_one_content($path, $rename = false)
{

    $files = glob('{' . UPLOAD_DIR . '/content/' . $path . '/*.html' . ',' . UPLOAD_DIR . '/content/' . $path . '/*.txt' . '}', GLOB_BRACE);

    if (count($files) == 0) return false;

    $cont = file_get_contents($files[0]);

    if ($rename)
        rename($files[0], $files[0] . '.old');

    #var_dump($files); die;
    return $cont;
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

    }


    if (!$image) return false;

    $result = file_put_contents(ROOT_DIR . '/uploads/' . $cat_alias . '/' . $alias . '.jpg', $image->getBody());

    return $result ? '/uploads/' . $cat_alias . '/' . $alias . '.jpg' : false;
}


function add_code($cat_id, $code, $publish = false)
{
    global $db;
    $publish = (!$publish) ? 0 : 1;

    $q = "INSERT INTO `" . PREFIX . "_receptonator`
        (`id`, `cat_id`, `code`, `publish`)
     VALUES
    	(null,  {$cat_id}, '{$code}', '{$publish}');
    ";
    $db->query($q);

}

function convert1251($string)
{
    $string = str_ireplace('?', '1/2', $string);
    $string = mb_convert_encoding($string, 'cp1251', 'utf-8');
    $string = preg_replace('#\?{2,50}#iUs', '', $string);
    return $string;
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

    $text = $db->safesql(mb_convert_encoding($text, 'cp1251', 'utf-8'));

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
    $NpjLettersFrom = "абвгдезиклмнопрстуфцыі";
    $NpjLettersTo = "abvgdeziklmnoprstufcyi";
    $NpjBiLetters = array("й" => "j", "ё" => "yo", "ж" => "zh", "х" => "x", "ч" => "ch", "ш" => "sh", "щ" => "shh", "э" => "ye", "ю" => "yu", "я" => "ya", "ъ" => "", "ь" => "", "ї" => "yi", "є" => "ye");

    $NpjCaps = "АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЬЪЫЭЮЯЇЄІ";
    $NpjSmall = "абвгдеёжзийклмнопрстуфхцчшщьъыэюяїєі";

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
    global $config;

    $stops = explode(',', $config['stop_list']);

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
    preg_match_all('#[А-Я]#Us', $str, $upC);

   # var_dump($upC);

    if (count($upC[0]) > 4) return $str;

    $str = trim($str);
    $str[0] = strtolower($str[0]);
    return (string)$str;
}

function getRandDate()
{
    global $config;

    if (!$config['publish_date']) $config['publish_date'] = 1;

    $rand = rand(1, $config['publish_date']);

    return date("Y-m-d H:i:s", strtotime("+ {$rand} minutes", strtotime(date("Y-m-d H:i:s"))));
}

class JsParserException extends Exception
{
}

function parse_jsobj($str, &$data)
{
    $str = trim($str);
    if (strlen($str) < 1) return;

    if ($str{0} != '{') {
        throw new JsParserException('The given string is not a JS object');
    }
    $str = substr($str, 1);

    /* While we have data, and it's not the end of this dict (the comma is needed for nested dicts) */
    while (strlen($str) && $str{0} != '}' && $str{0} != ',') {
        /* find the key */
        if ($str{0} == "'" || $str{0} == '"') {
            /* quoted key */
            list($str, $key) = parse_jsdata($str, ':');
        } else {
            $match = null;
            /* unquoted key */
            if (!preg_match('/^\s*[a-zA-z_][a-zA-Z_\d]*\s*:/', $str, $match)) {
                throw new JsParserException('Invalid key ("' . $str . '")');
            }
            $key = $match[0];
            $str = substr($str, strlen($key));
            $key = trim(substr($key, 0, -1)); /* discard the ':' */
        }

        list($str, $data[$key]) = parse_jsdata($str, '}');
    }
    "Finshed dict. Str: '$str'\n";
    return substr($str, 1);
}

function comma_or_term_pos($str, $term)
{
    $cpos = strpos($str, ',');
    $tpos = strpos($str, $term);
    if ($cpos === false && $tpos === false) {
        throw new JsParserException('unterminated dict or array');
    } else if ($cpos === false) {
        return $tpos;
    } else if ($tpos === false) {
        return $cpos;
    }
    return min($tpos, $cpos);
}

function parse_jsdata($str, $term = "}")
{
    $str = trim($str);


    if (is_numeric($str{0} . "0")) {
        /* a number (int or float) */
        $newpos = comma_or_term_pos($str, $term);
        $num = trim(substr($str, 0, $newpos));
        $str = substr($str, $newpos + 1); /* discard num and comma */
        if (!is_numeric($num)) {
            throw new JsParserException('OOPSIE while parsing number: "' . $num . '"');
        }
        return array(trim($str), $num + 0);
    } else if ($str{0} == '"' || $str{0} == "'") {
        /* string */
        $q = $str{0};
        $offset = 1;
        do {
            $pos = strpos($str, $q, $offset);
            $offset = $pos;
        } while ($str{$pos - 1} == '\\'); /* find un-escaped quote */
        $data = substr($str, 1, $pos - 1);
        $str = substr($str, $pos);
        $pos = comma_or_term_pos($str, $term);
        $str = substr($str, $pos + 1);
        return array(trim($str), $data);
    } else if ($str{0} == '{') {
        /* dict */
        $data = array();
        $str = parse_jsobj($str, $data);
        return array($str, $data);
    } else if ($str{0} == '[') {
        /* array */
        $arr = array();
        $str = substr($str, 1);
        while (strlen($str) && $str{0} != $term && $str{0} != ',') {
            $val = null;
            list($str, $val) = parse_jsdata($str, ']');
            $arr[] = $val;
            $str = trim($str);
        }
        $str = trim(substr($str, 1));
        return array($str, $arr);
    } else if (stripos($str, 'true') === 0) {
        /* true */
        $pos = comma_or_term_pos($str, $term);
        $str = substr($str, $pos + 1); /* discard terminator */
        return array(trim($str), true);
    } else if (stripos($str, 'false') === 0) {
        /* false */
        $pos = comma_or_term_pos($str, $term);
        $str = substr($str, $pos + 1); /* discard terminator */
        return array(trim($str), false);
    } else if (stripos($str, 'null') === 0) {
        /* null */
        $pos = comma_or_term_pos($str, $term);
        $str = substr($str, $pos + 1); /* discard terminator */
        return array(trim($str), null);
    } else if (strpos($str, 'undefined') === 0) {
        /* null */
        $pos = comma_or_term_pos($str, $term);
        $str = substr($str, $pos + 1); /* discard terminator */
        return array(trim($str), null);
    } else {
        throw new JsParserException('Cannot figure out how to parse "' . $str . '" (term is ' . $term . ')');
    }
}


function get_ip_array()
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
        if ($t[0] == "\tinet" && $t[1] !== '127.0.0.1') {
            array_push($ips, $t[1]);
        }
    }

    return $ips[rand(0, count($ips) - 1)];
}