<?php
/*
 * Module for Company Routes
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Module;

class Company extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'OpenTHC\Directory\Controller\Company\Single');

		// Create
		$a->map(['GET','POST'], '/create', 'OpenTHC\Directory\Controller\Company\Create');

		// Update
		$a->map(['GET','POST'], '/update', 'OpenTHC\Directory\Controller\Company\Update');

		// Merge
		$a->map(['GET','POST'], '/merge', 'OpenTHC\Directory\Controller\Company\Merge');

		// Review
		$a->get('/recent', 'OpenTHC\Directory\Controller\Company\Recent');
		$a->get('/review', 'OpenTHC\Directory\Controller\Company\Review');

		// Single
		$a->get('/{id}.json', 'OpenTHC\Directory\Controller\Company\Single');
		$a->get('/{id}', 'OpenTHC\Directory\Controller\Company\Single');

	}

}
