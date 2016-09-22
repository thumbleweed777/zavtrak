<?php
if (!empty($_SERVER['QUERY_STRING'])){
$re_addr=$_SERVER['QUERY_STRING'];
header('Location: '.$re_addr);
}
?>