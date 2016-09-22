<?php

/**
 * Module for DLE 7.5 "Taginator", frontend part
 * author: Moroz A.N.
 * skype: str01tel
 * email: netstroix@gmail.com
 */

$tagConfig = parse_ini_file(ENGINE_DIR . '/data/taginator.config.ini');
define(UPLOAD_DIR, ENGINE_DIR . '/../uploads');

if ( false === function_exists('lcfirst') ):
    function lcfirst( $str )
    { return (string)(strtolower(substr($str,0,1)).substr($str,1));}
endif; 


function taginator_execute($count, $request)
{


    global $tagConfig;
    if (($tagConfig['enable'])  == 1) return false;
    
    $count = intval($count)+1;
    
    $request = str_ireplace('+', ' ', $request);

    if ( $tagConfig['popularity_key'] <= $count || $tagConfig['popularity_key'] == 0) {
        if (taginator_stop_list($request)) {
            if ($results = taginator_search_post($request)) {

                $request = taginator_cut_tag($request);
                taginator_create($request, $count, $results);

                 return true;
            }

        }
    }
    return false;

}

function taginator_stop_list($request)
{
    global $tagConfig;

    $stops = explode(',', $tagConfig['stop_list']);

    if (count($stops) == 1 && trim($stops[0]) == '') return true;

    foreach ($stops as $stop) {
        $stop = trim(preg_quote($stop, '#'));
        if (preg_match("#{$stop}#i", $request)) {
            return false;
        }
    }
    return true;
}

function taginator_create($request, $count, $results)
{
    global $db, $tagConfig;

    $request = $tagConfig['add_to_tag'] . ' ' . $db->safesql(taginator_cut_tag($request));
    $slug = totranslit($request);
    $request = lcfirst($request);

    $exist = $db->super_query("SELECT id  FROM " . PREFIX . "_taginator WHERE tag='{$request}' OR slug='{$slug}' ", true);
    if (count($exist) > 0) return false;

    $image = get_google_img($request);
    
  #  var_dump($image); die;
    
    if (!$image || trim($image) == '' ) return false;

    if(!class_exists('ctTemplate'))
    require_once  ENGINE_DIR . '/modules/taginator/ctTemplate.class.php';

    $tmp = new ctTemplate();
    $tmp->setBaseDir(ENGINE_DIR . '/modules/taginator/template');

    $static = $tmp->loadTemplate('static',
                                 array(
                                      'title' => $request,
                                      'img_name' => $image,
                                      'results' => $results,
                                 )
    );

    $static = $db->safesql( tag_word_filter( $static )  );
    $request = ucfirst( trim($db->safesql($request)) );

    $db->query("INSERT INTO `" . PREFIX . "_static` (`id`, `name`, `descr`, `template`, `allow_br`, `allow_template`, `grouplevel`, `tpl`, `metadescr`, `metakeys`, `views`, `template_folder`, `date`) VALUES (NULL, '{$slug}', '{$request}', '{$static}', '0', '0', 'all', '', '{$request}', '{$request}', '0', '', NOW())");
    $static_id = $db->insert_id();
    $db->query("INSERT INTO `" . PREFIX . "_taginator` (`id`, `tag`, `slug`,  `image`, `popularity`, `created_at`, `static_id`) VALUES (null, '{$request}', '{$slug}',  '{$image}', '{$count}', NOW(), '{$static_id}' )");

}

function tag_word_filter($string)
{

    preg_match_all('#\{%(.+)%\}#Us', $string, $match);

    foreach($match[1] as $key => $words) {
        $w_array = explode('|', $words);
        $w_count = count($w_array) - 1;
        $string = str_replace($match[0][$key], $w_array[rand(0, $w_count)], $string);
    }

    return $string;
}

function taginator_cut_tag($request)
{
    global $tagConfig;
    $cuts = explode(',', $tagConfig['cut_list']);

    foreach ($cuts as $cut) {
        $cut = trim(preg_quote($cut, '#'));
        $request = str_ireplace($cut, '', $request);
    }

    return $request;
}

function taginator_search_post($request)
{
    global $db, $tagConfig;

    $like_title = generate_like($request, $tagConfig['accuracy_search'], 'title');
    $like_post = generate_like($request, $tagConfig['accuracy_search'], 'full_story');

    $q = "SELECT *  FROM " . PREFIX . "_post WHERE  {$like_title} OR {$like_post} LIMIT {$tagConfig['search_limit']}";
    
    $results = $db->super_query($q, true);
    if (!$results) return false;

    return $results;
}

function get_google_img($k)
{
    require_once ENGINE_DIR . '/modules/taginator/http.php';
    require_once ENGINE_DIR . '/modules/taginator/simpleImg.php';


    $title = $k;
    $k = mb_convert_encoding($k, 'UTF-8', 'CP1251');
    
    
   $base_url = 'http://ajax.googleapis.com/ajax/services/search/images?v=1.0';
   $url = $base_url;
   $url .= '&imgsz=large&q='.urlencode($k).'&start=00';

             $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, $url);
            $json = curl_exec($curl);
            curl_close($curl);
            $results = json_decode($json);  



    if (count($results) == 0 || !is_writable(UPLOAD_DIR)) return false;


    foreach ($results->responseData->results as $result) {
        $result = $result->url;
      #  var_dump($results); die;
	if (!preg_match('#\.jpg$#', $result)) continue;
	
        $http = new Http();

        $http->execute($result);

        if ($http->getStatus() !== '200' || $http->getStatus() == 0) {
            continue;
        }

        preg_match('#(png|jpg|jpeg|gif)$#i', $result, $ext);

        $file_name = date('M-Y').'/'.totranslit($title) . '.' . $ext[1];
     

        $path_to_img = UPLOAD_DIR . DIRECTORY_SEPARATOR . 'taginator' . DIRECTORY_SEPARATOR . $file_name;
        $small_name = UPLOAD_DIR . DIRECTORY_SEPARATOR . 'taginator' . DIRECTORY_SEPARATOR . 'Small-' . $file_name;

        if(!is_readable(dirname ($path_to_img) )) {
            umask(0);
            @mkdir(dirname ($path_to_img), 0777 , true);
        }
        if(!is_readable(dirname ($small_name) )) {
            umask(0);
            @mkdir(dirname ($small_name), 0777 , true);
        }

        $write = file_put_contents($path_to_img, $http->getResult());

        if ($write) {

            $tb = new SimpleImage();
            $tb->load($path_to_img);
            $tb->scale(90);
            $tb->resizeToHeight(100);
            $tb->save($small_name);

            return $file_name;
        }

    }

    return false;
}



function generate_like($query, $type, $column)
{

    global $db;
    $query = trim($db->safesql($query));
    $query = preg_replace("/[^\w\x7F-\xFF\s]/", " ", $query);
    $query = preg_replace("/\s+/", " ", $query);

    $querys = explode(' ', $query);
    $count_words = count($querys);

    switch ($type) {
        case('full_match'):

            $like = "{$column} LIKE '%$query%'";
            break;

        case('once_word'):

            $qq = array();
            foreach ($querys as $q) {
                $qq[] = "{$column} LIKE '%$q%'";
            }
            $like = implode(' OR ', $qq);
            break;

        case('all_words'):

            $qq = array();
            foreach ($querys as $q) {
                $qq[] = "{$column} LIKE '%$q%'";
            }
            $like = implode(' AND ', $qq);

            break;
        case('last_two'):

            if ($count_words >= 2) {
                $like = "{$column} LIKE '%{$querys[$count_words-2]}%' AND {$column} LIKE '%{$querys[$count_words-1]}%'";
            } elseif ($count_words == 1) {
                $like = "{$column} LIKE '%{$querys[$count_words-1]}%'";
            } else {
                $like = "{$column} LIKE '%{$query}%'";
            }

            break;

        case('first_two'): 

            if ($count_words >= 2) {
                $like = "{$column} LIKE '%{$querys[0]}%' AND {$column} LIKE '%{$querys[1]}%'";
            } elseif ($count_words == 1) {
                $like = "{$column} LIKE '%{$querys[0]}%'";
            } else {
                $like = "{$column} LIKE '%{$query}%'";
            }

            break;
            
        case('first_three'): 

            if ($count_words >= 3) {
                $like = "{$column} LIKE '%{$querys[0]}%' AND {$column} LIKE '%{$querys[1]}%' AND {$column} LIKE '%{$querys[2]}%'";
            } elseif ($count_words == 1) {
                $like = "{$column} LIKE '%{$querys[0]}%'";
            } else {
                $like = "{$column} LIKE '%{$query}%'";
            }

            break;
        case('first_four'): 

            if ($count_words >= 4) {
                $like = "{$column} LIKE '%{$querys[0]}%' AND {$column} LIKE '%{$querys[1]}%' AND {$column} LIKE '%{$querys[2]}%' AND {$column} LIKE '%{$querys[3]}%'";
            } elseif ($count_words == 1) {
                $like = "{$column} LIKE '%{$querys[0]}%'";
            } else {
                $like = "{$column} LIKE '%{$query}%'";
            }

            break;
            
           case('first_five'): 

            if ($count_words >= 5) {
                $like = "{$column} LIKE '%{$querys[0]}%' AND {$column} LIKE '%{$querys[1]}%' AND {$column} LIKE '%{$querys[2]}%' AND {$column} LIKE '%{$querys[3]}%' AND {$column} LIKE '%{$querys[4]}%'";
            } elseif ($count_words == 2) {
                $like = "{$column} LIKE '%{$querys[0]}%'";
            } else {
                $like = "{$column} LIKE '%{$query}%'";
            }

            break;

    }

    return $like;
}