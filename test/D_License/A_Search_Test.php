<?php
/**
 *
 */

namespace OpenTHC\Directory\Test\D_License;

class A_Search_Test extends \OpenTHC\Directory\Test\Base
{
	function test_search() : void {

		$res = $this->get('/api/license');
		$this->assertValidResponse($res, 400);

		$res = $this->get('/api/license?q=OPENTHC');
		$res = $this->assertValidResponse($res, 403);

	}

}
