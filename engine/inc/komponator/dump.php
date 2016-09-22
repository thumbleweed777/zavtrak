<?php

setlocale(LC_ALL, "ru_RU.CP1251");
header("Content-Type: text/html; charset=windows-1251");

define('DATALIFEENGINE', 'dle');
ini_set('max_execution_time', 0);

$dir = dirname(__FILE__);
define('ROOT_DIR', $dir . '/../../..');

define('DUMP_NAME', date('Y-m-d_H:i') . '-dump.sql');
define('DUMP_PATH', $dir . '/dump/' . DUMP_NAME);

include ROOT_DIR . '/engine/classes/mysql.class.php';
include ROOT_DIR . '/engine/data/dbconfig.php';

$cats = is_array($_POST['cats']) ? $_POST['cats'] : array();

fecho("<div style='font-family: helvetica; font-size: 12px;'>");

fecho("Старт компоновки...<br>");


foreach ($cats as $id => $num) {
    if ($num == 0) continue;
    $cat = $db->super_query("SELECT * FROM dle_category WHERE id={$id} LIMIT 1");
    dump($id, $num);
    fecho("Дамп категории <b>{$cat['name']}</b> c {$num} новостями выполнен успешно.<br>");
}

fecho("<br>Архивирование дампа...<br>");
archive();
fecho("Дамп готов...<br>");
fecho("<a href='/engine/inc/komponator/dump/" . DUMP_NAME . ".tar.gz'>Скачать чудо дамп</a><br>");
fecho("</div>");

function dump($id, $num)
{
    $cat_dump = dumpCat($id, $num);
    file_put_contents(DUMP_PATH, $cat_dump, FILE_APPEND);
    $posts_dump = dump_list_values($id, $num);
    file_put_contents(DUMP_PATH, $posts_dump, FILE_APPEND);

    unset($cat_dump, $posts_dump);
}

function dumpCat($cat_id)
{
    global $db;
    $sql = $db->query("SELECT * FROM dle_category WHERE id={$cat_id}  LIMIT 1");
    $output = "\n\n-- Dump category ID={$cat_id}  \n\n";
    while ($row = mysql_fetch_array($sql)) {
        $broj_polja = count($row) / 2;
        $output .= "INSERT INTO `dle_category` VALUES(";
        $buffer = '';
        for ($i = 0; $i < $broj_polja; $i++) {
            $vrednost = $row[$i];
            if (!is_integer($vrednost)) {
                $vrednost = "'" . str_ireplace(array("\r\n", "\r", "\n"), '\n', addslashes(iconv('cp1251', 'utf-8', $vrednost))) . "'";
            }
            $buffer .= $vrednost . ', ';
        }
        $buffer = substr($buffer, 0, count($buffer) - 3);
        $output .= $buffer . ");\n";
    }

    return $output;
}

function dump_list_values($cat_id, $num)
{
    global $db;
    $sql = $db->query("SELECT * FROM dle_post WHERE category={$cat_id} ORDER BY RAND() LIMIT {$num}");
    $output = "\n\n -- Insert data -- \n\n";
    while ($row = mysql_fetch_array($sql)) {
        $broj_polja = count($row) / 2;
        $output .= "INSERT INTO `dle_post` VALUES(";
        $buffer = '';
        for ($i = 0; $i < $broj_polja; $i++) {

            $vrednost = $row[$i];
            if ($i == 0) {
                $vrednost = 'null';
            } elseif (!is_integer($vrednost)) {
                $vrednost = "'" . str_ireplace(array("\r\n", "\r", "\n"), '\n', addslashes(iconv('cp1251', 'utf-8', $vrednost))) . "'";
            }
            $buffer .= $vrednost . ', ';
        }
        $buffer = substr($buffer, 0, count($buffer) - 3);
        $output .= $buffer . ");\n";
    }

    return $output;
}

function archive()
{

    $file = DUMP_PATH;
    $gzfile = DUMP_PATH . ".tar.gz";
    system("tar -cvzf {$gzfile} {$file}");

    unlink($file);
}

function fecho($str)
{
    echo $str;

    for ($i = 0; $i < 5; $i++)
        echo "<!-- abcdefghijklmnopqrstuvwxyz1234567890aabbccddeeffgghhiijjkkllmmnnooppqqrrssttuuvvwwxxyyzz11223344556677889900abacbcbdcdcededfefegfgfhghgihihjijikjkjlklkmlmlnmnmononpopoqpqprqrqsrsrtstsubcbcdcdedefefgfabcadefbghicjkldmnoepqrfstugvwxhyz1i234j567k890laabmbccnddeoeffpgghqhiirjjksklltmmnunoovppqwqrrxsstytuuzvvw0wxx1yyz2z113223434455666777889890091abc2def3ghi4jkl5mno6pqr7stu8vwx9yz11aab2bcc3dd4ee5ff6gg7hh8ii9j0jk1kl2lmm3nnoo4p5pq6qrr7ss8tt9uuvv0wwx1x2yyzz13aba4cbcb5dcdc6dedfef8egf9gfh0ghg1ihi2hji3jik4jkj5lkl6kml7mln8mnm9ono -->\n\n";

    while (ob_get_level())
        ob_end_flush();

    @ob_flush();
    @flush();
}