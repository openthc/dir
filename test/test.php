#!/usr/bin/env php
<?php
/**
 * OpenTHC Directory Test Runner
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

require_once(dirname(__DIR__) . '/boot.php');

// Default Option
if (empty($_SERVER['argv'][1])) {
	$_SERVER['argv'][1] = 'phpunit';
	$_SERVER['argc'] = count($_SERVER['argv']);
}


// Command Line
$doc = <<<DOC
OpenTHC Directory Test Runner

Usage:
	test <command> [options]
	test phpunit
	test phpstan
	test phplint

Options:
	--filter=<FILTER>   Some Filter for PHPUnit
	--phpunit=<PHPUNIT> Inject arguments directly into PHPUnit

DOC;

$res = \Docopt::handle($doc, [
	'exit' => false,
	'optionsFirst' => true,
]);
// var_dump($res);
$cli_args = $res->args;
// var_dump($cli_args);


// Test Config
$cfg = [];
$cfg['base'] = APP_ROOT;
$cfg['site'] = 'dir';

$test_helper = new \OpenTHC\Test\Helper($cfg);
$cfg['output'] = $test_helper->output_path;


// Call PHPCS?
// \OpenTHC\Test\Linter::execute();


// PHPLint
if ($cli_args['phplint']) {
	$tc = new \OpenTHC\Test\Facade\PHPLint($cfg);
	$res = $tc->execute();
	var_dump($res);
}


// PHPStan
if ($cli_args['phpstan']) {
	$tc = new \OpenTHC\Test\Facade\PHPStan($cfg);
	$res = $tc->execute();
	var_dump($res);
}


// Psalm/Psalter?


// PHPUnit
// Pick Config File
$cfg_file_list = [];
$cfg_file_list[] = sprintf('%s/phpunit.xml', __DIR__);
$cfg_file_list[] = sprintf('%s/phpunit.xml.dist', __DIR__);
foreach ($cfg_file_list as $f) {
	if (is_file($f)) {
		$cfg['--configuration'] = $f;
		break;
	}
}
// Filter?
if ( ! empty($cli_args['--filter'])) {
	$cfg['--filter'] = $cli_args['--filter'];
}
// if ( ! empty($cli_args['--phpunit'])) {
// 	$arg[] = $cli_args['--phpunit'];
// }

$tc = new \OpenTHC\Test\Facade\PHPUnit($cfg);
$res = $tc->execute();
switch ($res['code']) {
case 0:
case 200:
	echo "\nTEST SUCCESS\n";
	break;
case 1:
case 2:
case 400:
case 500:
	echo "\nTEST FAILURE\n";
	echo $res['data'];
	break;
default:
	echo "\nTEST UNKNOWN ($res)\n";
	break;
}


// Output
$res = $test_helper->index_create($res['data']);
echo "TEST COMPLETE\n  $res\n";
