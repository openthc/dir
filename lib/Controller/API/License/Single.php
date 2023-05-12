<?php
/**
 * Search for License
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Controller\API\License;

class Single extends \OpenTHC\Directory\Controller\API\Base
{
	private $_Service; // Service making the Request

	function __invoke($REQ, $RES, $ARG)
	{
		$this->_Service = $REQ->getAttribute('Service');

		$L0 = $this->findLicense($ARG);

		if ($L0) {

			$L1 = $this->inflate_license($L0);

			// $ret = $L1;
			$ret['data'] = $L1;
			$ret['meta'] = [
				'created_at' => $L0['created_at'],
				'updated_at' => $L0['updated_at'],
			];

			_exit_json($ret);

		}

		_exit_json([
			'data' => null,
			'meta' => [ 'detail' => 'Not Found [ALS-033]' ],
		], 404);

	}

	/**
	 *
	 */
	function findLicense($ARG)
	{
		$arg = array();

		$cre = $this->getCRE();
		if (!empty($cre)) {
		//	$arg['company.cre'] = $cre;
		}

		if (!empty($_GET['company'])) {
			$arg['company.guid'] = strtoupper($_GET['company']);
		}

		// Try by Full GUID
		$arg['license.guid'] = strtoupper($ARG['guid']);
		$res = $this->query($arg);
		if (1 == count($res)) {
			return($res[0]);
		}

		// Try by Code
		unset($arg['license.guid']);
		$arg['license.code'] = strtoupper($ARG['guid']);
		$res = $this->query($arg);
		if (1 == count($res)) {
			return($res[0]);
		}

		// Try by Code, No Prefix
		unset($arg['license.guid']);
		$arg['license.code'] = strtoupper(preg_replace('/^(E|G|J|M|R|Z)/', null, $ARG['guid']));
		$res = $this->query($arg);
		if (1 == count($res)) {
			return($res[0]);
		}

		// License, No Company
		unset($arg['company.guid']);
		unset($arg['license.guid']);
		unset($arg['license.code']);

		$arg['license.guid'] = strtoupper($ARG['guid']);
		$res = $this->query($arg);
		if (1 == count($res)) {
			return($res[0]);
		}

		// Match Numerical GUID or Code
		unset($arg['company.guid']);
		unset($arg['license.guid']);
		unset($arg['license.code']);

		$d = preg_replace('/^(E|G|J|M|R|Z)/', null, $ARG['guid']);
		$arg['license.code'] = sprintf('%%%d', $d);
		$arg['license.guid'] = sprintf('%%%d', $d);

		$res = $this->query($arg, 'LIKE');
		if (1 == count($res)) {
			return($res[0]);
		}

		// Some WA-State SHIT
		$q = $ARG['guid'];
		//if (preg_match('/^\w(\d{5,6})$/', $q, $m)) {
		//	$q = $m[1];
		//} else
		if (preg_match('/^WA\w+\.(\w+)$/', $q, $m)) {
			$q = $m[1];
			$q = "%{$q}";
			$arg = array();
			$arg['license.guid'] = $q;
			$res = $this->query($arg, 'LIKE');
			if (1 == count($res)) {
				return($res[0]);
			}
		}
		if (preg_match('/^([0-9A-Z]{26})$/', $q, $m)) {
			$q = $m[1];
			$arg = array();
			$arg['license.id'] = $q;
			$res = $this->query($arg);
			if (1 == count($res)) {
				return($res[0]);
			}
		}

		return null;

	}

	/**
	 *
	 */
	function help($RES)
	{
		$text = <<<EOT
# OpenTHC Directory License API

Your Application will need to [Register](https://openthc.com/oauth2/app/register) to get an API Key.

	GET /api/license?q={TERM}
	GET /api/license?q={TERM}&cre=co
	GET /api/license?q={TERM}&state=co
	GET /api/license?q={TERM}&company={TERM}
	GET /api/license?q={TERM}&license={CODE|GUID}
	GET /api/license?q={TERM}&address={TERM}
	GET /api/license?q={FIELD}:{TERM}

EOT;

		$data = array(
			'Page' => array(
				'title' => 'API',
			),
			'text' => $text
		);

		return $this->_container->view->render($RES, 'page/api.html', $data);

	}

	/**
	 *
	 */
	function query($where, $op='=')
	{
		$dbc = $this->_container->DB;

		$sql.= ' SELECT license.id';
		$sql.= ', company.guid AS company_guid';
		$sql.= ', license.guid AS license_guid';
		$sql.= ', license.code AS license_code';
		$sql.= ', license.*';
		$sql.= ' FROM license';
		$sql.= ' JOIN company ON license.company_id = company.id';
		$sql.= ' WHERE ';

		$tmp = array();
		foreach ($where as $k => $v) {
			$a = sprintf(':a%08x', crc32($k));
			$tmp[] = sprintf('%s %s %s', $k, $op, $a);
			$arg[$a] = $v;
		}
		$sql.= implode(' OR ', $tmp);
		$sql.= ' ORDER BY id ASC';

		$res = $dbc->fetch_all($sql, $arg);

		return $res;
	}

	/**
	 * Inflate and Sanatise the License Record
	 */
	function inflate_license($src)
	{
		$src['meta'] = json_decode($src['meta'], true);
		$src['cre_meta'] = json_decode($src['cre_meta'], true);

		// Two More Decodes? TF?
		// @todo Audit and Remove this
		if (!is_array($src['meta'])) {
			$src['meta'] = json_decode($src['meta'], true);
		}

		// Build Return
		$rec = array(
			'id' => $src['id'],
			'guid' => $src['guid'],
			'code' => $src['code'],
			'name' => $src['name'],
			'stat' => $src['stat'],
			'flag' => $src['flag'],
			'hash' => $src['hash'],
			'type' => $src['type'],
			'email' => $src['email'],
			'phone' => $src['phone'],
			'meta' => [
				'ubi9' => $src['meta']['ubi9'],
				'ubi16' => $src['meta']['ubi16'],
			],
			'address_full' => $src['address_full'],
			'address_meta' => [],
			'company' => array(
				'id' => $src['company_id'],
				'guid' => $src['company_guid'],
			),
		);

		// If Allowed Email?
		if ('019KAGVSC00T3G4RC3BZJMESMT' == $this->_Service['id']) {
			$rec['email'] = $src['email'];
		}

		if (empty($rec['address_meta']['city'])) {
			if (!empty($src['cre_meta']['city'])) {
				$rec['address_meta']['city'] = $src['cre_meta']['city'];
			}
		}

		if (!empty($src['geo_lat'])) {
			$rec['address_meta']['geo'] = [
				'lat' => $src['geo_lat'],
				'lon' => $src['geo_lon'],
			];
		}

		// Find Company ID
		if (empty($rec['company']['guid'])) {
			$rec['company']['guid'] = $src['cre_meta']['certificate_number'];
		}

		// $rec['ubi16'] = $rec['meta']['ubi16'];

		$rec['marker'] = array(
			'color' => \UI_license::color($rec['type']),
			// 'mark' => \UI_license::mark($rec[type']),
		);

		// CRE Data
		// $rec['_source'] = $src['cre_meta'];

		// // Load CRE Data
		// $cre_file = sprintf('%s/var/wa/%s.json', APP_ROOT, $License['guid']);
		// if (is_file($cre_file)) {
		// 	$cre_data = file_get_contents($cre_file);
		// 	$cre_data = json_decode($cre_data, true);
		// 	$ret_data['cre']['data'] = $cre_data['_source'];
		// }

		return $rec;

	}
}
