<?php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app['debug'] = true;

$app = new Silex\Application();
$app->register(new DerAlex\Silex\YamlConfigServiceProvider(__DIR__ . '/settings.yml'));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));


$app->get('/login/', function (Request $request) use ($app) {


	$client_url =  $_SERVER['HTTP_HOST'];
	if( $app['config']['client_url'][$client_url]['url'] == $client_url ) {

		$encodeData = $request->query->get('data');
		$salt = $app['config']['secret'];

                $decodeData = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($encodeData), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));

                $userParams = json_decode($decodeData);
		$pathImageCardFront = $app['config']['client_url'][$client_url]['path_image_card_front'];
		$pathImageCardBack = $app['config']['client_url'][$client_url]['path_image_card_back'];


		return $app['twig']->render('index.twig', array(
		       'user' => $userParams,
		       'pathImageCardFront' => $pathImageCardFront,
          	       'pathImageCardBack' => $pathImageCardBack,			
                ));
	} else {
		 return $app['twig']->render('error.twig', array(
                       'message' => "sitio web no encontrado",
                ));
	}
});



$app->run();
