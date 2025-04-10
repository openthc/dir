<?php
/**
 *
 */

namespace OpenTHC\Directory\Test\C_Company;

class B_Single_Test extends \OpenTHC\Directory\Test\Base
{
	function test_company_system() : void {

		$res = $this->get('/api/company/018NY6XC00C0MPANY000000000');
		$res = $this->assertValidResponse($res);
		$this->assertIsArray($res);
		$this->assertArrayHasKey('data', $res);
		$this->assertArrayHasKey('meta', $res);

		$this->assertIsArray($res['data']);
		// $this->assertCount(12, $l);
	}

	function test_company_orphan() : void {

		$res = $this->get('/api/company/018NY6XC00C0MPANY000000001');
		$res = $this->assertValidResponse($res);
		$this->assertIsArray($res);
		$this->assertArrayHasKey('data', $res);
		$this->assertArrayHasKey('meta', $res);

		$this->assertIsArray($res['data']);
		// $this->assertCount(12, $l);

	}

	function test_single() : void {

		$res = $this->get('/api/company/018NY6XC000ZK21YCA08SWDE7S');
		$this->assertValidResponse($res);
	}

	function test_single_404() : void {

		$res = $this->get('/api/company/FOUR_ZERO_FOUR');
		$res = $this->assertValidResponse($res, 404);

	}

}
