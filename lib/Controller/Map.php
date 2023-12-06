<?php
/**
 * A Map of Everyone in the Designated CRE
 *
 * SPDX-License-Identifier: GPL-3.0-only
 *
 * https://stackoverflow.com/questions/6388590/highlighting-borders-of-state-and-cities-of-us-in-google-map-api-3
 * https://www.census.gov/geo/maps-data/data/tiger-kml.html
 * https://productforums.google.com/forum/#!topic/gec-dynamic-data-layers/PmkWPsS6hmg
 * @see https://www.google.com/maps/d/u/0/viewer?mid=1yJUVfG2bjhXeZaouZWe7knt3HhI&hl=en_US&ll=47.712359%2C-117.352552&z=11
 */

namespace OpenTHC\Directory\Controller;

class Map extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		// _acl_exit($_SESSION['Contact']['id'], 'license', 'search-map');
		if (empty($_SESSION['Company']['id'])) {
			// __exit_text('Maps only for authenticated users', 403);
		}

		$data = array(
			'Page' => array('title' => 'Map'),
			'q' => $_GET['q'],
			'center' => [],
			'center_fix' => false,
		);

		switch ($_GET['cre']) {
		case 'usa/ok':
			$_GET['c'] = '35.50,-99.25';
			$data['center_fix'] = true;
			break;
		case 'usa/wa':
			$_GET['c'] = '47.739429,-120.404301';
			$data['center_fix'] = true;
			break;
		}


		if (!empty($_GET['c'])) {

			$x = explode(',', $_GET['c']);
			$c = array();
			$c['lat'] = $x[0];
			$c['lon'] = $x[1];
			$data['center'] = $c;

		} elseif (!empty($_SESSION['map']['center'])) {

			$data['center'] = $_SESSION['map']['center'];
			$data['center_fix'] = true;

		} else {

			// @todo Use MaxMind
			if (empty($_SESSION['geoip'])) {
				$_SESSION['geoip'] = geoip_record_by_name($_SERVER['REMOTE_ADDR']);
			}

			$center = array(
				'lat' => $_SESSION['geoip']['latitude'],
				'lon' => $_SESSION['geoip']['longitude'],
			);

			$data['center'] = $center;

		}

		$dbc = _dbc();
		$sql = 'SELECT count(id) AS c, type FROM license WHERE type IS NOT NULL GROUP BY type ORDER BY 2 ASC';
		$res = $dbc->fetchAll($sql);
		$data['license_type_list'] = $res;
		$data['license_type_pick'] = $_GET['type'];

		return $RES->write( $this->render('map.php', $data) );

	}

}
