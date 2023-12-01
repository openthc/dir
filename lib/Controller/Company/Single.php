<?php
/**
 * View a Single Company
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Controller\Company;

use OpenTHC\Company;
use OpenTHC\License;

class Single extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		$pk = $ARG['id'];
		if (empty($pk)) {
			$pk = $_GET['id'];
		}
		if (empty($pk)) {
			_exit_html_fail('<h1>Invalid Request</h1>', 400);
		}

		$dbc = _dbc();

		// Try like heck to find it
		$Company = new Company($dbc, $pk);
		if (empty($Company['id'])) {
			$Company->findBy([ 'guid' => $pk ]);
		}
		if (empty($Company['id'])) {
			// 404
			_exit_html_warn('<h1>Company Not Found</h1>', 404, [ 'title' => 'Not Found ']);
		}

		// JSON Request?
		$path = $REQ->getUri()->getPath();
		if ('.json' == substr($path, -5)) {
			__exit_json([
				'id' => $Company['id'],
				'code' => $Company['guid'],
				'guid' => $Company['guid'],
				'name' => $Company['name'],
				'type' => $Company['type'],
				'cre' => $Company['cre'],
				// '_source' => $Company,
			]);
		}

		$file = 'company/single.php';
		$data = [
			'Company' => $Company
		];

		// Find Licenses
		$sql = 'SELECT id, name, address_full, code, guid, flag, stat, type, geo_lat, geo_lon, meta FROM license WHERE company_id = ? ORDER BY license.stat, license.code';
		$sql = 'SELECT * FROM license WHERE company_id = ? ORDER BY license.stat, license.code';
		$arg = array($Company['id']);
		$data['License_List'] = $dbc->fetchAll($sql, $arg);
		// $data['License'] = $License_List[0];

		$data['Contact_List'] = $this->_get_contact_list($dbc, $Company);

		return $RES->write( $this->render($file, $data) );

	}

	/**
	 *
	 */
	function _get_contact_list($dbc, $Company)
	{
		// From the Auth+Main Database
		$sql = <<<SQL
		SELECT auth_contact.id, auth_contact.username AS name, auth_contact.username AS email, NULL AS phone, 'auth_0' AS contact_type
		FROM auth_contact WHERE company_id = :c0

		UNION ALL

		SELECT auth_contact.id, auth_contact.username AS name, auth_contact.username AS email, NULL AS phone, 'auth_1' AS contact_type
		FROM auth_contact WHERE id IN (SELECT contact_id FROM auth_company_contact WHERE company_id = :c0)

		UNION ALL

		SELECT contact.id, contact.name AS name, contact.email, contact.phone, 'base_0' AS contact_type
		FROM contact WHERE company_id = :c0

		ORDER BY 1, 2, 3, 5

		SQL;

		$sql = <<<SQL
		SELECT contact.id, contact.name AS name, contact.email, contact.phone, 'base_0' AS contact_type
		FROM contact
		WHERE company_id = :c0
		UNION
		SELECT contact.id, contact.name AS name, contact.email, contact.phone, 'link_0' AS contact_type
		FROM contact
		WHERE id IN (SELECT contact_id FROM company_contact WHERE company_id = :c0)
		ORDER BY 1, 2, 3, 5
		SQL;

		$arg = [ ':c0' => $Company['id'] ];

		//$res = $dbc->fetchAll($sql, $arg);
		$res = [];

		$contact_list = [];
		foreach ($res as $rec) {
			if (empty($contact_list[ $rec['id'] ])) {
				$contact_list[ $rec['id'] ] = $rec;
			} else {
				$v0 = $contact_list[ $rec['id'] ];
				if ($v0['name'] != $rec['name']) {
					$rec['name'] = sprintf('%s (%s)', $v0['name'], $rec['name']);
				}
				// if ($v0['name'] != $rec['name']) {
				// 	$rec['name'] = sprintf('%s (%s)', $v0['name'], $rec['name']);
				// }
				if ($v0['email'] != $rec['email']) {
					$rec['email'] = sprintf('%s (%s)', $v0['email'], $rec['email']);
				}
				if ($v0['phone'] != $rec['phone']) {
					$rec['phone'] = sprintf('%s (%s)', $v0['phone'], $rec['phone']);
				}
				$rec['contact_type'] = sprintf('%s/%s', $v0['contact_type'], $rec['contact_type']);
				$contact_list[ $rec['id'] ] = $rec;
			}
		}

		return $contact_list;
	}

}
