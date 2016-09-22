<?php
define('DATALIFEENGINE', 'dle');
define('FILE_PATH', dirname(__FILE__) . '/../../data/taginator.db.txt');
define('ENGINE_DIR', dirname(__FILE__) . '/../../');
define('ROOT', dirname(__FILE__) . '/../../../');


include ROOT . '/engine/classes/mysql.class.php';
include ROOT . '/engine/data/dbconfig.php';
require_once  ENGINE_DIR . 'modules/taginator.php';

$config = array(
    'num' => 10
);

$fp = fopen(FILE_PATH, 'r+');
$fc = fopen(FILE_PATH, 'c+');
$i = 1;
$pos = 0;
while ($str = fgets($fp)) {
    if ($i > $config['num']) {
        break;
    }
    if (taginator_execute(1000, trim($str)))
    {
        echo $str . ' - ok<br>';
    }else {
        echo $str . ' - fuck<br>';
    }


    fseek($fc, $pos);
    fwrite($fc, str_repeat("\x0", strlen($str)), 50000);
    $pos = ftell($fp);

    $i++;
}
fclose($fp);
fclose($fc);


function totranslit($var)
{

    $var = preg_replace("/&#?[a-z0-9]{2,8};/i", "", $var);
    $table = array(
        '�' => 'A',
        '�' => 'B',
        '�' => 'V',
        '�' => 'G',
        '�' => 'D',
        '�' => 'E',
        '�' => 'YO',
        '�' => 'ZH',
        '�' => 'Z',
        '�' => 'I',
        '�' => 'J',
        '�' => 'K',
        '�' => 'L',
        '�' => 'M',
        '�' => 'N',
        '�' => 'O',
        '�' => 'P',
        '�' => 'R',
        '�' => 'S',
        '�' => 'T',
        '�' => 'U',
        '�' => 'F',
        '�' => 'H',
        '�' => 'C',
        '�' => 'CH',
        '�' => 'SH',
        '�' => 'CSH',
        '�' => '',
        '�' => 'Y',
        '�' => '',
        '�' => 'E',
        '�' => 'YU',
        '�' => 'YA',

        '�' => 'a',
        '�' => 'b',
        '�' => 'v',
        '�' => 'g',
        '�' => 'd',
        '�' => 'e',
        '�' => 'yo',
        '�' => 'zh',
        '�' => 'z',
        '�' => 'i',
        '�' => 'j',
        '�' => 'k',
        '�' => 'l',
        '�' => 'm',
        '�' => 'n',
        '�' => 'o',
        '�' => 'p',
        '�' => 'r',
        '�' => 's',
        '�' => 't',
        '�' => 'u',
        '�' => 'f',
        '�' => 'h',
        '�' => 'c',
        '�' => 'ch',
        '�' => 'sh',
        '�' => 'csh',
        '�' => '',
        '�' => 'y',
        '�' => '',
        '�' => 'e',
        '�' => 'yu',
        '�' => 'ya',
 ' ' => '-',
    );

    $latin_string = str_replace(
        array_keys($table),
        array_values($table), trim($var)
    );

    $latin_string = str_replace(".php", "", $latin_string);
    $latin_string = str_replace(" ", '-', $latin_string);
    $latin_string = trim(strip_tags($latin_string));

    if (strlen($latin_string) > 47) {
        $latin_string = substr($latin_string, 0, 45);
    }

    $latin_string = preg_replace("/\s+/ms", '-', trim($latin_string));
    $latin_string = preg_replace("/[^a-z0-9\_\-\.]+/mi", "-", $latin_string);
    $latin_string = preg_replace('#[\-]+#i', '-', $latin_string);
  #  $latin_string = str_replace(array('.', '/', '-'), '', $latin_string);
    $latin_string = strtolower($latin_string);



    return $latin_string;
}
