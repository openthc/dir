<?php
/**
 * Search for Company (& License)
 */

namespace OpenTHC\Directory\Controller\API\License;

class Search extends \OpenTHC\Directory\Controller\API\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		if (empty($_GET)) {
			return $RES->withJSON([
				'data' => null,
				'meta' => [ 'detail' => 'Invalid Search Request [ALS#018]' ],
			], 400);
		}

		$p = $REQ->getAttribute('Service');
		if (empty($p)) {
			return $RES->withStatus(403);
		}

		return $this->findLicense($RES);

	}

	function findLicense($RES)
	{
		$sql = <<<SQL
SELECT license.*
, license.id AS license_id
FROM license
LEFT JOIN company ON license.company_id = company.id
WHERE license.id = :q0 OR license.guid = :q0 OR license.name ILIKE :q1
ORDER BY license.name
SQL;

		$arg = [
			':q0' => $_GET['q'],
			':q1' => sprintf('%%%s%%', $_GET['q']),
		];

		$dbc = _dbc();
		$res = $dbc->fetchAll($sql, $arg);

		// print_r($sql);
		// print_r($arg);
		// print_r($res);
		// exit;

		$out = [];
		$out['data'] = [];
		$out['meta'] = [];

		foreach ($res as $rec) {
			$lic = $this->inflate_license($rec);
			$lic['stat'] = $rec['stat'];
			unset($lic['meta']);
			unset($lic['address_meta']);
			// unset($lic['ubi16']);
			$lic['ubi16'] = '-hidden-';
			$out['data'][] = $lic;
		}
		//

		_exit_json($out);

		// if (!empty($_GET['license'])) {
		//
		// 	$arg = array();
		//
		// 	$cre = $this->getCRE();
		// 	if (!empty($cre)) {
		// 		$arg['company.cre'] = $cre;
		// 	}
		//
		// 	if (!empty($_GET['company'])) {
		// 		$arg['company.guid'] = $_GET['company'];
		// 	}
		//
		// 	// By Full GUID
		// 	$arg['license.guid'] = $_GET['license'];
		// 	$arg['license.name'] = $_GET['license'];
		//
		// 	$res = $this->query($arg);
		// 	if (1 == count($res)) {
		// 		$l = $this->inflate_license($res[0]);
		// 		_exit_json(array($l));
		// 	}
		//
		// 	// Try by Code
		// 	unset($arg['license.guid']);
		// 	$arg['license.code'] = $_GET['license'];
		//
		// 	$res = $this->query($arg);
		// 	if (1 == count($res)) {
		// 		$l = $this->inflate_license($res[0]);
		// 		_exit_json(array($l));
		// 	}
		//
		// 	// Try by Code, No Prefix
		// 	unset($arg['license.guid']);
		// 	$arg['license.code'] = preg_replace('/^(E|G|J|M|R|Z)/', null, $_GET['license']);
		//
		// 	$res = $this->query($arg);
		// 	if (1 == count($res)) {
		// 		$l = $this->inflate_license($res[0]);
		// 		_exit_json(array($l));
		// 	}
		//
		// 	// License, No Company
		//
		// 	unset($arg['company.guid']);
		// 	unset($arg['license.guid']);
		// 	unset($arg['license.code']);
		//
		// 	// $arg['license.guid'] = $_GET['license'];
		//
		// 	$res = $this->query($arg);
		// 	_exit_json([
		// 		'meta' => [],
		// 		'data' => $res,
		// 	]);
		// 	// if (1 == count($res)) {
		// 	// 	$l = $this->inflate_license($res[0]);
		// 	// 	_exit_json([
		// 	// 		'meta' => [],
		// 	// 		'data' => $l,
		// 	// 	]);
		// 	// }
		// }
		//
		// _exit_json(array('status' => 'failure'), 404);
		//
		// $sql_try_list = array();
		//
		// // Generic Search Term
		// $q = trim($_GET['q']);
		// if (!empty($q)) {
		//
		// 	$f = strtok($q, ':');
		// 	$one = false;
		//
		// 	switch ($f) {
		// 	case 'kind':
		// 	case 'type':
		//
		// 		$q = strtok('');
		// 		$sql_where = array();
		// 		$sql_where = 'type = ?';
		// 		$arg = array($q);
		//
		// 		break;
		//
		// 	case 'l':
		// 	case 'lic':
		// 	case 'lic6':
		// 	case 'license':
		// 		$one = true;
		// 		$q = strtok('');
		// 		$q = substr($q, 0, 6);
		// 		$sql_where = array();
		// 		$sql_where = 'license.code = ?';
		// 		$arg = array($q);
		// 		break;
		// 	case 'guid':
		// 		$one = true;
		// 		$q = strtok('');
		// 		$q = substr($q, 0, 9);
		// 		$sql_where = array();
		// 		$sql_where = 'guid = ?';
		// 		$arg = array($q);
		// 		break;
		//
		// 	default:
		// 		$f = null;
		// 		$sql_where = array();
		// 		$sql_where[] = 'company.name LIKE ?';
		// 		$sql_where[] = 'license.guid LIKE ?';
		// 		$sql_where = implode(' OR ', $sql_where);
		// 		$q = "%$q%";
		// 		$arg = array($q, $q, $q);
		//
		// 		$sql_where = '(company.name ILIKE ? OR company.guid LIKE ?)';
		// 		$arg = array($q, $q);
		// 	}
		//
		// }

		//if (!empty($_GET['license']) && !empty($_GET['address'])) {
        //
		//	$one = true;
        //
		//	$sql_where = array();
		//	$sql_where[] = 'license.code = ?';
		//	$sql_where[] = 'license.address_full = ?';
		//	$sql_where = implode(' AND ', $sql_where);
		//	$arg = array($_GET['license'], $_GET['address']);
        //
		//	$sql_try_list[] = array(
		//		'sql' => $sql_where,
		//		'arg' => $arg,
		//	);
        //
		//}
		if (!empty($_GET['address'])) {
			// Alert Not Handled?
		}

		if (!empty($_GET['license'])) {

			$one = true;

			$sql_where = array();
			$sql_where[] = '(license.code = :l0 OR license.code = :l1 OR license.guid = :l2)';
			$sql_where = implode(' AND ', $sql_where);
			$arg = array(
				':l0' => $_GET['license'],
				':l1' => preg_replace('/^(G|J|L|M|R)/', null, $_GET['license']),
				':l2' => $_GET['license'],
			);

			$sql_try_list[] = array(
				'sql' => $sql_where,
				'arg' => $arg,
			);

		}

		if (empty($sql_try_list)) {
			//return $RES->withJSON(array(
			_exit_json([
				'data' => null,
				'meta' => [ 'detail' => 'Invalid Search Request [MLS#142]' ],
				//'_try' => $sql_try_list,
				//'_sql' => $sql,
				//'_res' => print_r($res, true),
			], 400);
		}


		// Lookup
		$sql_fields = array(
			'company.id AS company_id',
			'license.id AS license_id',
			'company.type',
			'company.name',
			'company.phone',
			'company.email',
			'company.guid AS company_code',
			'license.code AS license_code',
			'license.guid AS license_guid',
			'license.type AS license_type',
			'license.geo_lat',
			'license.geo_lon',
			'license.meta',
		);

		//print_r($sql_try_list);
		//exit;

		$dbc = _dbc();

		foreach ($sql_try_list as $i => $sql_where) {

			$sql = 'SELECT ';
			$sql.= implode(', ', $sql_fields);
			//$sql.= ' company.*, license.*';
			$sql.= ' FROM company';
			$sql.= ' JOIN license ON company.id = license.company_id';
			$sql.= sprintf(' WHERE %s', $sql_where['sql']);
			$sql.= ' ORDER BY company.id ASC';

			$res = $dbc->fetchAll($sql, $sql_where['arg']);

			if (!empty($res)) {
				if (count($res) > 0) {
					break; // Stop Trying
				}
			}

		}

		if (empty($res)) {
			_exit_json(array(
				'status' => 'failure',
				'result' => 'Nothing Found',
				'_sql' => $sql_try_list,
			), 404);
		}

		$ret = null;

		if ($one) {

			if (count($res) > 1) {
				__exit_text('Conflict', 409);
//				header('Content-Type: text/plain');
//				print_r($res);
//				header('Content-Type: text/plain');
//					'status' => 'failure',
//					'result' => 'Conflict',
//					'_try' => $sql_try_list,
//					'_sql' => $sql,
//					'_res' => print_r($res, true),
//				), 409);

			} else {

				$ret = _license_inflate_rec($res[0]);

			}

		} else {

			foreach ($res as $rec) {

				$rec = _license_inflate_rec($rec);
				$ret[] = $rec;

			}

		}

		if ($ret) {
			_exit_json($ret);
		}

	}

	function query($where)
	{
		$sql.= ' SELECT company.guid AS company_guid';
		$sql.= ' , license.guid AS license_guid';
		$sql.= ' , license.code AS license_code';
		$sql.= ' , license.*';
		$sql.= ' FROM company';
		$sql.= ' JOIN license ON company.id = license.company_id';
		$sql.= ' WHERE ';

		$tmp = array();
		foreach ($where as $k => $v) {
			$a = sprintf(':a%08x', crc32($k));
			$tmp[] = sprintf('%s = %s', $k, $a);
			$arg[$a] = $v;
		}
		$sql.= implode(' AND ', $tmp);

		$dbc = _dbc();
		$res = $dbc->fetchAall($sql, $arg);

		return $res;
	}

	/**

	*/
	function inflate_license($src)
	{

		$rec = array(
			'id' => $src['license_id'],
			'name' => $src['name'],
			'code' => $src['code'],
			'guid' => $src['guid'],
			'type' => $src['type'],
			'meta' => $src['meta'],
			'address' => $src['address_full'],
			'address_meta' => $src['address_meta'],
			'geo' => null,
			'ubi16' => null, // @deprecated
			'company' => array(
				'id' => $src['company_id'],
				'guid' => $src['company_code'],
			),
		);

		if (!empty($src['geo_lat'])) {
			$rec['geo'] = array(
				'lat' => $src['geo_lat'],
				'lon' => $src['geo_lon'],
			);
		}

		$rec['meta'] = json_decode($rec['meta'], true);
		if (!is_array($rec['meta'])) {
			$rec['meta'] = json_decode($rec['meta'], true);
			//print_r($rec);
		}
		if (!is_array($rec['meta'])) {
			$rec['meta'] = json_decode($rec['meta'], true);
			//print_r($rec);
		}

		$rec['ubi16'] = $rec['meta']['ubi16'];

		return $rec;
	}
}
