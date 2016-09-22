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
 Файл: opensearch.php
-----------------------------------------------------
 Назначение: Модуль поддержки OpenSearch
=====================================================
*/

define( 'DATALIFEENGINE', true );
define( 'ROOT_DIR', '..' );
define( 'ENGINE_DIR', dirname( __FILE__ ) );

error_reporting( 7 );
ini_set( 'display_errors', true );
ini_set( 'html_errors', false );

include ENGINE_DIR . '/data/config.php';

if( $config['http_home_url'] == "" ) {
	
	$config['http_home_url'] = explode( "engine/opensearch.php", $_SERVER['PHP_SELF'] );
	$config['http_home_url'] = reset( $config['http_home_url'] );
	$config['http_home_url'] = "http://" . $_SERVER['HTTP_HOST'] . $config['http_home_url'];

}

require_once ENGINE_DIR . '/classes/templates.class.php';

$tpl = new dle_template( );
$tpl->dir = ROOT_DIR . '/templates';
define( 'TEMPLATE_DIR', $tpl->dir );

$tpl->load_template( 'opensearch.tpl' );

$tpl->set( '{path}', $config['http_home_url'] );

$tpl->compile( 'main' );

header( 'Content-type: application/xml' );

echo $tpl->result['main'];

?>