<?php
/**
 * Main License View
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Controller\License;

class Main extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = [];
		return $RES->write( $this->render('license/main.php', $data) );
	}
}
