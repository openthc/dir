<?php
/**
 * License Autocomplete
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Controller\API\License;

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

		$_GET['term'] = trim($_GET['term']);
		$_GET['type'] = trim($_GET['type']);
		$_GET['iso3166'] = trim($_GET['iso3166']);

		if (empty($_GET['term'])) {
			return $RES->withJSON([
				'data' => null,
				'meta' => [ 'note' => 'No Search Terms Provided [VAL-020]' ]
			], 400);
		}

		$ret = [];

		$dbc = _dbc();

		$sql_query = <<<SQL
SELECT * FROM company_license
WHERE {SQL_WHERE}
ORDER BY license_name
LIMIT 25
SQL;

		$sql_query = <<<SQL
SELECT * FROM license
WHERE {SQL_WHERE}
ORDER BY name
LIMIT 25
SQL;

		$sql_filter = $this->buildFilter();

		$arg = [];
		$sql_where = [];
		foreach ($sql_filter as $k => $v) {
			$sql_where[] = $v['sql'];
			$arg[$k] = $v['val'];
		}
		$sql_where = implode(' AND ', $sql_where);

		$sql = str_replace('{SQL_WHERE}', $sql_where, $sql_query);

		$res = $dbc->fetchAll($sql, $arg);
		foreach ($res as $rec) {
			// Have to transform first to make sure license_id is full
			// $rec = $this->_rec_transform($rec);
			$n = sprintf('#%s - %s - %s', $rec['code'], $rec['name'], $rec['address_full']);
			$n = trim($n, ' -');
			$rec = array(
				'id' => $rec['id'], // @deprecated
				'label' => $n,
				'value' => $rec['code'],
				'license' => array(
					'id' => $rec['id'],
					'name' => $rec['name'],
					'code' => $rec['code'],
					'guid' => $rec['guid'],
					'type' => $rec['type'],
					'stat' => $rec['stat'],
					'address' => $rec['address_full'],
					'latitude' => $rec['geo_lat'],
					'longitude' => $rec['geo_lon'],
				),
				'company' => array(
					'id' => $rec['company_id'],
					// 'guid' => $rec['company_guid'],
					// 'name' => $rec['company_name'],
					// 'stat' => $rec['company_stat'],
					// 'type' => $rec['company_type'],
				),
			);

			$ret[$rec['id']] = $rec;
		}

		// $arg = [];
		// $sql_where = [];
		// foreach ($sql_filter as $k => $v) {
		// 	$sql_where[] = $v['sql'];
		// 	$arg[$k] = $v['val'];
		// }
		// $sql_where = implode(' AND ', $sql_where);
		// $sql = str_replace('{SQL_WHERE}', $sql_where, $sql_query);

		// $res = $dbc->fetchAll($sql, $arg);
		// foreach ($res as $rec) {
		// 	$x = $this->_rec_transform($rec);
		// 	$ret[$rec['license_id']] = $x;
		// }

		if ('_debug' == $_GET['_debug']) {
			$ret = [
				'data' => $ret,
				'meta' => [
					'sql' => $sql,
					'sql_filter' => $sql_filter,
					'sql_params' => $arg,
				]
			];
		}

		return $RES->withJSON($ret);

	}

	/**
	 *
	 */
	function buildFilter()
	{
		$sql_filter = [];

		// Search Exact ID Match
		if (strlen($_GET['term']) == 26) {
			$sql_filter[':pk079'] = [
				// 'sql' => '(company_id = :pk079 OR license_id = :pk079)',
				'sql' => '(company_id = :pk079 OR id = :pk079)',
				'val' => $_GET['term'],
			];

			return $sql_filter;

		}

		// Filter Status
		$sql_filter[':s0'] = [
			// 'sql' => 'license_stat IN (100, :s0)',
			'sql' => 'stat IN (100, :s0)',
			'val' => 200,
		];

		/*
		switch ($_GET['stat']) {
			case '100':
			case '200':
			case '400':
				break;
			case '*':
				$sql_filter[':s0'] = [
					'sql' => 'stat IN (100, 200, 204, 410)',
				];
		}
		*/

/*
		// Filter CRE
		$cre = $this->getCRE();
		if (!empty($cre)) {
			switch ($cre) {
				case 'openthc':
					unset($sql_filter[':cre']);
					// $sql_filter[':cre0'] = [
					// 	'sql' => 'cre NOT LIKE :cre',
					// 	'val' => 'usa/%',
					// ];
					// $sql_filter[':cre1'] = [
					// 	'sql' => 'cre NOT LIKE :cre',
					// 	'val' => '%can%',
					// ];

					// if (empty($_GET['iso3166'])) {
					// 	$sql_filter[':iso3166'] = [
					// 		'sql' => 'iso3166 IS NULL',
					// 	];
					// }
					// if (!empty($_GET['iso3166'])) {
					// 	$sql_filter[':iso3166'] = [
					// 		'sql' => 'iso3166 = :iso3166',
					// 		'val' => $_GET['iso3166'],
					// 	];
					// }
					break;
				case 'usa/ok':
				case 'usa/ok/openthc':
				case 'usa/ok/test':
					$sql_filter[':cre'] = [
						// 'sql' => 'license_cre = :cre',
						'sql' => 'cre = :cre',
						'val' => 'usa/ok',
					];
					break;
				case 'usa/wa/ccrs':
					$sql_filter[':cre'] = [
						'sql' => 'cre = :cre',
						'val' => 'usa/wa',
					];
					break;
				default:
					$sql_filter[':cre'] = [
						// 'sql' => 'license_cre = :cre',
						'sql' => 'cre = :cre',
						'val' => $cre,
					];
			}

		}
*/

		// Filter Type
		if (!empty($_GET['type'])) {
			$sql_filter[':t1'] = [
				// 'sql' => 'license_type = :t1',
				'sql' => 'type = :t1',
				'val' => $_GET['type'],
			];
		}

		// Search Exact Match
		// $sql_filter[':q0'] = [
		// 	'sql' => '(company_name = :q0 OR license_name = :q0 OR license_code = :q0 OR license_guid = :q0)',
		// 	'val' => $_GET['term'],
		// ];

		// Second Query
		$q2 = $_GET['term'];
		if (strlen($q2) <= 2) {
			$q2 = "{$q2}%";
		} else {
			$q2 = "%{$q2}%";
		}

		$sql_filter[':q0'] = [
			// 'sql' => '(company_name ILIKE :q0 OR license_name ILIKE :q0 OR license_code ILIKE :q0 OR license_guid ILIKE :q0)',
			'sql' => '(name ILIKE :q0 OR code ILIKE :q0 OR guid ILIKE :q0)',
			'val' => $q2,
		];

		return $sql_filter;

	}


	function _search_match_not_open($dbc, $q)
	{
		$sql_filter = [];
		// $sql_filter[':s0'] = [
		// 	'sql' => 'license_stat = :s0',
		// 	'val' => 200,
		// ];
		$sql_filter[':q0'] = [
			'sql' => '(company_name ILIKE :q0 OR license_name ILIKE :q0 OR license_code ILIKE :q0 OR license_guid ILIKE :q0)',
			'val' => $q,
		];

		$cre = $this->getCRE();
		if (!empty($cre)) {
			$sql_filter[':cre'] = [
				'sql' => 'license_cre = :cre',
				'val' => $cre,
			];
		}

		$arg = [];
		$sql_where = [];
		foreach ($sql_filter as $k => $v) {
			$sql_where[] = $v['sql'];
			$arg[$k] = $v['val'];
		}
		$sql_where = implode(' AND ', $sql_where);

		$sql = <<<SQL
SELECT * FROM company_license
WHERE $sql_where
ORDER BY license_name
LIMIT 25
SQL;
		$sql = str_replace("\n", ' ', $sql);
		// syslog(LOG_DEBUG, $sql);
		// syslog(LOG_DEBUG, json_encode($arg));

		$res = $dbc->fetchAll($sql, $arg);

		return $res;

	}

	/**
	 * Transform the License Record
	 */
	function _rec_transform($rec)
	{
		// if (empty($rec['license_id'])) {
		// 	$rec['license_id'] = $rec['company_id'];
		// }
		// if (empty($rec['license_name'])) {
		// 	$rec['license_name'] = $rec['company_name'];
		// }
		// if (empty($rec['license_stat'])) {
		// 	$rec['license_stat'] = $rec['company_stat'];
		// }

		// $n = sprintf('%s #%s', $rec['license_name'], $rec['license_code']);
		//if (!empty($_SESSION['show-address'])) {
		//	if (!empty($rec['license_address'])) {
			$n = sprintf('#%s - %s - %s', $rec['license_code'], $rec['license_name'], $rec['license_address_full']);
		//	}
		//}
		// $n = trim($n, '# ');
		if (200 != $rec['license_stat']) {
			$n = sprintf('%s #%s - CLOSED? - %s', $rec['license_name'], $rec['license_code'], $rec['license_address_full']);
		}

		$x = array(
			// 'id' => $rec['license_id'], // @deprecated
			// 'name' => $rec['license_name'], // @deprecated
			'label' => $n,
			'value' => $rec['license_code'],
			'license' => array(
				'id' => $rec['license_id'],
				'name' => $rec['license_name'],
				'code' => $rec['license_code'],
				'guid' => $rec['license_guid'],
				'type' => $rec['license_type'],
				'stat' => $rec['license_stat'],
				'address' => $rec['license_address_full'],
				'latitude' => $rec['license_geo_lat'],
				'longitude' => $rec['license_geo_lon'],
			),
			'company' => array(
				'id' => $rec['company_id'],
				'guid' => $rec['company_guid'],
				'name' => $rec['company_name'],
				'stat' => $rec['company_stat'],
				'type' => $rec['company_type'],
			),
		);
		return $x;
	}

}
