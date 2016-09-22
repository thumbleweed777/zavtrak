<?php


class MySQLDump {
	var $tables = array();
	var $connected = false;
	var $output;
	var $droptableifexists = false;
	var $mysql_error;
	



function list_values($tablename, $where) {
	$sql = mysql_query("SELECT * FROM $tablename");
	$this->output .= "\n\n-- Dumping data for table: $tablename\n\n";
	while ($row = mysql_fetch_array($sql)) {
		$broj_polja = count($row) / 2;
		$this->output .= "INSERT INTO `$tablename` VALUES(";
		$buffer = '';
		for ($i=0;$i < $broj_polja;$i++) {
			$vrednost = $row[$i];
			if (!is_integer($vrednost)) { $vrednost = "'".addslashes($vrednost)."'"; } 
			$buffer .= $vrednost.', ';
		}
		$buffer = substr($buffer,0,count($buffer)-3);
		$this->output .= $buffer . ");\n";
	}

    return $this->output;
}


}
?>