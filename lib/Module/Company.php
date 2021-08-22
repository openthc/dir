<?php
/*
 * Module for Company Routes
 */

namespace App\Module;

class Company extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'App\Controller\Company\Single');

		// Create
		$a->map(['GET','POST'], '/create', 'App\Controller\Company\Create');

		// Update
		$a->map(['GET','POST'], '/update', 'App\Controller\Company\Update');

		// Merge
		$a->map(['GET','POST'], '/merge', 'App\Controller\Company\Merge');

		// Review
		$a->get('/recent', 'App\Controller\Company\Recent');
		$a->get('/review', 'App\Controller\Company\Review');

		// Single
		$a->get('/{id}.json', 'App\Controller\Company\Single');
		$a->get('/{id}', 'App\Controller\Company\Single');

	}

}
