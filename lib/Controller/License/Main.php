<?php
/**
 *
 */

namespace App\Controller\License;

class Main extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = [];
		return $RES->write( $this->render('license/main.php', $data) );
	}
}
