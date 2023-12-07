<?php
/**
 * Edit/Update a License
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Controller\License;

use \Edoceo\Radix\Session;

use OpenTHC\Company;
use OpenTHC\License;

class Update extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		// If Allowed to Update
		if (_acl($_SESSION['Contact']['id'], 'license', 'update')) {

			$data = [];
			return $RES->write( $this->render('license/update.php', $data) );

		}

		$pk = $ARG['id'];
		if (empty($pk)) {
			$pk = $_GET['id'];
		}
		if (empty($pk)) {
			_exit_html_fail('Invalid Request', 400);
		}

		$dbc_main = _dbc();

		$License = new License($dbc_main);
		$License->loadBy('id', $pk);
		if (empty($License['id'])) {
			_exit_html_fail('<h1>Not Found</h1>', 404);
		}

		switch ($_POST['a']) {
			case 'license-update-save':

				// Send to CIC?
				$arg = [
					'company_id' => '018NY6XC00C0MPANY000000001',
					'contact_id' => '018NY6XC00C0NTACT000000001',
					'name' => sprintf('License Update Request %s', $License['name']),
					'meta' => json_encode([
						'License' => $License->toArray(),
						'update' => $_POST,
					])
				];
				$dbc_corp = _dbc('corp');
				$dbc_corp->insert('ticket', $arg);

				Session::flash('info', _('License data update submitted, thank you'));

				return $RES->withRedirect(sprintf('/license/%s', $_GET['id']));
		}

		$data = [];
		return $RES->write( $this->render('license/update-public.php', $data) );

	}
}
