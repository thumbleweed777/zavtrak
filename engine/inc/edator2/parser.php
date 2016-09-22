<?php

setlocale(LC_ALL, "ru_RU.CP1251");
header("Content-Type: text/html; charset=windows-1251");
ini_set('max_execution_time', 0);

function getModuleName()
{
    return 'edator2';
}

function get_class_name($url)
{

    if (preg_match('#kylinar\.com\.ua#', $url)) $name = 'KylinarComUaParserCore';
    elseif (preg_match('#recipe\.repa\.kz#', $url)) $name = 'RecipeRepaKzParser';
    elseif (preg_match('#ratatui\.org#', $url)) $name = 'RatatuiOrg';
    elseif (preg_match('#culinarbook\.ru#', $url)) $name = 'CulinarbookRu';
    elseif (preg_match('#epovar\.kz#', $url)) $name = 'EpovarKz';
    elseif (preg_match('#mactep\.com\.ua#', $url)) $name = 'MactepComUa';


    return isset($name) ? $name : false;
}


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


require_once MODULE_DIR . '/classes/ParserCore.php';

autoload(MODULE_DIR . '/classes');


set_include_path(VENDOR_DIR);

require_once VENDOR_DIR . '/Zend/Loader.php';

require_once(VENDOR_DIR . '/simplehtmldom/simple_html_dom.php');


$config = parse_ini_file(ROOT_DIR . '/engine/data/' . getModuleName() . '.config.ini');

if ($config['enable']) die ('Module turn off!');

Zend_Loader::loadClass('Zend_Http_Client');

$where = isset($_GET['cat_id']) ? "WHERE c.id IN({$_GET['cat_id']})" : "";

$q = "SELECT c.*, COUNT(v.id) as v_count FROM dle_" . getModuleName() . "_cat c LEFT JOIN " . PREFIX . "_" . getModuleName() . " v ON v.cat_id = c.id AND v.publish = 1 {$where}     GROUP BY c.id ORDER BY c.id DESC  ";
$categories = $db->super_query($q, true);


foreach ($categories as $cat) {


    if ($cat['v_count'] >= $cat['limit']) continue;

    $cat_count = $cat['v_count'];

    echo "URL - '{$cat['query']}', start find/  <br>";
    $cc = $cat['v_count'];

    if (!$className = get_class_name($cat['query'])) {
        echo "Error - '{$cat['query']}', class for this url not found.  <br>";
        continue;
    }
    /* @var $class GotovimSamiParserCore  */
    $class = new $className('cp1251', $cat['query'], $cat['p_from'], $cat['p_to'], $config);


    $pageLinks = $class->getPageLinks();
    #var_dump($pageLinks); die;
    $pL = 1;
    foreach ($pageLinks as $pK => $link) {

        $pEx = code_exist(md5($link));

        if ((count($pageLinks) - 1) == $pK AND $pEx) {
            setEndLimit($cat);
        }

        if ($pEx) continue;
        add_code($cat['id'], md5($link));

        $postLinks = array_values(array_unique($class->getPostLinks($link)));
        # var_dump($postLinks); die;

        foreach ($postLinks as $postK => $pLink) {


            if ((count($pageLinks) - 1) == $pK AND (count($postLinks) - 1) == $postK) {
                setEndLimit($cat);
            }

            if (code_exist(md5($pLink))) continue;
            add_code($cat['id'], md5($pLink), true);

            #  var_dump(count($pageLinks), $pK, count($postLinks), $postK);
            #  echo '<br>';

            try {

                $class->setContent($pLink);
                $post['title'] = $class->getTitle();
                $post['ingredients'] = $class->getIngredients();
                $post['method'] = $class->getMethod();

            } catch (Exception $e) {

                echo $e->getMessage() . $e->getFile() . "<br>";
                continue;
            }


            if (!$post['title'])
                continue;

            if (post_exist($post['title'])) {
                echo $post['title'] .  "- ‰Û·Î¸ <br>";
                continue;
            }
            $res_add = add_post($post, $cat);

            if ($res_add) {
                $cc++;
            }

            if ($cc + 1 > $cat['limit']) {
                continue 3;
            }
            force_flush();
        }
        if ($pL == 2)
            die ('cron limit');

        $pL++;
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

function setEndLimit($cat)
{
    global $db;
    $where = "WHERE c.id IN({$cat['id']})";
    $dbCat = $db->super_query("SELECT c.*, COUNT(v.id) as v_count FROM dle_" . getModuleName() . "_cat c LEFT JOIN " . PREFIX . "_" . getModuleName() . " v ON v.cat_id = c.id AND v.publish = 1 {$where}     GROUP BY c.id ORDER BY c.id DESC");

    $limit = $dbCat['v_count'];
    $db->query("UPDATE `dle_" . getModuleName() . "_cat` SET `limit` = '{$limit}' WHERE `id` = '{$cat['id']}'");
}

function force_flush()
{
    echo "\n\n<!-- Deal with browser-related buffering by sending some incompressible strings -->\n\n";

    for ($i = 0; $i < 5; $i++)
        echo "<!-- abcdefghijklmnopqrstuvwxyz1234567890aabbccddeeffgghhiijjkkllmmnnooppqqrrssttuuvvwwxxyyzz11223344556677889900abacbcbdcdcededfefegfgfhghgihihjijikjkjlklkmlmlnmnmononpopoqpqprqrqsrsrtstsubcbcdcdedefefgfabcadefbghicjkldmnoepqrfstugvwxhyz1i234j567k890laabmbccnddeoeffpgghqhiirjjksklltmmnunoovppqwqrrxsstytuuzvvw0wxx1yyz2z113223434455666777889890091abc2def3ghi4jkl5mno6pqr7stu8vwx9yz11aab2bcc3dd4ee5ff6gg7hh8ii9j0jk1kl2lmm3nnoo4p5pq6qrr7ss8tt9uuvv0wwx1x2yyzz13aba4cbcb5dcdc6dedfef8egf9gfh0ghg1ihi2hji3jik4jkj5lkl6kml7mln8mnm9ono -->\n\n";

    while (ob_get_level())
        ob_end_flush();

    @ob_flush();
    @flush();
}


function add_post($post, $cat)
{
    global $db, $config;



        $img = false;


    $title = $post['title'];

    $author = 'admin';
    $alias = $cat['alias'] = totranslit($post['title']);

    $tmp = new ctTemplate();
    $tmp->setBaseDir(ROOT_DIR . '/engine/inc/' . getModuleName() . '/template');


    $short_story = $tmp->loadTemplate('short', array('post' => $post, 'image' => $img));
    $full_story = $tmp->loadTemplate('full', array('post' => $post, 'image' => $img, 'cat' => $cat));

    $full_story = tag_word_filter($full_story);
    $short_story = tag_word_filter($short_story);

    $full_story = $db->safesql($full_story);
    $short_story = $db->safesql($short_story);

    $on_home_page = $cat['allow_main'];

    $q = "INSERT INTO `" . PREFIX . "_post` (`id`, `autor`, `date`, `short_story`, `full_story`, `xfields`, `title`, `descr`, `keywords`, `category`, `alt_name`, `comm_num`, `allow_comm`, `allow_main`, `allow_rate`, `approve`, `fixed`, `rating`, `allow_br`, `vote_num`, `news_read`, `votes`, `access`, `expires`, `symbol`, `flag`, `editdate`, `editor`, `reason`, `view_edit`, `tags`)
    VALUES
    	(null, '{$author}', NOW() , '{$short_story}', '{$full_story}', '', '{$title}', '{$title}', '{$title}', '{$cat['cat_id']}', '{$alias}', 0, 1, {$on_home_page}, 1, 1, 0, 0, 1, 0, 0, 0, '', '0000-00-00', '', 1, '', '', '', 0, '');
    ";

    $db->query($q);
    $post_id = $db->insert_id();

    echo  $title . " - OK <br>";

    return true;
}

function get_short($text)
{

    $short = '';
    if (preg_match('#<\s*br[\s/]*>#', $text, $br)) {
        $ps = explode($br[0], $text);

        foreach ($ps as $k => $p) {
            $short .= $p . '<br />';
            if ($k == 3) break;
        }

        return $short . ' ...';
    } else {

        $short = substr($text, 0, 100) . '...';

        return $short;
    }


}


function code_exist($code, $cat_id = null)
{
    global $db;

    $w = is_numeric($cat_id) ? "AND cat_id='{$cat_id}'" : "";

    $ex = $db->super_query("SELECT * FROM `" . PREFIX . "_" . getModuleName() . "` WHERE code = '{$code}' $w LIMIT 1");

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
        return false;
    }


    $rnd = rand(1, 9999);

    $result = file_put_contents(ROOT_DIR . '/uploads/recipe/' . $cat_alias . '/' . $alias . '_' . $rnd . '.jpg', $image->getBody());

    return $result ? '/uploads/recipe/' . $cat_alias . '/' . $alias . '_' . $rnd . '.jpg' : false;
}


function add_code($cat_id, $code, $publish = false)
{
    global $db;
    $publish = (!$publish) ? 0 : 1;

    $q = "INSERT INTO `" . PREFIX . "_" . getModuleName() . "`
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
    $NpjLettersFrom = "‡·‚„‰ÂÁËÍÎÏÌÓÔÒÚÛÙˆ˚≥";
    $NpjLettersTo = "abvgdeziklmnoprstufcyi";
    $NpjBiLetters = array("È" => "j", "∏" => "yo", "Ê" => "zh", "ı" => "x", "˜" => "ch", "¯" => "sh", "˘" => "shh", "˝" => "ye", "˛" => "yu", "ˇ" => "ya", "˙" => "", "¸" => "", "ø" => "yi", "∫" => "ye");

    $NpjCaps = "¿¡¬√ƒ≈®∆«»… ÀÃÕŒœ–—“”‘’÷◊ÿŸ‹⁄€›ﬁﬂØ™≤";
    $NpjSmall = "‡·‚„‰Â∏ÊÁËÈÍÎÏÌÓÔÒÚÛÙıˆ˜¯˘¸˙˚˝˛ˇø∫≥";

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
    preg_match_all('#[¿-ﬂ]#Us', $str, $upC);

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
        if ($t[0] == "\tinet" && $t[1] !== '127.0.0.1' && preg_match('#91\.238\.245#', $t[1])) {
            array_push($ips, $t[1]);
        }
    }
    #var_dump($ips); die;
    #  return '192.168.2.105';
    return $ips[rand(0, count($ips) - 1)];
}


function get_google_img($k, $cat_alias, $alias)
{

    $k = convToUtf8($k);

    $http = new Zend_Http_Client(null, array('adapter' => 'Zend_Http_Client_Adapter_Curl'));
    #$http->getAdapter()->setCurlOption(CURLOPT_INTERFACE, get_ip_array());
    $http->setUri('http://images.google.com/images?tbs=isz:m&as_q=' . urlencode($k));

    $response = $http->request('GET');
    $html = str_get_html($response->getBody());


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

function convToUtf8($str)
{
    if (mb_detect_encoding($str, "UTF-8, ISO-8859-1, GBK") != "UTF-8") {
        return iconv("cp1251", "utf-8", $str);
    } else return $str;
}

function autoload($path)
{

    $files = glob($path . '/*.php');
    foreach ($files as $file) {
        require_once $file;

    }

}