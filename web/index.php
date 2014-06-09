<?php


require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app['debug'] = false;

$app = new Silex\Application();
$app->register(new DerAlex\Silex\YamlConfigServiceProvider(__DIR__ . '/settings.yml'));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));


$app->get('/login/', function (Request $request) use ($app) {

	$encodeData = $request->query->get('data');
	$salt = $app['config']['secret'];
	
    $decodeData = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($encodeData), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
    
    $userParams = json_decode($decodeData); 

	return $app['twig']->render('index.twig', array(
        'user' => $userParams,
    ));

});



$app->run();
