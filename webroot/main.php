<?php
/**
 * OpenTHC Main Controller
 */

use Edoceo\Radix;
use Edoceo\Radix\Session;

// We may start with an error code from the PHP interpreter
$e0 = error_get_last();

header('Cache-Control: no-cache, must-revalidate');
header('Content-Language: en');

// Mangle SERVER data
require_once(dirname(dirname(__FILE__)) . '/boot.php');

\OpenTHC\Config::init(APP_ROOT);

$cfg = [];
// $cfg['debug'] = true;
$app = new \OpenTHC\App($cfg);

$con = $app->getContainer();

// API
$app->group('/api', 'App\Module\API');


// Browse
// $app->get('/browse', 'App\Controller\Browse')
// 	->add('App\Middleware\Menu')
// 	->add('OpenTHC\Middleware\Session');


// Map
$app->get('/map', 'App\Controller\Map')
	->add('App\Middleware\Menu')
	->add('OpenTHC\Middleware\Session');


// Search
$app->get('/search', 'App\Controller\Search')
	->add('App\Middleware\Menu')
	->add('OpenTHC\Middleware\Session');


// $app->get('/labs', function($REQ, $RES, $ARG) {
// 	return $RES->withRedirect('/search?type=Laboratory');
// });
// // $app->get('/laboratories', 'App\Controller\Search:lab');
// // $app->get('/medical', 'App\Controller\Medical');


// Core System Objects
$app->group('/company', 'App\Module\Company')
	->add('App\Middleware\Menu')
	->add('OpenTHC\Middleware\Session');

$app->group('/contact', 'App\Module\Contact')
	->add('App\Middleware\Menu')
	->add('OpenTHC\Middleware\Session');

$app->group('/license', 'App\Module\License')
	->add('App\Middleware\Menu')
	->add('OpenTHC\Middleware\Session');


// Authentication
$app->group('/auth', function() {

	$this->get('', 'App\Controller\Auth\oAuth2\Open'); // @deprecated path?
	$this->get('/open', 'App\Controller\Auth\oAuth2\Open');
	$this->map(['GET', 'POST'], '/connect', 'App\Controller\Auth\Connect');
	$this->get('/back', 'App\Controller\Auth\oAuth2\Back');
	$this->get('/fail', 'OpenTHC\Controller\Auth\Fail');
	$this->get('/ping', 'OpenTHC\Controller\Auth\Ping');
	$this->get('/shut', 'OpenTHC\Controller\Auth\Shut');

})
->add('App\Middleware\Menu')
->add('OpenTHC\Middleware\Session');


// Run the App
$ret = $app->run();


exit(0);
