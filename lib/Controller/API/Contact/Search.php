<?php
/**
 * Contact Search Controller
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Controller\API\Contact;

class Search extends \OpenTHC\Directory\Controller\API\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$sql_filter_c = array();
		$sql_filter_v = array();

		if (!empty($_GET['q'])) {
			if (empty($_GET['email'])) {
				$_GET['email'] = $_GET['q'];
			}
			// if (empty($_GET['phone'])) {
			// 	$_GET['phone'] = $_GET['q'];
			// }
		}

		if (!empty($_GET['company'])) {
			$sql_filter_c[] = 'contact.company_id = :com';
			$sql_filter_v[':com'] = $_GET['company'];
		}

		if (!empty($_GET['email'])) {

			$e = $_GET['email'];
			$e = strtolower(trim($e));

			$sql_filter_c[] = 'contact.email = :e';
			$sql_filter_v[':e'] = $e;

		}

		if (!empty($_GET['phone'])) {
			$p = \_phone_e164($_GET['phone']);
			$sql_filter_c[] = 'contact.phone = :p';
			$sql_filter_v[':p'] = $p;
		}

		if (empty($sql_filter_c)) {
			return $RES->withStatus(204);
		}

		$sql_filter_c[] = 'contact.stat NOT IN (404, 410)';

		$sql = 'SELECT * FROM contact';
		$sql.= ' WHERE ' . implode(' AND ', $sql_filter_c);
		$sql.= ' ORDER BY name';
		$sql.= ' LIMIT 10';

		$dbc = $this->_container->DB;
		$res = $dbc->fetchAll($sql, $sql_filter_v);
		if (0 == count($res)) {
			_exit_json([
				'meta' => [ 'detail' => 'Not Found [ACS#058]' ],
				'data' => null,
			], 404);
		}

		// detect http://www.iana.org/assignments/media-types/application/vnd.api+json vs application/json
		return $RES->withJSON([
			'meta' => [],
			'data' => $res,
		], 200);

	}
}
