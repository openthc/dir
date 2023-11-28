<?php
/**
 * View a Single Channel
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Controller\Channel;

class Single extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$pk = $ARG['id'];
		if (empty($pk)) {
			$pk = $_GET['id'];
		}
		if (empty($pk)) {
			_exit_html_fail('<h1>Invalid Request</h1>', 400);
		}

		$data = $this->data;

		$dbc = _dbc();

		$Channel = $dbc->fetchRow('SELECT * FROM channel WHERE id = ?', [ $pk ]);

		// 404
		if (empty($Channel['id'])) {
			_exit_html_warn('<h1>Channel Not Found</h1>', 404, [ 'title' => 'Not Found ']);
		}

		$data['Channel'] = $Channel;
		$data['Page']['title'] = 'Channel :: ' . h($Channel['name']);

		// Company List
		$sql = 'SELECT id, name FROM company WHERE id IN (SELECT company_id FROM company_channel WHERE channel_id = :ch0) ORDER BY id';
		$arg = [ ':ch0' => $Channel['id'] ];
		$data['company_list'] = $dbc->fetchAll($sql, $arg);

		// License List
		$sql = 'SELECT id, name, cre FROM license WHERE id IN (SELECT license_id FROM license_channel WHERE channel_id = :ch0) ORDER BY id';
		$arg = [ ':ch0' => $Channel['id'] ];
		$data['license_list'] = $dbc->fetchAll($sql, $arg);

		// Contact List
		$sql = 'SELECT id, name FROM contact WHERE id IN (SELECT contact_id FROM contact_channel WHERE channel_id = :ch0) ORDER BY id';
		$arg = [ ':ch0' => $Channel['id'] ];
		$data['contact_list'] = $dbc->fetchAll($sql, $arg);

		__exit_text($data);

		// return $RES->write( $this->render('contact/single.php', $data) );

	}

}
