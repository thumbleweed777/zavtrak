<?php

if (!defined('AUTOSITEMAP')) die('WTF?!');


$now_time = getdate();

if ( $now_time['hours'] === 2) {

	$updated_date = unserialize(@file_get_contents('uploads/last_update.txt'));

	if ( $updated_date['mday'] !== $now_time['mday'] ) {

		file_put_contents('uploads/last_update.txt', serialize($now_time) );

		if (file_exists(ENGINE_DIR.'/classes/google.class.php')) {
		
			include_once ENGINE_DIR.'/classes/google.class.php';
			$map = new googlemap($config);

			$map->limit = 50000;
			$map->news_priority = '0.6';
			$map->stat_priority = '0.5';
			$map->cat_priority =  '0.7';


			$sitemap = $map->build_map();

			$handler = fopen(ROOT_DIR. "/uploads/sitemap.xml", "wb+");
			fwrite($handler, $sitemap);
			fclose($handler);

			@chmod(ROOT_DIR. "/uploads/sitemap.xml", 0666);
			//print_r('sitemap_updated');
		}

	}

}