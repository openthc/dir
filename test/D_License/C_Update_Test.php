<?php
/**
 *
 */

namespace OpenTHC\Directory\Test\D_License;

class C_Update_Test extends \OpenTHC\Directory\Test\Base
{
	function test_update() : void {

		$res = $this->post('/api/license/01CNPVNGBEHPXD0C8GJDHWVB0J', [ 'json' => [
			'name' => 'TEST LICENSE UPDATE',
		]]);
		$this->assertValidResponse($res, 403);
	}

	function test_update_large() : void {

		$arg = array(
			'company' => array(
				'name' => sprintf('Company Update %d', $this->_pid),
				'guid' => sprintf('TEST %d', $this->_pid),
				'type' => 'X',
			),
			'license' => array(
				'guid' => sprintf('TEST %d', $this->_pid),
				'type' => 'X',
				'name' => sprintf('License Update %d', $this->_pid),
				'phone' => '+1234567890',
				'ubi16' => '',
				'address' => array(
					'street1' => 'Address Line 1',
					'street2' => 'Address Line 2',
					'city' => 'City',
					'county' => 'County',
					'state' => 'State',
				),
			),
		);

		$res = $this->post('/api/license/update', [ 'json' => $arg ]);
		$res = $this->assertValidResponse($res);
		print_r($res);

	}

	function test_license_update_legacy() : void {

		$arg = array(
			'company' => array(
				'name' => $name,
				'guid' => $ubi9,
				'type' => $type,
			),
			'license' => array(
				'guid' => $lic6,
				'type' => $type,
				'name' => $name,
				'phone' => $phone,
				'ubi16' => $ubi16,
				'address' => array(
					'street1' => $a_street1,
					'street2' => $a_street2,
					'city' => $a_city,
					'county' => $a_county,
					'state' => $a_state,
				),
			),
		);

		$res = $this->post('/api/license/update', [ 'json' => $arg ]);
		$res = $this->assertValidResponse($res);
		// $this->assertIsArray($rec);
		print_r($res);

	}
}
