<?php
/**
 * Create a Contact
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Controller\Contact;

use OpenTHC\Contact;

class Main extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		_acl_exit($_SESSION['Contact']['id'], 'contact', 'search');

		$data = array(
			'Page' => array('title' => 'Contacts')
		);

		$sql = <<<SQL
		SELECT id, name, email, phone
		FROM contact
		WHERE stat = 200
		ORDER BY id
		SQL;
		$arg = [];

		$dbc = _dbc();
		$data['contact_list'] = $dbc->fetchAll($sql, $arg);

		return $RES->write( $this->render('contact/main.php', $data) );

	}
}
