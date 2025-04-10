<?php
/**
 */

namespace OpenTHC\Directory\Test\A_Core;

class C_Config_Test extends \OpenTHC\Directory\Test\Base
{
	function test_env(): void
	{
		$key_list = [
			'OPENTHC_TEST_ORIGIN',
			'OPENTHC_TEST_CONTACT0_USERNAME',
			'OPENTHC_TEST_CONTACT0_PASSWORD'
		];
		foreach ($key_list as $key) {
			$this->assertArrayHasKey($key, $_ENV, sprintf('Missing ENV: "%s"', $key));
		}

	}

	/**
	 *
	 */
	function test_openthc() : void
	{

		$cfg = \OpenTHC\Config::get('openthc/dir');
		$this->assertIsArray($cfg);
		$this->assertNotEmpty($cfg['id']);
		$this->assertNotEmpty($cfg['origin']);
		$this->assertNotEmpty($cfg['public']);
		$this->assertNotEmpty($cfg['secret']);

		$cfg = \OpenTHC\Config::get('openthc/sso');
		$this->assertIsArray($cfg);
		$this->assertNotEmpty($cfg['origin']);
		$this->assertNotEmpty($cfg['client-id']);
		$this->assertNotEmpty($cfg['client-sk']);
		$this->assertNotEmpty($cfg['client-pk']);

	}

}
