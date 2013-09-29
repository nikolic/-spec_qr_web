<?php

require_once __DIR__.'/../vendor/autoload.php';

require_once __DIR__.'/../vendor/phpqrcode/phpqrcode.php';

$app = new Silex\Application();

$images_path = __DIR__."/temp/";

$app->get('/hello', function() {
    return 'Hello!!!';
});

$app->get('/qrcode', function() {

	$PNG_WEB_DIR = 'temp/';
	$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR;

	if (!file_exists($PNG_TEMP_DIR)){
		 mkdir($PNG_TEMP_DIR, 777);
	}
       
	$filename = $PNG_WEB_DIR.uniqid().".png";

	QRcode::png('code data text', $filename);

    return $filename;
});

$app->get('/nikolic', function() use ($app) {

	$dd = array('success' => 1, 
				'level' => "HARD"
		);

    return $app->json($dd);
});

$app->run();

?>
