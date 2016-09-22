<?php

$tagConfig = parse_ini_file(ENGINE_DIR . '/data/taginator.config.ini');

if (($tagConfig['enable'])  == 0) {

switch ($tagConfig['block_sort']) {
    case('DESC'):
        $sort = "ORDER BY s.date DESC";
        break;
    case('ASC'):
        $sort = "ORDER BY s.date ASC";
        break;
    case('RANDOM'):

	$id_last = $db->super_query("SELECT s.id  FROM " . PREFIX . "_static s   ORDER BY s.id DESC   LIMIT 1", true);
  	
  	$ids=array();
  	for($i=1;$i < $tagConfig['block_limit'] + 7 ;$i++) {
  	 $ids[] = rand (1, $id_last[0]['id'] );
  	}
      	$ids= implode(',', $ids);
        $sort = "AND s.id IN($ids) ORDER BY s.id DESC";
        break;
}

$query = "SELECT s.*, t.image, t.id  FROM " . PREFIX . "_static s
    LEFT JOIN " . PREFIX ."_taginator t ON t.static_id=s.id
    WHERE t.id > 0
  {$sort}
     LIMIT {$tagConfig['block_limit']}";
$results = $db->super_query($query, true);

$tag_block = '';

foreach ($results as $result) {

    $tmp = str_ireplace('{title}' , $result['descr'], $tagConfig['block_template']);
    $tmp = str_ireplace('{img}' , '/uploads/taginator/Small-'.$result['image'], $tmp);
    $tmp = str_ireplace('{link}' , '/'.$result['name'].'.html', $tmp);
    $tmp = str_ireplace("'" , '"', $tmp);

    $tag_block .= $tmp;
}

$tag_block .= '<a  style="color: #000000; float: right;" href="/tagmap/">еще</a> <br clear="all" />';

}else {
    $tag_block = '';
}