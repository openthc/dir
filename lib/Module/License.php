<?php
/*
 * Module for License Routes
 */

namespace OpenTHC\Directory\Module;

class License extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'OpenTHC\Directory\Controller\License\Main');

		// Create
		$a->map(['GET','POST'], '/create', 'OpenTHC\Directory\Controller\License\Create');

		// Update
		$a->map(['GET','POST'], '/update', 'OpenTHC\Directory\Controller\License\Update');

		// Merge
		$a->map(['GET','POST'], '/merge', 'OpenTHC\Directory\Controller\License\Merge');

		// Review
		// $OpenTHC\Directory->get('/license/recent', 'OpenTHC\Directory\Controller\License\Recent')
		$a->get('/review', 'OpenTHC\Directory\Controller\License\Review');

		// Special
		$a->map(['GET','POST'], '/address', 'OpenTHC\Directory\Controller\License\Address');

		// Single
		$a->get('/{id}.json', 'OpenTHC\Directory\Controller\License\Single');
		$a->get('/{id}', 'OpenTHC\Directory\Controller\License\Single');

	}

}
