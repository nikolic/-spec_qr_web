<?php

require_once __DIR__.'/../vendor/autoload.php';

require_once __DIR__.'/../vendor/phpqrcode/phpqrcode.php'; 


$app = new Silex\Application();

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_mysql',
        'user' => 'sourceco_api',
        'password' => 'qwe123',
        'dbname' => 'sourceco_api',
        'charset'   => 'utf8',
    ),
));

/*** ROUTES ***/

$app->get('/ping', function() use ($app) {
    return 'Pong !!!';
});

$app->get('/generate_qrcode/{id}', function($id) use ($app){

    /* Validation start */

    $MAX_LENGTH = 200;

    if( strlen($id) > $MAX_LENGTH ){
        return $app->json(array('success' => 0, 
                        'url' => NULL,
                        'error' => "Text length must be less than ".$MAX_LENGTH." !"
            ));
    }

     /* create code_exist? function */
    $checkQuery = "SELECT COUNT( * ) AS  `exist` FROM " 
                    . "`qr_codes` WHERE  `text` =  ? ";

    $result = $app['db']->fetchAssoc($checkQuery, array($id)); 

    // check text length

    if( $result['exist'] > 0 ){
        return $app->json(array('success' => 0, 
                        'url' => NULL,
                        'error' => "QRcode already exist!"
            ));
    }
     /* create code_exist? function */

    /* Validation end */

    $PNG_WEB_DIR = 'temp/';
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;

	if (!file_exists($PNG_TEMP_DIR)){
		 mkdir($PNG_TEMP_DIR, 777);
	}

    $filename = uniqid().".png";
	$save_path = $PNG_WEB_DIR.$filename;

	QRcode::png($id, $save_path);

	$sql = "INSERT INTO `sourceco_api`.`qr_codes` (`id`, `text`, `filename`, `used`, `created`)
 			VALUES (NULL, '" . $id . "', '" . $filename . "', '0', CURRENT_TIMESTAMP);";

    $res = $app['db']->query($sql);	

    //return $save_path;
    return $app->json(array('success' => 1, 
                            'url' => $_SERVER["HTTP_HOST"]."/silex/web/".$save_path
                ));
});


$app->get('/mark_as_used/{code}', function($code) use ($app) {

    /* create code_exist? function */
    $checkQuery = "SELECT COUNT( * ) AS  `exist` FROM " 
                . "`qr_codes` WHERE  `text` =  ? ";

    $result = $app['db']->fetchAssoc($checkQuery, array($code)); 

    // check text length

    if( $result['exist'] == 0 ){
        return $app->json(array('success' => 0, 
                        'error' => "QRcode doesn't exist!"
            ));
    }
     /* create code_exist? function */

    // Update used field 

    $updateSql = "UPDATE `sourceco_api`.`qr_codes` SET `used` = '1'"
                    ." WHERE `qr_codes`.`text` = '".$code."'";

    $updateSuccess = $app['db']->query($updateSql); 

    return $app->json(array('success' => 1, 'text' => $code));
});

$app->run();

?>
