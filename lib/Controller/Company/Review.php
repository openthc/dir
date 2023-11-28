<?php
/**
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Controller\Company;

class Review extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		_acl_exit($_SESSION['Contact']['id'], 'company', 'review');

		$sql = <<<SQL
		SELECT DISTINCT length(company.name) AS cnl, company.id, company.name, company.type, company.guid, company.updated_at, license.code AS license_code
		FROM company
		LEFT JOIN license ON company.id = license.company_id
		WHERE 1 = 1
		SQL;

		if (!empty($_GET['cre'])) {
			$sql.= ' AND company.cre = :cre';
			// $sql.= " AND license.code LIKE 'DA%'";
			$arg[':cre'] = $_GET['cre'];
		}

		// switch ($_GET['v']) {
		// case 'no-address':
		//
		// 	$sql.= ' AND (license.address_full IS NULL OR length(license.address_full) = 0)';
		// 	$arg = array();
		//
		// case 'ts-create':
		// case 'new':
		//
		// 	$sql.= ' ORDER BY company.updated_at DESC';
		//
		// 	break;
		//
		// case 'recent-update':
		// default:
		//
		$sql.= ' ORDER BY company.updated_at ASC';

		//$sql = "SELECT * FROM company WHERE updated_at IS NOT NULL AND updated_at >= '2016-12-01' ORDER BY updated_at ASC LIMIT 100";

		//	$sql.= ' WHERE created_at >= ? ORDER BY created_at DESC LIMIT 100';
		//	$arg = array('0', strftime('%Y-%m-%d', $_SERVER['REQUEST_TIME'] - 86400));
		//
		//	$sql.= ' WHERE created_at >= ? ORDER BY created_at DESC';
		//	$arg = array(strftime('%Y-%m-%d', $_SERVER['REQUEST_TIME'] - 86400));
		//

		//$sql.= ' WHERE cre = ? AND updated_at = created_at AND updated_at >= ? AND updated_at <= ? ORDER BY created_at DESC';
		//$arg = array('usa/wa', '2017-05-04 18:30', '2017-05-04 19:10');

		//$sql.= ' WHERE cre = ? ORDER BY created_at DESC';
		//$arg = array('usa/wa');

		// 	break;
		// }

		$sql.= ' LIMIT 100';

		$dbc = $this->_container->DB;
		$res = $dbc->fetchAll($sql, $arg);

		$data = array(
			'Page' => array(
				'title' => '<a href="/company">Company</a> :: Review',
			),
			'company_list' => $res
		);

		return $RES->write( $this->render('company/review.php', $data) );

	}
}
