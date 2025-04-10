<?php
/**
 *
 */

namespace OpenTHC\Directory\Test\D_License;

class B_Single_Test extends \OpenTHC\Directory\Test\Base
{
	function test_license_system() : void {

		$res = $this->get('/api/license/018NY6XC00L1CENSE000000000');
		$res = $this->assertValidResponse($res);
		$this->assertIsArray($res);
		$this->assertArrayHasKey('data', $res);
		$this->assertArrayHasKey('meta', $res);

		$this->assertIsArray($res['data']);
		$this->assertCount(14, $res['data']);

	}

	function test_license_orphan() : void {

		$res = $this->get('/api/license/018NY6XC00L1CENSE000000001');
		$res = $this->assertValidResponse($res);
		$this->assertIsArray($res);
		$this->assertArrayHasKey('data', $res);
		$this->assertArrayHasKey('meta', $res);

		$this->assertIsArray($res['data']);
		$this->assertCount(14, $res['data']);

	}

	/**
	 *
	 */
	function test_license_not_found() : void {

		$res = $this->get('/api/license/FOUR_ZERO_FOUR');
		$res = $this->assertValidResponse($res, 404);
		$this->assertIsArray($res);
		$this->assertArrayHasKey('data', $res);
		$this->assertArrayHasKey('meta', $res);

	}

}
