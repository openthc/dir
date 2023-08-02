<?php
/**
 * Search Controller
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Controller;

class Search extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		$dbc = _dbc();

		$_GET['q'] = trim($_GET['q']);
		$_GET['cre'] = trim($_GET['cre']);
		$_GET['type'] = trim($_GET['type']);

		$cre_list = \OpenTHC\CRE::getEngineList();

		$data = array(
			'Page' => array('title' => 'Directory'),
			'Search' => array('q' => $_GET['q']),
			'acl_edit' => _acl($_SESSION['Contact']['id'], 'company', 'update'),
			'map_link' => '/map?' . \http_build_query($_GET),
			'cre_pick' => $_GET['cre'],
			'cre_list' => $cre_list,
			'result_list' => [],
		);

		// Try some Magic things here
		// Likely Email So, Search That
		if (preg_match('/\w@\w/', $_GET['q'])) {
			if (_acl($_SESSION['Contact']['id'], 'contact', 'search')) {
				// $sql = 'SELECT id AS object_id, 'contact' AS object_type '
			}
		}

		// All Digits?
		// Could be a Phone Number
		if (preg_match('/^\d+$/', $_GET['q'])) {
			if (_acl($_SESSION['Contact']['id'], 'contact', 'search')) {
				// $sql = 'SELECT id AS object_id, 'contact' AS object_type '
			}
		}


		$sql_query = <<<SQL
		SELECT *,
			id AS object_id,
			tb AS object_type,
			ftxt AS name
			-- type AS object_sub_type
		FROM search_full_text
		WHERE {WHERE}
		ORDER BY name, object_type, type, stat
		OFFSET 0
		LIMIT 50
		SQL;
		// Table "public.search_full_text"
		// Column |         Type          | Collation | Nullable | Default
		// --------+-----------------------+-----------+----------+---------
		// id     | character varying(26) |           | not null |
		// tb     | text                  |           |          |
		// ftxt   | text                  |           |          |
		// ftsv   | tsvector              |           |          |
		// cre    | character varying(32) |           |          |
		// type   | character varying(64) |           |          |
		// stat   | integer               |           |          |
		// flag   | integer               |           |          |


		// $sql_query = <<<SQL
		// SELECT *
		// FROM object_search
		// WHERE {WHERE}
		// ORDER BY name, object_type, type, stat
		// OFFSET 0
		// LIMIT 25
		// SQL;
		// View "public.object_search"
		// Column    |          Type          | Collation | Nullable | Default
		// -------------+------------------------+-----------+----------+---------
		// object_id   | character varying(26)  |           |          |
		// object_type | text                   |           |          |
		// name        | character varying(256) |           |          |
		// cre         | character varying(32)  |           |          |
		// type        | character varying(64)  |           |          |
		// stat        | integer                |           |          |
		// flag        | integer                |           |          |
		// ftsv        | tsvector               |           |          |


		$sql_where = [];
		$sql_param = [];
		if (!empty($_GET['cre'])) {
			$sql_param[':c0'] = $_GET['cre'];
			$sql_where[] = '(cre IS NULL OR cre = :c0)';
			$_SESSION['search-cre'] = $_GET['cre'];
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

		// If no search terms provided, but filters are present then search all
		// $res = [];
		// if (!empty($_GET['q'])) {

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

		$data['Page']['title'] = 'Directory :: Search :: Results';

		// Add License Type list
		$sql = 'SELECT count(id) AS c, type FROM license WHERE type IS NOT NULL GROUP BY type ORDER BY type ASC';
		$res = $dbc->fetchAll($sql);
		$data['license_type_list'] = $res;
		$data['license_type_pick'] = $_GET['type'];

		return $RES->write( $this->render('search.php', $data) );

	}

}
