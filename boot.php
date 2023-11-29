<?php
/**
 * OpenTHC Directory Bootstrap
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

define('APP_ROOT', __DIR__);

error_reporting(E_ALL & ~ E_NOTICE);

openlog('openthc-dir', LOG_ODELAY|LOG_PID, LOG_LOCAL0);

require_once(APP_ROOT . '/vendor/autoload.php');

if ( ! \OpenTHC\Config::init(APP_ROOT) ) {
	_exit_html_fail('<h1>Invalid Application Configuration [ALB-035]</h1>', 500);
}

define('OPENTHC_SERVICE_ID', \OpenTHC\Config::get('openthc/dir/id'));
define('OPENTHC_SERVICE_ORIGIN', \OpenTHC\Config::get('openthc/dir/origin'));


function _acl()
{
	return true;
}

/**
 * Database Connection Helper
 */
function _dbc($conn='main')
{
	static $dbc_list = [];

	if ( ! empty($dbc_list[$conn])) {
		return $dbc_list[$conn];
	}

	$dbc = null;

	switch ($conn) {
		case 'auth':
		case 'main':
			$cfg = \OpenTHC\Config::get(sprintf('database/%s', $conn));
			$dsn = sprintf('pgsql:application_name=openthc-dir;host=%s;dbname=%s', $cfg['hostname'], $cfg['database']);
			$dbc = new \Edoceo\Radix\DB\SQL($dsn, $cfg['username'], $cfg['password']);
			break;
		// default:
		// 	$dbc = new \Edoceo\Radix\DB\SQL($conn);
	}

	if ( ! empty($dbc)) {
		$dbc_list[$conn] = $dbc;
	}

	return $dbc;

}
