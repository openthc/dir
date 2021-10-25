<?php
/**
 * oAuth2 Returns Here
 */

namespace App\Controller\Auth\oAuth2;

use Edoceo\Radix\Session;

class Back extends \OpenTHC\Controller\Auth\oAuth2
{
	/**
	 *
	 */
	function __invoke($REQ, $RES, $ARG)
	{
		$p = $this->getProvider();

		if (empty($_GET['code'])) {
			_exit_text('Invalid Request [AOB-017]', 400);
		}

		// Check State
		$this->checkState();

		// Try to get an access token using the authorization code grant.
		$tok = null;
		try {
			$tok = $p->getAccessToken('authorization_code', [
				'code' => $_GET['code']
			]);
		} catch (\Exception $e) {
			_exit_text('Invalid Access Token [AOB-030]', 400);
		}

		if (empty($tok)) {
			_exit_text('Invalid Access Token [AOB-034]', 400);
		}

		// Token Data Verify
		$x = json_decode(json_encode($tok), true);
		if (empty($x['access_token'])) {
			_exit_text('Invalid Access Token [AOB-044]', 400);
		}
		if (empty($x['token_type'])) {
			_exit_text('Invalid Access Token [AOB-048]', 400);
		}

		// Using the access token, we may look up details about the
		// resource owner.
		try {

			$x = $p->getResourceOwner($tok);
			$Profile = $x->toArray();

			if (empty($Profile['Contact']['id'])) {
				_exit_text('Invalid [AOB-059]', 403);
			}

			if (empty($Profile['Company']['id'])) {
				_exit_text('Invalid [AOB-063]', 403);
			}

			// Scope Permission?
			if (is_string($Profile['scope'])) {
				$Profile['scope'] = explode(' ', $Profile['scope']);
			}
			if (!in_array('dir', $Profile['scope'])) {
				_exit_json([
					'data' => $Profile,
					'meta' => [ 'detail' => 'Scope Not Permitted [AOB-067]' ]
				], 403);
			}

			$_SESSION['email'] = $Profile['Contact']['username'];
			$_SESSION['Contact'] = $Profile['Contact'];
			$_SESSION['Company'] = $Profile['Company'];

			Session::flash('info', sprintf('Signed in as: %s', $_SESSION['Contact']['username']));

			$r = $_GET['r'];
			if (empty($r)) {
				$r = '/map';
			}

			return $RES->withRedirect($r);

		} catch (\Exception $e) {
			_exit_text($e->getMessage() . ' [AOB-076]', 500);
		}
	}
}
