<?php

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();

$app->get('/hello', function() {
    return 'Hello!!!';
});

$app->get('/nikolic', function() use ($app) {

	$dd = array('success' => 1, 
				'level' => "HARD"
		);

    return $app->json($dd);
});

$app->run();

?>
