<?php
/**
	Search for Company / License
*/

namespace App\Controller\API;

use Edoceo\Radix\DB\SQL;

require_once(APP_ROOT . '/controller/search.php');

class Search extends \App\Controller\API\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$res = _search_v1($_GET['q'], $_GET);
		$out = array();
		foreach($res as $rec) {

			if (empty($rec['license_type'])) {
				$rec['license_type'] = '?';
			}
			$rec['marker'] = array(
				'color' => \UI_license::color($rec['license_type']),
				'mark' => \UI_license::mark($rec['license_type']),
			);

			$out[] = $rec;
		}

		return $RES->withJSON($out);

	}
}
