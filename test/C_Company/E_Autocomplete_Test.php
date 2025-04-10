<?php
/**
 *
 */

namespace OpenTHC\Directory\Test\C_Company;

class E_Autocomplete_Test extends \OpenTHC\Directory\Test\Base
{
	function test_empty_request() : void {

		$res = $this->get('/api/autocomplete/company');
		$this->assertValidResponse($res, 400);
	}

	function test_company_autocomplete() : void {

		$res = $this->get('/api/autocomplete/company?term=openthc');
		$res = $this->assertValidResponse($res, 200);
		$this->assertIsArray($res);
		$this->assertCount(16, $res);
	}

}
