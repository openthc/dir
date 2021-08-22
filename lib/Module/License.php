<?php
/*
 * Module for License Routes
 */

namespace App\Module;

class License extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'App\Controller\License\Main');

		// Create
		$a->map(['GET','POST'], '/create', 'App\Controller\License\Create');

		// Update
		$a->map(['GET','POST'], '/update', 'App\Controller\License\Update');

		// Merge
		$a->map(['GET','POST'], '/merge', 'App\Controller\License\Merge');

		// Review
		// $app->get('/license/recent', 'App\Controller\License\Recent')
		$a->get('/review', 'App\Controller\License\Review');

		// Special
		$a->map(['GET','POST'], '/address', 'App\Controller\License\Address');

		// Single
		$a->get('/{id}.json', 'App\Controller\License\Single');
		$a->get('/{id}', 'App\Controller\License\Single');

	}

}
