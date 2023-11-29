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

function _dbc($conn=null)
{
	static $dbc = null;

	if (empty($dbc)) {
		$cfg = \OpenTHC\Config::get('database');
		$dsn = sprintf('pgsql:host=%s;dbname=%s', $cfg['hostname'], $cfg['database']);
		$dbc = new \Edoceo\Radix\DB\SQL($dsn, $cfg['username'], $cfg['password']);
	}

	return $dbc;
}
