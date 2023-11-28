<?php
/**
 * Home Page
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Controller;

class Home extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		$data = [];
		return $RES->write( $this->render('home.php', $data) );
	}

}
