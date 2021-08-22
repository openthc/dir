<?php
/**
 * OpenTHC Directory Bootstrap
 */

define('APP_ROOT', __DIR__);

error_reporting(E_ALL & ~ E_NOTICE);

openlog('openthc-dir', LOG_ODELAY|LOG_PID, LOG_LOCAL0);

require_once(APP_ROOT . '/vendor/autoload.php');

\OpenTHC\Config::init(APP_ROOT);

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
