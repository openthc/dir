<?php
/**
 *
 */

namespace OpenTHC\Directory\Test\D_License;

class E_Autocomplete_Test extends \OpenTHC\Directory\Test\Base
{
	function test_fire() : void {

		$res = $this->get('/api/autocomplete/license');
		$this->assertValidResponse($res, 400);

		$res = $this->get('/api/autocomplete/license?term=OPENTHC');
		$this->assertValidResponse($res, 200);

	}

	function test_license_autocomplete() : void {

		$res = $this->get('/api/autocomplete/license?term=WeedTraQR');
		$res = $this->assertValidResponse($res, 200);
		$this->assertIsArray($res);
		$this->assertCount(4, $res);
	}

}
