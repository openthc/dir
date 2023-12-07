<?php
/**
 * View a Single License
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Controller\License;

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
			_exit_html_fail('<h1>Invalid Request [CLS-025]', 400);
		}

		$dbc = _dbc();

		// Try a few ways to find it
		$License = new License($dbc);
		$License->loadBy('id', $pk);
		if (empty($License['id'])) {
			$License->loadBy([
				'guid' => $pk,
				'stat' => 200,
			]);
		}
		if (empty($License['id'])) {
			$License->loadBy([
				'code' => $pk,
				'stat' => 200,
			]);
		}
		if (empty($License['id'])) {
			$License->loadBy([
				'guid' => $pk,
			]);
		}
		if (empty($License['id'])) {
			_exit_html_warn('<h1>License Not Found [CLS-040]</h1>', 404, [ 'title' => 'Not Found ']);
		}

		// Redirect to use PK
		if ($pk != $License['id']) {
			$url = sprintf('/license/%s', $License['id']);
			return $RES->withRedirect($url, 301);
		}

		$Company = new Company($dbc, $License['company_id']);

		// Channel List
		$channel_list = $dbc->fetchAll('SELECT * FROM channel WHERE id IN (SELECT channel_id FROM license_channel WHERE license_id = :l0)', [
			':l0' => $License['id'],
		]);

		// Public Keys
		$sql = <<<SQL
		SELECT id, created_at, expires_at, meta
		FROM license_public_key
		WHERE license_id = :l0
		  AND deleted_at IS NULL
		ORDER BY id
		SQL;
		$pks = $dbc->fetchAll($sql, [
			':l0' => $License['id']
		]);

		// HTML Output
		$data = [
			'Page' => [
				'title' => sprintf('License :: %s', h($License['name'])),
			],
			'Company' => $Company,
			'License' => $License,
			'License_Channel_list' => $channel_list,
			'License_Public_Key_list' => $pks
		];

		return $RES->write( $this->render('license/single.php', $data) );
	}

}
