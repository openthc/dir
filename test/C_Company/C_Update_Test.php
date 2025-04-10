<?php
/**
 *
 */

namespace OpenTHC\Directory\Test\C_Company;

class C_Update_Test extends \OpenTHC\Directory\Test\Base
{
	function test_update() : void {

		$res = $this->post('/api/company/01CNPVNGBEHPXD0C8GJDHWVB0J', [ 'form_params' => [
			'name' => 'TEST COMPANY UPDATE',
		]]);
		$this->assertValidResponse($res, 403);
	}

}
