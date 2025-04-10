<?php
/**
 *
 */

namespace OpenTHC\Directory\Test;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class Browser extends \OpenTHC\Test\BaseBrowser
{
	/**
	 *
	 */
	public static function setUpBeforeClass() : void
	{
		// Set custom configuration
		self::$cfg = array_merge([
			'project'          => 'OpenTHC/DIR', // Valid, Preferred if both present
			'projectName'      => 'OpenTHC/DIR', // Valid
			'build'            => 'NO BUILD',
			'buildName'        => 'NO BUILD',
			'sessionName'      => sprintf('DIR %d', getmypid()),
		], self::$cfg);

		// Init BrowserBase
		parent::setUpBeforeClass();
	}
}
