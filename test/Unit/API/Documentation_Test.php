<?php
/**
 *
 */

namespace OpenTHC\Directory\Test\Unit\API;

class Documentation_Test extends \OpenTHC\Directory\Test\Base {

	/**
	 * @test
	 */
	function main_page() : void {

		$res = $this->get('/api');
		$html = $this->assertValidResponse($res, 200, 'text/html');

	}

}
