<?php
/*
 * Module for Contact Routes
 */

namespace App\Module;

class Contact extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'App\Controller\Contact\Single');

		// Create
		$a->get('/create', 'App\Controller\Contact\View');
		$a->post('/create', 'App\Controller\Contact\Save');

		// Update
		$a->get('/update', 'App\Controller\Contact\Update');
		$a->post('/update', 'App\Controller\Contact\Update:post');

		// Merge
		$a->map(['GET','POST'], '/merge', 'App\Controller\Contact\Merge');

		// Review
		$a->get('/review', 'App\Controller\Contact\Review');

		// Single
		$a->get('/{id}.json', 'App\Controller\Contact\Single');
		$a->get('/{id}', 'App\Controller\Contact\Single');

	}
}
