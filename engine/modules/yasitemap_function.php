<?php
/*
=====================================================
Mod "Yandex Site Map (Yasitemap)" for DataLife Engine - by ShapeShifter
url = http://Smart-Planet.ru Version: 2.0
=====================================================
DataLife Engine - by SoftNews Media Group
-----------------------------------------------------
http://dle-news.ru/
-----------------------------------------------------
Copyright (c) 2004,2008 SoftNews Media Group
=====================================================
Данный код защищен авторскими правами
=====================================================
Файл: yasitemap_function.php
-----------------------------------------------------
Назначение: Кэш
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
	die("Hacking attempt!");
}

function get_vars_yasitemap ($papka, $file){

	return unserialize(@file_get_contents(ENGINE_DIR.'/cache/'.$papka.$file.'.php'));
}

function set_vars_yasitemap ($papka, $file, $data){

	$upload_dir = ENGINE_DIR.'/cache/'.$papka;
		if(!is_dir($upload_dir))
		{
		mkdir($upload_dir, 0777);
		@chmod ($upload_dir, 0777);
		}
		else
		@chmod ($upload_dir, 0777);

    $fp = fopen(ENGINE_DIR.'/cache/'.$papka.$file.'.php', 'wb+');
    fwrite($fp, serialize($data) );
    fclose($fp);

	@chmod(ENGINE_DIR.'/cache/'.$papka.$file.'.php', 0666);
}
?>