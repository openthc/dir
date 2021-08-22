<?php
/**
 * Search Controller
 */

namespace App\Controller;

class Search extends \OpenTHC\Controller\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		$dbc = _dbc();

		$cre_list = \OpenTHC\CRE::getEngineList();
		$cre_list = array_filter($cre_list, function($item) {
			return 'test' != basename($item['code']);
		});

		$data = array(
			'Page' => array('title' => 'Directory'),
			'Search' => array('q' => $_GET['q']),
			'acl_edit' => _acl($_SESSION['Contact']['id'], 'company', 'update'),
			'map_link' => '/map?' . \http_build_query($_GET),
			'cre_pick' => $_GET['cre'],
			'cre_list' => $cre_list,
			'result_list' => [],
		);

		$sql_query = <<<SQL
SELECT *
FROM object_search
WHERE {WHERE}
ORDER BY name
OFFSET 0
LIMIT 25
SQL;

		$sql_where = [];
		$sql_param = [];
		if (!empty($_GET['cre'])) {
			$sql_param[':c0'] = $_GET['cre'];
			$sql_where[] = 'cre = :c0';
		}

		if (!empty($_GET['type'])) {
			$sql_param[':t0'] = $_GET['type'];
			$sql_where[] = 'type = :t0';
		}

		if (!empty($_GET['q'])) {
			$sql_param[':q0'] = $_GET['q'];
			$sql_where[] = 'ftsv @@ plainto_tsquery(:q0)';
		}

		if (count($sql_where) && count($sql_param)) {

			$sql_query = str_replace('{WHERE}', implode(' AND ', $sql_where), $sql_query);
			$res = $dbc->fetchAll($sql_query, $sql_param);

			// Single Match, Jump
			if (1 == count($res)) {
				$rec = $res[0];
				switch ($rec['object_type']) {
					case 'company':
					case 'contact':
					case 'license':
						return $RES->withRedirect(sprintf('/%s/%s', $rec['object_type'], $rec['object_id']));
				}
			}

			// Inflate
			foreach ($res as $rec) {

				switch ($rec['object_type']) {
					case 'company':
						$x = new \OpenTHC\Company($dbc, $rec['object_id']);
						$rec['code'] = $x['code'];
						$rec['guid'] = $x['guid'];
						break;
					case 'contact':
						$x = $dbc->fetchRow('SELECT * FROM contact WHERE id = ?', $rec['object_id']);
						$rec['code'] = $x['code'];
						$rec['guid'] = $x['guid'];
						// $x = new \OpenTHC\Contact($dbc, $ser['object_id']);
						break;
					case 'license':
						$x = new \OpenTHC\License($dbc, $rec['object_id']);
						$rec['code'] = $x['code'];
						$rec['guid'] = $x['guid'];
						break;
				}

				$data['result_list'][] = $rec;
			}

		}

		// If no search terms provided, but filters are present then search all?
		// $res = [];
		// if (!empty($_GET['q'])) {

			// $sql = 'SELECT id, type, name FROM full_text WHERE ftsv @@ plainto_tsquery(:q0) ORDER BY type, name LIMIT 25';
			// $res = $dbc->fetchAll($sql, [
				// ':q0' => $_GET['q'], // @todo Sanitize better
			// ]);

			// @todo Query into a temp table
			// Table to Build Report Into
			// $sql = <<<SQL
			// CREATE TEMP TABLE directory_search (
			// )
			// SQL;

			// SQL::query($sql);


			// @todo insert into tmp table directly from the company?

			// @todo select from the tmp table

		// }

		\App_Menu::addMenuItem('page', '/company/create', '<i class="fas fa-plus-square"></i> Create');
		//$_ENV['h1'] = $_ENV['title'] = sprintf('Directory :: Search &gt;%d Businesses', round($_SESSION['directory-count-all'], -3));

		$data['Page']['title'] = 'Directory :: Search :: Results';
		// 	'license_type_list' => array(),
		// 	'license_type_pick' => $_GET['lt'],
		// );

		// Add License Type list
		$sql = 'SELECT count(id) AS c, type FROM license WHERE type IS NOT NULL GROUP BY type ORDER BY 2 ASC';
		$res = $dbc->fetchAll($sql);
		$data['license_type_list'] = $res;
		$data['license_type_pick'] = $_GET['type'];

		return $RES->write( $this->render('search.php', $data) );

	}

}
