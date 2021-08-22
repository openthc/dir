<?php
/**
 * OpenTHC Directory Configuration Example
 */

$cfg = [];

$cfg['database'] = [
	'hostname' => '127.0.0.1',
	'database' => 'openthc_main',
	'username' => 'openthc_main',
	'password' => 'openthc_main'
];

$cfg['redis'] = [
	'hostname' => '127.0.0.1',
	'database' => '0',
	'publish' => 'openthc_dir_pub',
];

$cfg['openthc'] = [
	'sso' => [
		'hostname' => 'sso.openthc.dev',
	]
];

return $cfg;
