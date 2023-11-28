<?php
/**
 * OpenTHC Directory Main Controller
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

use Edoceo\Radix;
use Edoceo\Radix\Session;

// We may start with an error code from the PHP interpreter
$e0 = error_get_last();

header('Cache-Control: no-cache, must-revalidate');
header('Content-Language: en');

// Mangle SERVER data
require_once(dirname(dirname(__FILE__)) . '/boot.php');

$cfg = [];
$cfg['debug'] = true;
$app = new \OpenTHC\App($cfg);


// API
$app->group('/api', 'OpenTHC\Directory\Module\API');


// Homepage
$app->get('/home', 'OpenTHC\Directory\Controller\Home');


// Browse
// $app->get('/browse', 'OpenTHC\Directory\Controller\Browse')
// 	->add('OpenTHC\Directory\Middleware\Menu')
// 	->add('OpenTHC\Middleware\Session');


// Map
$app->get('/map', 'OpenTHC\Directory\Controller\Map')
	->add('OpenTHC\Directory\Middleware\Menu')
	->add('OpenTHC\Middleware\Session');


// Search
$app->get('/search', 'OpenTHC\Directory\Controller\Search')
	->add('OpenTHC\Directory\Middleware\Menu')
	->add('OpenTHC\Middleware\Session');


// Core System Objects
$app->group('/company', 'OpenTHC\Directory\Module\Company')
	->add('OpenTHC\Directory\Middleware\Menu')
	->add('OpenTHC\Middleware\Session');

$app->group('/contact', 'OpenTHC\Directory\Module\Contact')
	->add('OpenTHC\Directory\Middleware\Menu')
	->add('OpenTHC\Middleware\Session');

$app->group('/license', 'OpenTHC\Directory\Module\License')
	->add('OpenTHC\Directory\Middleware\Menu')
	->add('OpenTHC\Middleware\Session');

$app->group('/channel', 'OpenTHC\Directory\Module\Channel')
	->add('OpenTHC\Directory\Middleware\Menu')
	->add('OpenTHC\Middleware\Session');



// Authentication
$app->group('/auth', function() {

	$this->get('', 'OpenTHC\Directory\Controller\Auth\oAuth2\Open'); // @deprecated path?
	$this->get('/open', 'OpenTHC\Directory\Controller\Auth\oAuth2\Open');
	$this->map(['GET', 'POST'], '/connect', 'OpenTHC\Directory\Controller\Auth\Connect');
	$this->get('/back', 'OpenTHC\Directory\Controller\Auth\oAuth2\Back');
	$this->get('/fail', 'OpenTHC\Controller\Auth\Fail');
	$this->get('/ping', 'OpenTHC\Controller\Auth\Ping');
	$this->get('/shut', 'OpenTHC\Controller\Auth\Shut');

})
->add('OpenTHC\Directory\Middleware\Menu')
->add('OpenTHC\Middleware\Session');


// Run the App
$ret = $app->run();


exit(0);
