<?php
/**
 * Company Search
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Controller\API\Company;

use Edoceo\Radix;

use OpenTHC\Directory\License;

require_once(APP_ROOT . '/controller/search.php');

class Search extends \OpenTHC\Directory\Controller\API\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		if (empty($_GET)) {
			return $RES->withJSON([
				'data' => null,
				'meta' => [ 'note' => 'Invalid Search Request [ACS#021]' ],
			], 400);
		}

		$sql_try_list = array();

		// Generic Search Term
		$q = trim($_GET['q']);
		if (!empty($q)) {

			$f = strtok($q, ':');
			$one = false;

			switch ($f) {
			case 'guid':

				$one = true;
				$q = strtok('');
				$sql_where = array();
				$sql_where = 'guid = ?';
				$arg = array($q);

				if (preg_match('/^wa\-(\d+)\-(\d+)$/', $q, $m)) {
					$sql_where = 'bizid = ? AND license.code = ?';
					$arg = array($m[1], $m[2]);
				}

				break;

			case 'kind':
			case 'type':

				$q = strtok('');
				$sql_where = array();
				$sql_where = 'type = ?';
				$arg = array($q);

				break;

			case 'l':
			case 'lic':
			case 'lic6':
			case 'license':

				$one = true;
				$q = strtok('');
				$q = substr($q, 0, 6);
				$sql_where = array();
				$sql_where = 'license.code = ?';
				$arg = array($q);

				break;

			case 'guid':
			case 'ubi':
				$one = true;
				$q = strtok('');
				$q = substr($q, 0, 9);
				$sql_where = array();
				$sql_where[] = 'guid = ?';
				$arg = array($q);
				break;

			default:

				$f = null;
				$sql_where = array();
				$sql_where[] = 'company.name ILIKE ?';
				$sql_where[] = 'license.guid ILIKE ?';
				$sql_where = implode(' OR ', $sql_where);
				$q = "%$q%";
				$arg = array($q, $q, $q);

				$sql_where = '(company.name ILIKE ? OR company.guid ILIKE ?)';
				$arg = array($q, $q);
			}

			$sql_try_list[] = array(
				'sql' => $sql_where,
				'arg' => $arg,
			);

		}

		if (!empty($_GET['company']) && !empty($_GET['license'])) {

			$one = true;

			$_GET['license'] = preg_replace('/[^\d]+/', null, $_GET['license']);

			$sql_where = array();
			$sql_where[] = 'company.guid = ?';
			$sql_where[] = 'license.code = ?';
			$sql_where = implode(' AND ', $sql_where);
			$arg = array($_GET['company'], $_GET['license']);

			$sql_try_list[] = array(
				'sql' => $sql_where,
				'arg' => $arg,
			);

		}

		if (!empty($_GET['license'])) {

			$one = true;

			$sql_where = array();
			$sql_where[] = 'license.code = :guid';
			$sql_where = implode(' AND ', $sql_where);
			$arg = array(
				':guid' => $_GET['license'],
			);

			$sql_try_list[] = array(
				'sql' => $sql_where,
				'arg' => $arg,
			);

		}

		if (empty($sql_try_list)) {
			_exit_json([
				'data' => null,
				'meta' => [ 'note' => 'Invalid Search Request [MCS#142]' ],
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
			'license.meta',
		);

		$dbc = _dbc();

		// var_dump($sql_try_list); exit;

		foreach ($sql_try_list as $i => $sql_where) {

			$sql = 'SELECT ';
			$sql.= implode(', ', $sql_fields);
			//$sql.= ' company.*, license.*';
			$sql.= ' FROM company';
			$sql.= ' LEFT JOIN license ON company.id = license.company_id';
			$sql.= sprintf(' WHERE %s', $sql_where['sql']);
			$sql.= ' ORDER BY company.id ASC';

			$res = $dbc->fetchAll($sql, $sql_where['arg']);
			if (!empty($res)) {
				if (count($res) > 0) {
					break; // Stop Trying
				}
			}

		}

		if ($one) {

			if (count($res) > 1) {
				header('Content-Type: text/plain');
				print_r($res);
				exit;
				_exit_json(array(
					'status' => 'failure',
					'result' => 'Conflict',
					'_try' => $sql_try_list,
					'_sql' => $sql,
					'_res' => print_r($res, true),
				), 409);
			} else {
				$ret = _company_inflate_rec($res[0]);
			}

		} else {

			foreach ($res as $rec) {

				$rec = _company_inflate_rec($rec);

				$ret[] = $rec;
			}

		}

		if ($ret) {
			_exit_json([
				'data' => $ret,
				'meta' => [],
			]);
		}

		_exit_json(array(
			'data' => null,
			'meta' => [ 'note' => 'Nothing Found' ],
		), 404);
	}
}

function _company_inflate_rec($rec)
{
	$flag = $rec['license_flag'];
	if ($flag & License::FLAG_DEAD) {
		$flag_a[] = 'CLOSED';
	}

	$rec['meta'] = json_decode($rec['meta'], true);
	$rec['ubi16'] = $rec['meta']['ubi16'];

	return $rec;
}
