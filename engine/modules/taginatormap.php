<?php

require_once ENGINE_DIR.DIRECTORY_SEPARATOR.'/vendor/util/Pagination.php';
$tagConfig = parse_ini_file(ENGINE_DIR . '/data/taginator.config.ini');

#$query = "SELECT t.*, s.image FROM " . PREFIX . "_taginator t LEFT JOIN " . PREFIX . "_static ORDER BY id DESC";

$query = "SELECT s.*, t.image, t.id  FROM " . PREFIX . "_static s
    LEFT JOIN " . PREFIX ."_taginator t ON t.static_id=s.id
    WHERE t.id > 0
    ORDER BY t.created_at DESC
";



$pager = new Pagination($query, 50, 25, "do=tagmap");
$pager->setDebug(false);

$results = $pager->paginate();

if( !is_array($results) ) $results = array();

$html = '';
foreach ($results as $result) {
    $html .= "<a href='/{$result['name']}.html'> <img style='float: left; margin-right: 4px' width='70' src='/uploads/taginator/Small-{$result['image']}' alt='{$result['descr']}' title='{$result['descr']}' /> {$result['descr']}</a><br clear='all' /><br />";

}


$nav = preg_replace('#index\.php\?page=(\d+)\&do\=tagmap#', 'tagmap/page/$1', $pager->renderFullNav() );
$nav = preg_replace('#tagmap/page/1"#', 'tagmap/"', $nav );

$tpl->load_template('tagmap.tpl');

$tpl->set('{results}', $html);
$tpl->set('{pagination}', $nav );

$tpl->compile('content');