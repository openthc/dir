<?php
/**
 *
 */

namespace App\Controller\Company;

class Main extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$data = [];
		return $RES->write( $this->render('company/main.php', $data) );
	}

}
