<?php
/**
 * Company Autocomplete
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Controller\API\Company;

use Edoceo\Radix\DB\SQL;

class Autocomplete extends \OpenTHC\Directory\Controller\API\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		session_write_close();

		if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
			// if ('OpenTHC SOMETHING_SECRET' == $_SERVER['HTTP_AUTHORIZATION']) {
			// 	$_SESSION['show-address'] = true;
			// }
		}

		$q = trim($_GET['term']);
		if (empty($q)) {
			return $RES->withJSON([
				'data' => null,
				'meta' => [ 'note' => 'No Search Terms Provided [ACA-026]' ]
			], 400);
		}

		// Second Query, Searching
		$q2 = $q;
		if (strlen($q2) <= 2) {
			$q2 = "{$q2}%";
		} else {
			$q2 = "%{$q2}%";
		}

		$cre = $this->getCRE();

		$ret = [];
		$dbc = _dbc();

		// Exact Match Finder
		$sql = <<<SQL
SELECT DISTINCT id AS company_id, cre AS company_cre, guid AS company_guid, name AS company_name FROM company
WHERE (name = :q0 OR guid = :q0)
ORDER BY company_name
LIMIT 10
SQL;
		$arg = [ ':q0' => $q ];
		$res = $dbc->fetchAll($sql, $arg);
		foreach ($res as $rec) {
			$x = $this->_rec_transform($rec);
			$ret[$rec['company_id']] = $x;
		}


		// Pattern Finder
		$sql = <<<SQL
SELECT DISTINCT id AS company_id, cre AS company_cre, guid AS company_guid, name AS company_name FROM company
WHERE (name ILIKE :q0 OR guid ILIKE :q0)
ORDER BY company_name
LIMIT 10
SQL;
		$arg = [ ':q0' => $q2 ];
		$res = $dbc->fetchAll($sql, $arg);
		foreach ($res as $rec) {
			$x = $this->_rec_transform($rec);
			$ret[$rec['company_id']] = $x;
		}


		// Exact Match Finder
		$sql = <<<SQL
SELECT DISTINCT company_id, company_cre, company_guid, company_name FROM company_license
WHERE (company_name = :q0 OR license_name = :q0 OR license_code = :q0 OR license_guid = :q0)
ORDER BY company_name
LIMIT 10
SQL;
		$arg = [ ':q0' => $q ];
		$res = $dbc->fetchAll($sql, $arg);
		foreach ($res as $rec) {
			$x = $this->_rec_transform($rec);
			$ret[$rec['company_id']] = $x;
		}


		$sql = <<<SQL
SELECT DISTINCT company_id, company_cre, company_guid, company_name FROM company_license
WHERE (company_name ILIKE :q0 OR license_name ILIKE :q0 OR license_code ILIKE :q0 OR license_guid ILIKE :q0)
ORDER BY company_name
LIMIT 10
SQL;
		$arg = [ ':q0' => $q2 ];
		$res = $dbc->fetchAll($sql, $arg);
		foreach ($res as $rec) {
			$x = $this->_rec_transform($rec);
			$ret[$rec['company_id']] = $x;
		}

		// if (preg_match('/^\w(\d{5,6})$/', $q, $m)) {
		// 	$q = $m[1];
		// }

		// $res = _search_v1($q, array('company.cre' => $cre));
		// foreach ($res as $rec) {
		// 	$rec = $this->_rec_transform($rec);
		// 	$ret[] = $rec;
		// }

		return $RES->withJSON($ret);

	}

	private function _rec_transform($rec)
	{
		$n = sprintf('%s #%s', $rec['company_name'], $rec['company_guid']);
		$n = trim($n, '# ');
		if (!empty($rec['company_cre'])) {
			$n.= sprintf(' (%s)', $rec['company_cre']);
		}

		$rec = array(
			'label' => $n,
			'value' => $rec['company_guid'],
			'company' => array(
				'id' => $rec['company_id'],
				'name' => $rec['company_name'],
				'guid' => $rec['company_guid'],
			),
		);

		return $rec;

	}
}
