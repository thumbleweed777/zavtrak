<?php
/*
=====================================================
-----------------------------------------------------
 Файл: mod_marketgid.php
-----------------------------------------------------
 Назначение: парсер marketgid.ru
=====================================================
*/

if( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

// автор новости
$author = "guest";

include_once ENGINE_DIR . '/classes/parse.class.php';
$parse = new ParseFilter( Array (), Array (), 1, 1 );

if (isset($_POST['doaddnews'])) {
	
	$marketgid_url = trim($_POST['link']);	
	
	$page_1 = intval($_POST['first_page']);
	$page_2 = intval($_POST['last_page']);
	
	if($page_1 <= $page_2) {
		
		$cnt = $page_2 - $page_1;
		$cnt = ($cnt) ? $cnt : 1;
		
		for ($j = $page_1; $j <= $page_2; $j++) {
			
			$m_ind = 60 * ($j - 1);
			
			$marketgid_link[] = str_replace("/0/", "/" . $m_ind . "/", $marketgid_url);									
			$cnt = count($marketgid_link);
		}
		
			// === > MultiCURL
			$mh = curl_multi_init(); 
				   
			for ($i = 0; $i < $cnt; $i++ ) {
				
				$conn[$i] = curl_init($marketgid_link[$i]); 
				curl_setopt($conn[$i], CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($conn[$i], CURLOPT_TIMEOUT, 10);
				curl_multi_add_handle($mh, $conn[$i]);				
			}
				   
			do { 
				
				$n = curl_multi_exec($mh, $active); 
			} 
			while ($active); 
				   
			for ($i = 0; $i < $cnt; $i++ ) { 
				
			    $res[$i] = curl_multi_getcontent($conn[$i]); 		 
				    
				curl_multi_remove_handle($mh, $conn[$i]); 
				curl_close($conn[$i]); 
			} 		
				
			curl_multi_close($mh);
			// === > MultiCURL		
				
		$approve = 1;
		$allow_comm = 1;
		$allow_main = 1;
		$allow_rating = 1;
		$news_fixed = 0;	
		
		define( 'FOLDER_PREFIX', date( "Y-m" ) );	
		
		if( ! is_dir( ROOT_DIR . "/uploads/posts/" . FOLDER_PREFIX ) ) {
			
			@mkdir( ROOT_DIR . "/uploads/posts/" . FOLDER_PREFIX, 0777 );
			@chmod( ROOT_DIR . "/uploads/posts/" . FOLDER_PREFIX, 0777 );
			@mkdir( ROOT_DIR . "/uploads/posts/" . FOLDER_PREFIX . "/thumbs", 0777 );
			@chmod( ROOT_DIR . "/uploads/posts/" . FOLDER_PREFIX . "/thumbs", 0777 );
		}
				
		$config_path_image_upload = ROOT_DIR . "/uploads/posts/" . FOLDER_PREFIX . "/";		
		
		// === > Запись результата в БД
		foreach ($res as $key => $result) {
			
			$result = iconv("utf-8", "windows-1251", $result);
			
			// список товаров
			preg_match("#<ul class=\"goods-list\">(.*?)</ul>#is", $result, $goods);
			preg_match_all("#<li>(.*?)</li>#is", $goods[1], $goodsList);
			
			foreach ($goodsList[1] as $onegood) {
				
				
				// -------- Короткая новость
				
				$goodInfo = array();
				
				// тайтл + ссылка на товар
				preg_match("#<h1><a href=\"(.*?)\"(?:.*?)>(.*?)</a></h1>#is", $onegood, $trade);
				$goodInfo["link"] = "http://goods.marketgid.com" . $trade[1];
				$goodInfo["title"] = $trade[2];
				
				// цена
				preg_match("#<strong>(.*?)</strong>#is", $onegood, $price);
				$goodInfo["price"] = $price[1];
				
				// img
				preg_match("#<img src=\"(.*?)\"#is", $onegood, $poster);
				$goodInfo["poster"] = $poster[1];	
				
				// кор. описание
				preg_match("#<p>(.*?)</p>#is", $onegood, $short_story);
				$goodInfo["short_story"] = trim($short_story[1]);
				
				// --- обработка мал. изображения
				$file_prefix = time() + rand( 1, 100 );
				$file_prefix .= "_";				
				
				$imageurl = $goodInfo["poster"];
				$image_name = explode( "/", $imageurl );
				$image_name = end( $image_name );
				
				$img_name_arr = explode( ".", $image_name );
				$image_size = @filesize_url( $imageurl );
				$type = totranslit( end( $img_name_arr ) );
				
				if( $image_name != "" ) {
					
					$curr_key = key( $img_name_arr );
					unset( $img_name_arr[$curr_key] );
					$image_name = totranslit( implode( ".", $img_name_arr ) ) . "." . $type;
				
				}				
				
				@copy( $goodInfo["poster"], $config_path_image_upload . $file_prefix . $image_name );
				$short_image = FOLDER_PREFIX . "/" . $file_prefix . $image_name;
				// --- обработка мал. изображения	
				
				$tpl->result['shortstory'] = "";
				$tpl->load_template('marketgid.short.tpl');
				
				$tpl->set('{short-story}', $goodInfo["short_story"]);
				$tpl->set('{price}', $goodInfo["price"]);
				$tpl->set('{poster}', "<img src=\"/uploads/posts/" .FOLDER_PREFIX. "/" .$file_prefix . $image_name. "\" />");
				
				$tpl->compile('shortstory');
				$tpl->clear();
		
				
				// -------- Полная новость
				
				// curl обработка
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $goodInfo["link"]);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_USERAGENT, "Opera/9.64 (Windows NT 5.1; U; ru) Presto/2.1.1");
				curl_setopt($ch, CURLOPT_REFERER, "http://marketgid.ru/");
				curl_setopt($ch, CURLOPT_TIMEOUT, 50);
				$return = curl_exec($ch);
				$return = iconv("UTF-8", "windows-1251", $return);
				curl_close($ch);	
				// curl обработка
				
				
				// большой постер
				preg_match("#<td id=\"good-img\">(.*?)</td>#is", $return, $big_img);
				preg_match("#src=\"(.*?)\"#is", $big_img[1], $bigimg);
				$goodInfo["big_poster"] = $bigimg[1];
				
				// Описание товара
				preg_match("#<div class=\"box\" id=\"good-description\">(.*?)</div>#is", $return, $descr);
				$goodInfo["descr"] = trim($descr[1]);
				
				// Характеристики товара
				preg_match("#<div class=\"box\" id=\"good-characteristic\">(.*?)</div>#is", $return, $character);
				$goodInfo["character"] = trim($character[1]);	
				
				// --- обработка бол. изображения
				$file_prefix = time() + rand( 1, 100 );
				$file_prefix .= "_";				
				
				$imageurl = $goodInfo["big_poster"];
				$image_name = explode( "/", $imageurl );
				$image_name = end( $image_name );
				
				$img_name_arr = explode( ".", $image_name );
				$image_size = @filesize_url( $imageurl );
				$type = totranslit( end( $img_name_arr ) );
				
				if( $image_name != "" ) {
					
					$curr_key = key( $img_name_arr );
					unset( $img_name_arr[$curr_key] );
					$image_name = totranslit( implode( ".", $img_name_arr ) ) . "." . $type;
				
				}				
				
				@copy( $goodInfo["big_poster"], $config_path_image_upload . $file_prefix . $image_name );
				$full_image = FOLDER_PREFIX . "/" . $file_prefix . $image_name;
				// --- обработка бол. изображения				

				$tpl->result['fullstory'] = "";
				$tpl->load_template('marketgid.full.tpl');
				
				$tpl->set('{description}', $goodInfo["descr"]);
				$tpl->set('{character}', $goodInfo["character"]);
				$tpl->set('{price}', $goodInfo["price"]);
				$tpl->set('{poster}', "<img src=\"/uploads/posts/" .FOLDER_PREFIX. "/" .$file_prefix . $image_name. "\" />");
				
				$tpl->compile('fullstory');
				$tpl->clear();							
								
				
				// ===== > добавление в базу
				$added_time = time() + ($config['date_adjust'] * 60);
				$thistime = date( "Y-m-d H:i:s", $added_time );																		
								
				$parse->allow_code = false;			
				$short_story = $db->safesql($tpl->result['shortstory']);
				$full_story = $db->safesql($tpl->result['fullstory']);
				$allow_br = 0;				
				
				$parse->ParseFilter();
				$title 		= $db->safesql( $parse->process( trim( strip_tags ($goodInfo["title"]) ) ) );		
				$alt_name 	= totranslit( stripslashes( $title ), true, false );				
											
				$member_id['name'] = ($member_id['name']) ? $member_id['name'] : $author;
	
				if( ! count( $_REQUEST['catlist'] ) ) {
					$catlist = array ();
					$catlist[] = '0';
				} else
					$catlist = $_REQUEST['catlist'];
				$category_list = $db->safesql( implode( ',', $catlist ) );			
				
				$db->query( "INSERT INTO " . PREFIX . "_post (date, autor, short_story, full_story, title, category, alt_name, allow_comm, approve, allow_main, fixed, allow_rate, allow_br, flag) values ('$thistime', '$member_id[name]', '$short_story', '$full_story', '$title', '$category_list', '$alt_name', '$allow_comm', '$approve', '$allow_main', '$news_fixed', '$allow_rating', '$allow_br', '1')" );					
				$news_id = $db->insert_id();
				
				$added_time = time() + ($config['date_adjust'] * 60);
				$inserts = $short_image . "|||" .$full_image;
				$db->query( "INSERT INTO " . PREFIX . "_images (images, author, news_id, date) values ('$inserts', '$member_id[name]', '$news_id', '$added_time')" );				
				// ===== > добавление в базу					
			}					
			
		}
		// === > Запись результата в БД		
	}
	
	clear_cache();
	msgbox( "Новости добавлены", "Новости успешно добавлены, <a href=\"/index.php\">перейти на главную</a> " );	
		
}
elseif (isset($_POST['doparse'])) {
	
	$marketgid_url = trim($_POST['link']);
	
	// curl обработка
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $marketgid_url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, "Opera/9.64 (Windows NT 5.1; U; ru) Presto/2.1.1");
	curl_setopt($ch, CURLOPT_REFERER, "http://marketgid.ru/");
	curl_setopt($ch, CURLOPT_TIMEOUT, 50);
	$return = curl_exec($ch);
	$return = iconv("UTF-8", "windows-1251", $return);
	curl_close($ch);	
	// curl обработка		
	
	preg_match("#<ul class=\"page-list clearfix\">(.*?)</ul>#is", $return, $pages);
	preg_match_all("#<li(?:.*?)>(.*?)</li>#is", $pages[1], $li);
	
	foreach ($li[1] as $index) {
		
		$index = strip_tags($index);
		$index = trim($index);

		if(intval($index))
			$pageIndex[] = intval($index);
	}
	
	if(count($pageIndex)) {
		
		$first_page = min($pageIndex);
		$last_page = max($pageIndex);
	}
	else {
		
		$first_page = 1;
		$last_page = 1;		
	}
	
	$categories_list = CategoryNewsSelection( 0, 0 );
	if( $config['allow_multi_category'] )			
		$cats = "<select name=\"catlist[]\" id=\"category\" onchange=\"onCategoryChange(this.value)\" style=\"width:220px;height:73px;\" multiple>";
	else
		$cats = "<select name=\"catlist[]\" id=\"category\" onchange=\"onCategoryChange(this.value)\">";
		
	$cats .= $categories_list;
	$cats .= "</select>";
			
	$tpl->load_template('marketgid.tpl');
	
	$tpl->set( '{category}', $cats );	
	$tpl->set( '{link}', $marketgid_url );	
	$tpl->set( '{first_page}', $first_page );	
	$tpl->set( '{last_page}', $last_page );		
	
	$tpl->set_block( "'\\[form\\](.*?)\\[/form\\]'si", "" );
	
	$tpl->set('[addnews]', "");
	$tpl->set('[/addnews]', "");
		
	$tpl->compile('content');
	$tpl->clear();	
}
else {
		
	$tpl->load_template('marketgid.tpl');
	
	$tpl->set_block( "'\\[addnews\\](.*?)\\[/addnews\\]'si", "" );
	
	$tpl->set('[form]', "");
	$tpl->set('[/form]', "");
		
	$tpl->compile('content');
	$tpl->clear();	
}

?>