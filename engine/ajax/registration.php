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
 Файл: registration.php
-----------------------------------------------------
 Назначение: AJAX проверки имени
=====================================================
*/

@error_reporting(7);
@ini_set('display_errors', true);
@ini_set('html_errors', false);

define('DATALIFEENGINE', true);
define('ROOT_DIR', '../..');
define('ENGINE_DIR', '..');

include ENGINE_DIR.'/data/config.php';

if ($config['http_home_url'] == "") {

	$config['http_home_url'] = explode("engine/ajax/registration.php", $_SERVER['PHP_SELF']);
	$config['http_home_url'] = reset($config['http_home_url']);
	$config['http_home_url'] = "http://".$_SERVER['HTTP_HOST'].$config['http_home_url'];

}

require_once ENGINE_DIR.'/classes/mysql.php';
require_once ENGINE_DIR.'/data/dbconfig.php';

if ($_COOKIE['dle_skin']) {
	if (@is_dir(ROOT_DIR.'/templates/'.$_COOKIE['dle_skin']))
		{
			$config['skin'] = $_COOKIE['dle_skin'];
		}
}

if ($config["lang_".$config['skin']]) { 

     include_once ROOT_DIR.'/language/'.$config["lang_".$config['skin']].'/website.lng';

} else {

     include_once ROOT_DIR.'/language/'.$config['langs'].'/website.lng';

}
$config['charset'] = ($lang['charset'] != '') ? $lang['charset'] : $config['charset'];

require_once ENGINE_DIR.'/modules/functions.php';
require_once ENGINE_DIR.'/classes/parse.class.php';

$parse = new ParseFilter();


function check_name($name)
{
	global $lang, $db, $banned_info;

	$stop = '';

	if (strlen($name) > 20)
	{
		 
            $stop .= $lang['reg_err_3'];
	}
	if (preg_match("/[\||\'|\<|\>|\"|\!|\?|\$|\@|\/|\\\|\&\~\*\+]/",$name))
	{
		 
            $stop .= $lang['reg_err_4'];
	}
	if (empty($name))
	{
		 
            $stop .= $lang['reg_err_7'];
	}

	if (count($banned_info['name']))
		foreach($banned_info['name'] as $banned){

			$banned['name'] = str_replace( '\*', '.*' ,  preg_quote($banned['name'], "#") );

			if ( $banned['name'] AND preg_match( "#^{$banned['name']}$#i", $name ) ) {

				if ($banned['descr']) {
					$lang['reg_err_21']	= str_replace("{descr}", $lang['reg_err_22'], $lang['reg_err_21']);				
					$lang['reg_err_21']	= str_replace("{descr}", $banned['descr'], $lang['reg_err_21']);				
				} else $lang['reg_err_21']	= str_replace("{descr}", "", $lang['reg_err_21']);

				$stop .= $lang['reg_err_21'];
			}
	}

	if (!$stop)
	{

		$replace_word = array ('e' => '[eеё]', 'r' => '[rг]', 't' => '[tт]', 'y' => '[yу]','u' => '[uи]','i' => '[i1l!]','o' => '[oо0]','p' => '[pр]','a' => '[aа]','s' => '[s5]','w' => 'w','q' => 'q','d' => 'd','f' => 'f','g' => '[gд]','h' => '[hн]','j' => 'j','k' => '[kк]','l' => '[l1i!]','z' => 'z','x' => '[xх%]','c' => '[cс]','v' => '[vuи]','b' => '[bвь]','n' => '[nпл]','m' => '[mм]','й' => '[йиu]','ц' => 'ц','у' => '[уy]','е' => '[еeё]','н' => '[нh]','г' => '[гr]','ш' => '[шwщ]','щ' => '[щwш]','з' => '[з3э]','х' => '[хx%]','ъ' => '[ъь]','ф' => 'ф','ы' => '(ы|ь[i1l!]?)','в' => '[вb]','а' => '[аa]','п' => '[пn]','р' => '[рp]','о' => '[оo0]','л' => '[лn]','д' => 'д','ж' => 'ж','э' => '[э3з]','я' => '[я]','ч' => '[ч4]','с' => '[сc]','м' => '[мm]','и' => '[иuй]','т' => '[тt]','ь' => '[ьb]','б' => '[б6]','ю' => '(ю|[!1il][oо0])','ё' => '[ёеe]','1' => '[1il!]','2' => '2','3' => '[3зэ]','4' => '[4ч]','5' => '[5s]','6' => '[6б]','7' => '7','8' => '8','9' => '9','0' => '[0оo]','_' => '_','#' => '#','%' => '[%x]','^' => '[^~]','(' => '[(]',')' => '[)]','=' => '=','.' => '[.]','-' => '-','[' => '[\[]');
		$name=strtolower($name);
		$search_name=strtr($name, $replace_word);

		$db->query ("SELECT name FROM " . USERPREFIX . "_users WHERE LOWER(name) REGEXP '[[:<:]]{$search_name}[[:>:]]' OR name = '$name'");

        if ($db->num_rows() > 0)
        {
			$stop .= $lang['reg_err_20'];
		}
	}

	if (!$stop) return false; else return $stop;
}

$banned_info = get_vars ("banned");

if (!is_array($banned_info)) {
$banned_info = array ();

$db->query("SELECT * FROM " . USERPREFIX . "_banned");
while($row = $db->get_row()){

	if ($row['users_id']) {

       $banned_info['users_id'][$row['users_id']] = array('users_id' => $row['users_id'], 'descr' => stripslashes($row['descr']), 'date' => $row['date']);

    } else {

		if (count(explode(".", $row['ip'])) == 4) 
			$banned_info['ip'][$row['ip']] = array('ip' => $row['ip'], 'descr' => stripslashes($row['descr']), 'date' => $row['date']);
		elseif (strpos( $row['ip'], "@" ) !== false)
			$banned_info['email'][$row['ip']] = array('email' => $row['ip'], 'descr' => stripslashes($row['descr']), 'date' => $row['date']);
		else
			$banned_info['name'][$row['ip']] = array('name' => $row['ip'], 'descr' => stripslashes($row['descr']), 'date' => $row['date']);

   }

}
set_vars ("banned", $banned_info);
$db->free();
}

$name  = $db->safesql(trim(htmlspecialchars($parse->process(convert_unicode($_POST['name'], $config['charset'])))));
$allow = check_name($name);

if (!$allow)
	$buffer = "<font color=\"green\">".$lang['reg_ok_ajax']."</font>";
else
	$buffer = "<font color=\"red\">".$allow."</font>";

@header("Content-type: text/css; charset=".$config['charset']);
echo $buffer;
?>