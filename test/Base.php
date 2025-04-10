<?php
/**
 * Test Case Base Class
 */

namespace OpenTHC\Directory\Test;

class Base extends \OpenTHC\Test\Base {

	protected $client; // API Guzzle Client

	protected function setUp() : void {

		$this->client = $this->getGuzzleClient([
			'base_uri' => $_ENV['OPENTHC_TEST_ORIGIN'],
		]);

	}

	function get($url)
	{
		$res = $this->client->get($url);
		return $res;
	}

	function post($url, $arg)
	{
		$res = $this->client->post($url, $arg);
		return $res;
	}

	function delete($url)
	{
		$res = $this->client->delete($url);
		return $res;
	}

}
