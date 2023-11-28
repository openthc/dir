<?php
/**
 *
 */

namespace OpenTHC\Directory\Middleware;

class Menu extends \OpenTHC\Middleware\Base
{
	function __invoke($REQ, $RES, $NMW)
	{
		$menu = array(
			'home_link' => '/',
			'home_html' => '<i class="fas fa-home"></i>',
			'show_search' => true,
			'main' => array(),
			'page' => array(
				array(
					'link' => '/auth/open?r=r',
					'html' => '<i class="fas fa-sign-in-alt"></i>',
				)
			),
		);

		if (!empty($_SESSION['uid'])) {

			$menu['main'][] = array(
				'link' => '/map',
				'html' => '<i class="fas fa-map-signs"></i> Map',
			);

			$menu['page'] = array(
				//array(
				//	'link' => '/company/create',
				//	'html' => '<i class="fas fa-check-square-o"></i> Create'
				//),
				array(
					'link' => '/auth/shut',
					'html' => '<i class="fas fa-power-off"></i>',
				)
			);
		}

		if (_acl($_SESSION['Contact']['id'], 'recent-updates', 'view')) {

			$menu['main'][] = array(
				'link' => '/company/recent',
				'html' => '<span style="border-bottom:1px solid #e00;"><i class="fas fa-industry"></i> Companies</span>',
			);
			//$menu['main'][] = array(
			//	'link' => '/company/recent',
			//	'html' => 'Recent',
			//);
			$menu['main'][] = array(
				'link' => '/license/recent',
				'html' => '<span style="border-bottom:1px solid #e00;">Licenses</span>',
			);
		}


		$this->_container->view['menu'] = $menu;

		$RES = $NMW($REQ, $RES);

		return $RES;

	}

}
