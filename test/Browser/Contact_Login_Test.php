<?php
/**
 * Login test
 */

namespace OpenTHC\Test\Browser;

use Facebook\WebDriver\WebDriverBy;

class Contact_Login_Test extends \OpenTHC\Directory\Test\Browser
{
	/**
	 * This may be an incomplete list
	 */
	function test_config()
	{
		$key_list = [ 'id', 'origin', 'public', 'secret' ];
		$cfg = \OpenTHC\Config::get('openthc/sso');
		foreach ($key_list as $k) {
			$this->assertNotEmpty($cfg[ $k ]);
		}

		$dbc_auth = _dbc('auth');
		$cx = $dbc_auth->fetchRow('SELECT * FROM auth_context WHERE code = :co', [':co' => 'dir']);
		$this->assertNotEmpty($cx['code']);

		$Auth_Service = $dbc_auth->fetchRow('SELECT * FROM auth_service WHERE id = :pk', [':pk' => $cfg['id'] ]);
		$cx = $Auth_Service['context_list'];
		$cx = explode(' ', $cx);
		$this->assertContains('dir', $cx);
	}

	/**
	 *
	 */
	function test_sign_in()
	{
		$url = sprintf('%s/auth/open', $_ENV['OPENTHC_TEST_ORIGIN']);
		$this->getPage($url);

		$this->assertMatchesRegularExpression('/sso.+\/auth\/open\?_=[\w\-\_]+$/', self::$wd->getCurrentURL());

		$e = $this->findElement(WebDriverBy::name('username'));
		$e->clear();
		$e->sendKeys($_ENV['OPENTHC_TEST_CONTACT0_USERNAME']);

		$e = $this->findElement(WebDriverBy::name('password'));
		$e->sendKeys($_ENV['OPENTHC_TEST_CONTACT0_PASSWORD']);

		// $e = $this->findElement('#exec-sign-in');
		$e = $this->findElement(WebDriverBy::xpath('//button[contains(text(), "Sign In")]'));
		$e->click();

		$url = self::$wd->getCurrentURL();
		echo "\nURL2:$url\n";
		$this->assertMatchesRegularExpression('/sso.+\/oauth2\/authorize\?/', self::$wd->getCurrentURL());
		self::$wd->executeScript("window.scrollBy(0, 500)");
		$this->findElement('#oauth2-authorize-permit')->click();

		$url = self::$wd->getCurrentURL();
		echo "\nURL3:$url\n";
		$this->assertMatchesRegularExpression('/sso.+\/oauth2\/permit\?_=[\w\-\_]+$/', self::$wd->getCurrentURL());
		self::$wd->executeScript("window.scrollBy(0, 500)");
		$this->findElement('#oauth2-permit-continue')->click();

		$url = self::$wd->getCurrentURL();
		$this->assertMatchesRegularExpression('/search$/', $url);

		return true;
	}

}
