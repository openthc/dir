<?php
/**
 * Update Company
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Controller\Company;

use Edoceo\Radix;
use Edoceo\Radix\Session;
use Edoceo\Radix\Image;
use Edoceo\Radix\Net\HTTP;


use \OpenTHC\Company;

class Update extends \OpenTHC\Controller\Base
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		if (empty($_GET['id'])) {
			__exit_text('Not Found [CCU-017', 404);
		}

		$dbc = _dbc();

		// Load Company
		$Company = new Company($dbc, $_GET['id']);
		if (empty($Company['id'])) {
			__exit_text('Not Found [VLE-026]', 404);
		}

		// If You are an Anoymous
		if (!_acl($_SESSION['Contact']['id'], 'company', 'update')) {

			$data = [
				'Company' => $Company
			];

			switch ($_POST['a']) {
				case 'save':

					// Send to CIC?
					$arg = [
						'company_id' => '018NY6XC00C0MPANY000000001',
						'contact_id' => '018NY6XC00C0NTACT000000001',
						'name' => sprintf('Company Update Request %s', $Company['name']),
						'meta' => json_encode([
							'Company' => $Company->toArray(),
							'update' => $_POST,
						])
					];
					$dbc_corp = _dbc('corp');
					$dbc_corp->insert('ticket', $arg);

					Session::flash('info', _('Company data update submitted, thank you'));

					return $RES->withRedirect(sprintf('/company/%s', $Company['id']));

					break;
			}

			return $RES->write( $this->render('company/update.php', $data) );
		};

		// Needs to Have Permission
		_acl_exit($_SESSION['Contact']['id'], 'company', 'update');

		switch (strtolower($_POST['a'])) {

			case 'company-save-image':
			case 'save-icon':

				// Logo File
				if (!empty($_FILES['logo-file']['tmp_name'])) {

					$tmp_file = $_FILES['logo-file']['tmp_name'];
					$png_file = sprintf('%s/webroot/img/company/%s/logo-full.png', APP_ROOT, $Company['id']);
					$web_file = sprintf('/img/company/%s/logo.png', $Company['id']);

					$png_path = dirname($png_file);
					if (!is_dir($png_path)) {
						mkdir($png_path, 0755, true);
					}
					Image::toPNG($tmp_file, $png_file);

					copy($png_file, sprintf('%s/webroot%s', APP_ROOT, $web_file));

					$Company['logo'] = $web_file;
				}

				// Icon File
				$tmp_file = null;
				if (!empty($_POST['icon-link'])) {
					$tfn = tempnam(sys_get_temp_dir(), 'png');
					$res = HTTP::get($_POST['icon-link']);
					file_put_contents($tfn, $res['body']);
					$tmp_file = $tfn;
				} elseif (!empty($_FILES['icon-file']['tmp_name'])) {
					$tmp_file = $_FILES['icon-file']['tmp_name'];
				}

				if ($tmp_file) {
					$src_file = sprintf('%s/webroot/img/company/%s/icon-full.png', APP_ROOT, $Company['id']);
					$src_path = dirname($src_file);
					if (!is_dir($src_path)) {
						mkdir($src_path, 0755, true);
					}
					Image::toPNG($tmp_file, $src_file);

					$img_file = sprintf('%s/webroot/img/company/%s/icon.png', APP_ROOT, $Company['id']);
					Image::makeThumb($src_file, $img_file, 256, 256);

					$Company['icon'] = sprintf('/img/company/%s/icon.png', $Company['id']);
				}

				$Company->save();

				return $RES->withRedirect(sprintf('/company/%s', $Company['id']));

				break;

			case 'dead':
				$this->_mark_dead($dbc, $Company);
				break;
			case 'drop':
				// See Delete.php
				$subC = new Delete($this->_container);
				$RES = $subC->__invoke($REQ, $RES, $ARG);
				return $RES;
				break;
			case 'merge':

				if (empty($_SESSION['company-merge-list'])) {
					$_SESSION['company-merge-list'] = array();
				}
				$_SESSION['company-merge-list'][] = $_GET['id'];
				Radix::redirect('/company/merge?id=' . implode(',', $_SESSION['company-merge-list']));

				break;

			case 'mute':

				$sql = 'UPDATE company SET flag = :flag WHERE id = :id';
				$arg = array(
					':id' => $_GET['id'],
					':flag' => Company::FLAG_MUTETEST,
				);
				$dbc->query($sql, $arg);

				Radix::redirect('/search');

				break;

			case 'save':

				_acl_exit($_SESSION['Contact']['id'], 'company', 'update');

				var_dump($_POST);

				$key_list = array_keys($Company->toArray());
				foreach ($key_list as $k) {
					$v = trim($_POST[$k]);
					if (!empty($v)) {
						$Company[$k] = $v;
					}
				}

				$Company['email'] = strtolower($Company['email']);
				$Company['updated_at'] = strftime('%Y-%m-%d %H:%M:%S');

				// Profile Meta
				$profile_meta = json_decode($Company['profile_meta'], true);
				if (empty($profile_meta)) {
					$profile_meta = array();
				}
				foreach ($_POST as $k => $v) {
					if (preg_match('/^profile_meta\-(.+)$/', $k, $m)) {
						if (!empty($v)) {
							$k = $m[1];
							$profile_meta[$k] = $v;
						}
					}
				}
				ksort($profile_meta);
				$Company['profile_meta'] = json_encode($profile_meta);

				$Company['weblink_meta'] = _parse_weblink_form($Company['weblink_meta']);

				$Company->save();

				Session::flash('info', 'Company Saved');
				return $RES->withRedirect(sprintf('/company/%s', $Company['id']));

				break;
		}

		// Load Company
		$Company = new Company($dbc, $_GET['id']);
		if (empty($Company['id'])) {
			__exit_text('Not Found [VLE-026]', 404);
		}


		$file = 'company/edit.php';
		$data = [
			'Company' => $Company
		];

		return $RES->write( $this->render($file, $data) );
	}

	/**
	 *
	 */
	function _mark_dead($Company)
	{
		$dbc->query('UPDATE company SET flag = flag | :f1::int WHERE id = :id', array(
			':id' => $Company['id'],
			':f1' => Company::FLAG_DEAD
		));

		$dbc->query('UPDATE license SET flag = flag | :f1::int, stat = 410 WHERE company_id = :id', array(
			':id' => $Company['id'],
			':f1' => License::FLAG_DEAD
		));

		return true;
	}

}
