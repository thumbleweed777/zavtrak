<?php
/*
=====================================================
 DataLife Engine Nulled by M.I.D-Team
-----------------------------------------------------
 http://www.mid-team.ws/
-----------------------------------------------------
 Copyright (c) 2004,2009 SoftNews Media Group
=====================================================
 Данный код защищен авторскими правами
=====================================================
 Файл: mysql.php
-----------------------------------------------------
 Назначение: Класс для работы с базой данных
=====================================================
*/

if(!defined('DATALIFEENGINE'))
{
  die("Hacking attempt!");
}

if ( extension_loaded('mysqli') AND version_compare("5.0.5", phpversion(), "!=") )
{
	include_once( ENGINE_DIR."/classes/mysqli.class.php" );
}
else
{
	include_once( ENGINE_DIR."/classes/mysql.class.php" );
}

?>