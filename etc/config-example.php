<?php
/**
 * OpenTHC Directory Configuration Example
 */

// Init
$cfg = [];

// Database
$cfg['database'] = [
	'hostname' => '127.0.0.1',
	'database' => 'openthc_main',
	'username' => 'openthc_main',
	'password' => 'openthc_main'
];

// Redis
$cfg['redis'] = [
	'hostname' => '127.0.0.1',
	'database' => '0',
	'publish' => 'openthc_dir_pub',
];

// OpenTHC
$cfg['openthc'] = [
	'sso' => [
		'origin' => 'https://sso.openthc.example',
		'client-id' => '/* SOME ULID */',
		'client-sk' => '/* SOME Secret */',
	]
];

return $cfg;
