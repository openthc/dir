<?php
/**
 * Edit Company information
 */

namespace OpenTHC\Directory\Test\Browser;

use Facebook\WebDriver\WebDriverBy;

class Company_Edit_Test extends \OpenTHC\Directory\Test\Browser
{
	// public function test_config()
	// {
	// 	// Required for Service class making OPS requests
	// 	$x = \OpenTHC\Config::get('openthc/ops');
	// 	$this->assertNotEmpty($x['secret']);
	// }

	public function test_data()
	{
		// Log In
		$url = sprintf('%s/auth/open', $_ENV['OPENTHC_TEST_ORIGIN']);
		$this->getPage($url);
		$this->findElement(WebDriverBy::name('username'))->clear()->sendKeys($_ENV['OPENTHC_TEST_CONTACT0_USERNAME']);
		$this->findElement(WebDriverBy::name('password'))->sendKeys($_ENV['OPENTHC_TEST_CONTACT0_PASSWORD']);
		$this->findElement(WebDriverBy::xpath('//button[contains(text(), "Sign In")]'))->click();
		sleep(2);
		$this->findElement('#oauth2-authorize-permit')->click();
		$this->findElement('#oauth2-permit-continue')->click();

		$this->assertTrue(true);
	}

	/**
	 * @depends test_data
	 */
	public function test_edit()
	{
		$url = sprintf('%s/company/update?id=%s', $_ENV['OPENTHC_TEST_ORIGIN'], $_ENV['OPENTHC_TEST_COMPANY_A']);
		$this->getPage($url);

		$e = $this->findElement('[name=weblink_meta-twitter]');
		$e->sendKeys('openthc');

		$e = $this->findElement('[name=a][value=save]');
		$e->click();

		$src = self::$wd->getPageSource();
		$this->assertMatchesRegularExpression('/Company data update submitted/', $src);
	}
}
