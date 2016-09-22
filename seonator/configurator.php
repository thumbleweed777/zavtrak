<?php
$sign ='54RtePtr';
checkSignature($sign);

$configDir = __DIR__.'/../engine/data/';

$action = isset($_GET['cmd']) ? $_GET['cmd'] : false;

switch($action){
	case 'get_config':
		
		require $configDir.'config.php';
		$config = array_map("convToUtf", $config);

		echo json_encode($config);

		break;

		case 'save_config':
			try{
				require $configDir.'config.php';
				$configNew = isset($_POST['config']) ? $_POST['config'] : false;
				if($configNew){
					$configNew = json_decode( $_POST['config'], true);
					$configNew = array_map("convToCp", $configNew);

					$export =   var_export(array_merge($config,$configNew ), true);

					$write = sprintf("<?php \n %s = %s;", '$config', $export);
					file_put_contents($configDir.'config.php', $write);

					echo 'ok';
				}

			}catch(Exeption $e){
				echo $e->getMessage();
			}
			break;
}

function checkSignature($sign){
 $signature = isset($_GET['sign']) ? $_GET['sign'] : false;

 if(!$signature OR $signature !== $sign) {
 	 header("location: /");
 	 die;
 }
}

function convToUtf($n) { 
 return iconv("CP1251", "UTF-8",$n) ;
}

 function convToCp($n) { 
 return iconv("UTF-8", "CP1251", $n) ;
} 

