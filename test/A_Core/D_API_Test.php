<?php
/**
 *
 */

namespace OpenTHC\Directory\Test\A_Core;

class D_API_Test extends \OpenTHC\Directory\Test\Base
{
	function test_api() : void {

		$res = $this->get('/api');

		$raw = $res->getBody()->getContents();
		$this->assertEquals(200, $res->getStatusCode());
		$this->assertEquals('text/html', strtolower(strtok($res->getHeaderLine('content-type'), ';')));
		$this->assertEmpty($res->getHeaderLine('set-cookie'));
		// $this->assertValidResponse($res, 200, 'text/html');

	}

	function test_api_not_found() : void {

		$res = $this->get('/api/four_zero_four');

		$raw = $res->getBody()->getContents();
		$this->assertEquals(404, $res->getStatusCode());
		$this->assertEquals('text/html', strtolower(strtok($res->getHeaderLine('content-type'), ';')));
		$this->assertEmpty($res->getHeaderLine('set-cookie'));

	}

	function test_search() : void {

		$res = $this->get('/api/search');

		$raw = $res->getBody()->getContents();
		$this->assertEquals(200, $res->getStatusCode());
		$this->assertEquals('application/json', strtolower(strtok($res->getHeaderLine('content-type'), ';')));
		$this->assertEmpty($res->getHeaderLine('set-cookie'));

	}

}
