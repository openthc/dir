<?php
/**
 * Create a new Company
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Controller\Company;

use Edoceo\Radix\Session;

class Create extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{

		$data = array(
			'Page' => array(
				'title' => 'Company :: Create',
			),
		);
		$_ENV['h1'] = 'Company :: Create';


		if (empty($_SESSION['Contact']['id'])) {
			Session::flash('info', 'Please Sign In');
			$r = 'https://directory.openthc.com/company/create?' . http_build_query($_GET);
			return $RES->withRedirect('/auth?' . http_build_query(array('r' => $r)));
		}


		// Action
		switch ($_POST['a']) {
		case 'save':

			$C = array(); // new Company();
			$C['id'] = _ulid();
			// $C['cre'] = '?';
			$C['name'] = $_POST['company-name'];
			// $C['type'] = $_POST['company-type'];
			$C['email'] = strtolower(trim($_POST['email']));
			$C['phone'] = strtolower($_POST['phone']);
			// $C['weblink_meta'] = array('website' => $_POST['website']);
			$C['address_full'] = $_POST['address_full'];

			// Submit to our API
			$api = \OpenTHC\Service('dir');
			$res = $api->post('/api/company', $C);

			return $RES->withRedirect(sprintf('/company/%s', $C['id']));

			break;
		}

		$C = array();
		if (!empty($_GET['n'])) {
			$C['name'] = trim($_GET['n']);
		}

		// Company Type Options
		$dbc = _dbc();
		$Company_Type_list = array();
		$res = $dbc->fetchAll('SELECT count(id) AS c, type FROM company GROUP BY type ORDER BY 1');
		foreach ($res as $rec) {
			$Company_Type_list[ $rec['type'] ] = sprintf('%s (%d)', $rec['type'], $rec['c']);
		}
		$data['company_type_list_json'] = json_encode(array_keys($Company_Type_list));

		return $RES->write( $this->render('company/create.php', $data) );

	}
}
