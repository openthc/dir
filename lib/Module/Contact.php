<?php
/*
 * Module for Contact Routes
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Module;

class Contact extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'OpenTHC\Directory\Controller\Contact\Single');

		// Create
		$a->get('/create', 'OpenTHC\Directory\Controller\Contact\View');
		$a->post('/create', 'OpenTHC\Directory\Controller\Contact\Save');

		// Update
		$a->get('/update', 'OpenTHC\Directory\Controller\Contact\Update');
		$a->post('/update', 'OpenTHC\Directory\Controller\Contact\Update:post');

		// Merge
		$a->map(['GET','POST'], '/merge', 'OpenTHC\Directory\Controller\Contact\Merge');

		// Review
		$a->get('/review', 'OpenTHC\Directory\Controller\Contact\Review');

		// Single
		$a->get('/{id}.json', 'OpenTHC\Directory\Controller\Contact\Single');
		$a->get('/{id}', 'OpenTHC\Directory\Controller\Contact\Single');

	}
}
