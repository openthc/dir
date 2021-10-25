<?php
/*
 * Module for Contact Routes
 */

namespace App\Module;

class API extends \OpenTHC\Module\Base
{
	function __invoke($a)
	{
		$a->get('', 'App\Controller\API');

		// Full Search
		$a->get('/search', 'App\Controller\API\Search');
			//->add('OpenTHC\Middleware\CORS');

		// Company Search
		$a->get('/company', 'App\Controller\API\Company\Search');
			// ->add('OpenTHC\Middleware\CORS')

		// Company Create
		$a->post('/company', 'App\Controller\API\Company\Create')
			->add('App\Middleware\Trust');

		// Company Single
		$a->get('/company/{id}', 'App\Controller\API\Company\Single');
			//->add('OpenTHC\Middleware\CORS');

		// Company Update
		$a->post('/company/{guid}', 'App\Controller\API\Company\Update')
			->add('App\Middleware\Trust');

		// License Search
		$a->get('/license', 'App\Controller\API\License\Search')
			// ->add('App\Middleware\Trust')
			->add('App\Middleware\Auth\Service')
			// ->add('OpenTHC\Middleware\CORS')
			;

		// Create
		$a->post('/license', 'App\Controller\API\License\Create')
			->add('App\Middleware\Trust')
			;

		// Single
		$a->get('/license/{guid}', 'App\Controller\API\License\Single')
			// ->add('App\Middleware\Trust')
			->add('App\Middleware\Auth\Service')
			// ->add('OpenTHC\Middleware\CORS')
			;

		// Update
		$a->post('/license/{guid}', 'App\Controller\API\License\Update')
			// ->add('App\Middleware\Trust')
			->add('App\Middleware\Auth\Service')
			;


		// Contact
		$a->get('/contact', 'App\Controller\API\Contact\Search')
			->add('App\Middleware\Trust')
			;

		// Contact Create
		$a->post('/contact', 'App\Controller\API\Contact\Create')
			->add('App\Middleware\Trust')
			;

		// Contact Update
		$a->post('/contact/{id}', 'App\Controller\API\Contact\Update')
			->add('App\Middleware\Trust')
			;

		// Contact Single
		$a->get('/contact/{id}', 'App\Controller\API\Contact\Single')
			->add('App\Middleware\Trust')
			;

		// Autocomplete Search
		$a->get('/autocomplete/company', 'App\Controller\API\Company\Autocomplete'); // ->add('OpenTHC\Middleware\CORS');
		$a->get('/autocomplete/license', 'App\Controller\API\License\Autocomplete'); // ->add('OpenTHC\Middleware\CORS');
		$a->get('/autocomplete/contact', 'App\Controller\API\Contact\Autocomplete'); // ->add('OpenTHC\Middleware\CORS');
		//$a->get('/contact/search', 'App\Controller\API\Contact\Search')->add('OpenTHC\Middleware\CORS');

	}
}
