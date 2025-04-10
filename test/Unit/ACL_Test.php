<?php
/**
 * ACL Facade test
 */

namespace OpenTHC\Directory\Test\Unit;

class ACL_Test extends \OpenTHC\Directory\Test\Base
{
	public function setUp() : void
	{
		$acl = new \OpenTHC\Directory\Facade\ACL([
			'service' => [ 'id' => '' ],
			'contact' => [ 'id' => '' ],
			'company' => [ 'id' => '' ],
			'license' => [ 'id' => '' ]
		]);
	}

	public function test_permit_pass()
	{
		$res = \OpenTHC\Directory\Facade\ACL::permit('dir/test/facade/opa', [
			'service' => [ 'id' => OPENTHC_SERVICE_ID, ]
		]);
		$this->assertTrue($res);
	}

	public function test_permit_fail() {

		// $acl = new ACL($ctx, $dbc, $rdb);

		$res = \OpenTHC\Directory\Facade\ACL::permit('dir/test/facade/opa', [
			'service' => NULL
		]);
		$this->assertFalse($res);
	}

	public function test_permit_or_exit_pass() {

		// @see https://docs.phpunit.de/en/11.3/writing-tests-for-phpunit.html#expecting-exceptions
		// public function expectException(string $exception): void
		// public function expectExceptionCode($code): void
		// public function expectExceptionMessage(string $message): void
		// public function expectExceptionMessageMatches(string $regularExpression): void
		// public function expectExceptionObject(\Exception $exception): void
		$this->expectException(\Exception::class);

		$acl = new ACL();
		// $acl->assert();

		$res = \OpenTHC\Directory\Facade\ACL::assert('dir/test/facade/opa', [
			'service' => [
				'id' => OPENTHC_SERVICE_ID,
			]
		]);
		$this->assertTrue($res);
	}

	public function test_permit_or_exit_fail() {
		$this->expectException(\Exception::class);
		$res = \OpenTHC\Directory\Facade\ACL::assert('dir/test/facade/opa', [
			'service' => [
				'id' => '00FOUR_OH_FOUR00',
			]
		]);
		$this->assertInstanceOf(\Slim\Http\Response::class, $res);
		$this->assertEquals(403, $res->getStatusCode());
		$b = $res->getBody();
		print_r($b->getContents());
		$this->assertNotEmpty($b);
		$this->assertStringContainsString('Access Denied', $b->getContents());
	}

}
