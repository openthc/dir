<?php
/*
 * Module for Contact Routes
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Module;

class API extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'OpenTHC\Directory\Controller\API');

		// Full Search
		$a->get('/search', 'OpenTHC\Directory\Controller\API\Search');
			//->add('OpenTHC\Middleware\CORS');

		// Company Search
		$a->get('/company', 'OpenTHC\Directory\Controller\API\Company\Search');
			// ->add('OpenTHC\Middleware\CORS')

		// Company Create
		$a->post('/company', 'OpenTHC\Directory\Controller\API\Company\Create')
			->add('OpenTHC\Directory\Middleware\Trust');

		// Company Single
		$a->get('/company/{id}', 'OpenTHC\Directory\Controller\API\Company\Single');
			//->add('OpenTHC\Middleware\CORS');

		// Company Update
		$a->post('/company/{guid}', 'OpenTHC\Directory\Controller\API\Company\Update')
			->add('OpenTHC\Directory\Middleware\Trust');

		// License Search
		$a->get('/license', 'OpenTHC\Directory\Controller\API\License\Search')
			// ->add('OpenTHC\Directory\Middleware\Trust')
			->add('OpenTHC\Directory\Middleware\Auth\Service')
			// ->add('OpenTHC\Middleware\CORS')
			;

		// Create
		$a->post('/license', 'OpenTHC\Directory\Controller\API\License\Create')
			->add('OpenTHC\Directory\Middleware\Trust')
			;

		// Single
		$a->get('/license/{guid}', 'OpenTHC\Directory\Controller\API\License\Single')
			// ->add('OpenTHC\Directory\Middleware\Trust')
			->add('OpenTHC\Directory\Middleware\Auth\Service')
			// ->add('OpenTHC\Middleware\CORS')
			;

		// Update
		$a->post('/license/{guid}', 'OpenTHC\Directory\Controller\API\License\Update')
			// ->add('OpenTHC\Directory\Middleware\Trust')
			->add('OpenTHC\Directory\Middleware\Auth\Service')
			;


		// Contact
		$a->get('/contact', 'OpenTHC\Directory\Controller\API\Contact\Search')
			->add('OpenTHC\Directory\Middleware\Trust')
			;

		// Contact Create
		$a->post('/contact', 'OpenTHC\Directory\Controller\API\Contact\Create')
			->add('OpenTHC\Directory\Middleware\Trust')
			;

		// Contact Update
		$a->post('/contact/{id}', 'OpenTHC\Directory\Controller\API\Contact\Update')
			->add('OpenTHC\Directory\Middleware\Trust')
			;

		// Contact Single
		$a->get('/contact/{id}', 'OpenTHC\Directory\Controller\API\Contact\Single')
			->add('OpenTHC\Directory\Middleware\Trust')
			;

		// Autocomplete Search
		$a->get('/autocomplete/company', 'OpenTHC\Directory\Controller\API\Company\Autocomplete'); // ->add('OpenTHC\Middleware\CORS');
		$a->get('/autocomplete/license', 'OpenTHC\Directory\Controller\API\License\Autocomplete'); // ->add('OpenTHC\Middleware\CORS');
		$a->get('/autocomplete/contact', 'OpenTHC\Directory\Controller\API\Contact\Autocomplete'); // ->add('OpenTHC\Middleware\CORS');
		//$a->get('/contact/search', 'OpenTHC\Directory\Controller\API\Contact\Search')->add('OpenTHC\Middleware\CORS');

	}
}
