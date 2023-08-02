#!/usr/bin/php
<?php
/**
 * Updates the Text Search
 *
 * SPDX-License-Identifier: GPL-3.0-only
 *
 * @see - Search on PostgreSQL - https://news.ycombinator.com/item?id=17638169
 */

/*
Update Company
15082^C

real	17m47.649s
user	0m2.286s
sys	0m1.069s
*/

use Edoceo\Radix\DB\SQL;

require_once(dirname(dirname(__FILE__)) . '/boot.php');

$dbc = _dbc();

// Run the Thing
_search_update_company($dbc);
// _search_update_license($dbc);
// _search_update_contact($dbc);

echo "DONE!\n";

/**
 *
 */
function _search_update_company($dbc)
{
	echo "_search_update_company()\n";

	$sql_upsert = <<<SQL
	INSERT INTO search_full_text (id, tb, stat, flag, cre, ftxt, ftsv)
	VALUES (:pk, 'company', :s1, :f1, :c1, :n1, setweight(to_tsvector(:text_A), 'A') || setweight(to_tsvector(:text_B), 'B'))
	ON CONFLICT (id) DO
	UPDATE SET
		cre = :c1,
		flag = :f1,
		stat = :s1,
		ftxt = :n1,
		ftsv = setweight(to_tsvector(:text_A), 'A') || setweight(to_tsvector(:text_B), 'B')
	SQL;
	$cmd_upsert = $dbc->prepare($sql_upsert);

	$idx = 0;
	// $sql = 'SELECT * FROM company ORDER BY updated_at DESC LIMIT 500';
	$sql = 'SELECT * FROM company';
	$res = $dbc->fetch($sql);
	foreach ($res as $src) {

		$idx++;
		echo "\r$idx";

		if (empty($src['cre'])) {
			$src['cre'] = null;
		}

		$name = _company_name_trim_noise($src['name']);

		$text_A = [];
		$text_A[] = _company_name_trim_noise($src['name']);
		$text_A[] = _company_name_trim_noise($src['name_alt']);
		$text_A[] = _company_name_trim_noise($src['name_cre']);
		$text_A[] = $src['guid'];
		$text_A = array_filter($text_A);

		$text_B = [];
		$text_B[] = $src['email'];
		$text_B[] = preg_replace('/[^\d]+/', null, $src['phone']);
		//$text_B[] = implode(', ', json_decode($src['tags'], true));
		$text_B[] = $src['address_full'];
		$text_B = array_filter($text_B);

		// Update
		$cmd_upsert->execute([
			':pk' => $src['id'],
			':s1' => $src['stat'],
			':f1' => $src['flag'],
			':c1' => $src['cre'],
			':n1' => $src['name'],
			':text_A' => implode(' ', $text_A),
			':text_B' => implode(' ', $text_B),
		]);

	}

	echo "\n";

}

/**
 *
 */
function _search_update_license($dbc)
{
	echo "_search_update_license()\n";

	// Prepare
	$sql_upsert = <<<SQL
	INSERT INTO search_full_text (id, tb, ftxt, ftsv) VALUES (:pk, 'license', :txt0, setweight(to_tsvector(:text_A), 'A') || setweight(to_tsvector(:text_B), 'B'))
	ON CONFLICT (id) DO UPDATE SET ftxt = :txt0, ftsv = setweight(to_tsvector(:text_A), 'A') || setweight(to_tsvector(:text_B), 'B')
	SQL;
	$cmd_upsert = $dbc->prepare($sql_upsert);

	// Spin
	$idx = 0;
	// $sql = 'SELECT * FROM license ORDER BY updated_at DESC LIMIT 500';
	$sql = 'SELECT * FROM license';
	$res = $dbc->fetch($sql);
	foreach ($res as $src) {

		$idx++;
		echo "\r$idx";

		$name = _company_name_trim_noise($src['name']);

		$text_A = [];
		$text_A[] = $src['name'];
		$text_A[] = $src['name_cre'];
		$text_A[] = $src['name_dba'];
		$text_A[] = $src['code'];
		$text_A[] = $src['guid'];
		$text_A = array_filter($text_A);

		$text_B = [];
		$text_B[] = $src['email'];
		$text_B[] = $src['phone'];
		$text_B[] = $src['address_full'];
		$text_B = array_filter($text_B);

		// Update
		$cmd_upsert->execute([
			':pk' => $src['id'],
			':txt0' => implode(', ', $text_A),
			':text_A' => implode(' ', $text_A),
			':text_B' => implode(' ', $text_B),
		]);

	}

	echo "\n";

}

/**
 *
 */
function _search_update_contact($dbc)
{
	echo "_search_update_contact()\n";

	// Upsert Global Search Table
	$sql_upsert = <<<SQL
	INSERT INTO search_full_text (id, tb, ftxt, ftsv)
	VALUES (:pk, 'contact', :txt0, setweight(to_tsvector(:text_A), 'A') || setweight(to_tsvector(:text_B), 'B'))
	ON CONFLICT (id) DO
	UPDATE SET
		ftxt = :txt0,
		ftsv = setweight(to_tsvector(:text_A), 'A') || setweight(to_tsvector(:text_B), 'B')
	SQL;
	$cmd_upsert = $dbc->prepare($sql_upsert);

	// Spin
	$idx = 0;
	$sql = 'SELECT * FROM contact';
	$res = $dbc->fetch($sql);
	foreach ($res as $src) {

		$idx++;
		echo "\r$idx";

		$name = $src['name'];
		if (empty($name)) {
			$name = strtok($src['email'], '@');
		}

		$text_A = [];
		$text_A[] = $src['name'];
		$text_A[] = $src['name_last'];
		$text_A[] = $src['name_first'];
		$text_A = array_filter($text_A);

		$text_B = [];
		$text_B[] = $src['email'];
		$text_B[] = $src['phone'];
		$text_B = array_filter($text_B);

		// Update
		$cmd_upsert->execute([
			':pk' => $src['id'],
			':txt0' => implode(', ', $text_A),
			':text_A' => implode(' ', $text_A),
			':text_B' => implode(' ', $text_B),
		]);

	}

	echo "\n";
}


function _company_name_trim_noise($x)
{
	$x = preg_replace('/[_,:;\/\?\+\"\'\.\-]+/i', ' ', $x);
	$x = preg_replace('/\s(inc|llc|pllc)\b/i', '', $x);
	$x = preg_replace('/ +/', ' ', $x);
	$x = trim($x);
	return $x;
}
