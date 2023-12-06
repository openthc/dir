<?php
/**
 * Contact Autocomplete
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory\Controller\API\Contact;

class Autocomplete extends \OpenTHC\Directory\Controller\API\Base
{
	function __invoke($REQ, $RES, $ARG)
	{
		session_write_close();

		return $RES->withJSON([
			'data' => null,
			'meta' => [ 'note' => 'Not Found' ],
		], 404);

		// Auth Check

		// Search Contacts

		// Return Results

	}

}
