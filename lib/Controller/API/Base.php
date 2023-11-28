<?php
/**
 * Base Controller
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Controller\API;

class Base extends \OpenTHC\Controller\Base
{
	function __construct($c)
	{
		session_write_close();

		header('Access-Control-Allow-Origin: *');
		header('Content-Type: text/plain');

		parent::__construct($c);

	}

	function getCRE()
	{
		$cre = $_GET['cre'];
		if (empty($cre)) {
			// v2 Legacy
			$cre = $_GET['rce'];
			if (empty($cre)) {
				// v1 Legacy
				$cre = $_GET['rbe'];
			}
		}

		return $cre;
	}

}
