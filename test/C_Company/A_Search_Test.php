<?php
/**
 *
 */

namespace OpenTHC\Directory\Test\C_Company;

class A_Search_Test extends \OpenTHC\Directory\Test\Base
{
	function test_search() : void {

		$res = $this->get('/api/company');
		$this->assertValidResponse($res, 400);


		$res = $this->get('/api/company?q=OPENTHC');
		$res = $this->assertValidResponse($res);
		$this->assertIsArray($res);
	}

}
