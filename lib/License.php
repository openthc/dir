<?php
/**
 * License Model
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

namespace OpenTHC\Directory;

class License extends \OpenTHC\License
{
	// function _license_stat_map($L, $stat) // @deprecated use License Model or License/Update
	function setStatus($s)
	{
		$s = strtoupper(trim($s));
		if (empty($s)) {
			return(null);
		}

		switch ($s) {
		case '100':
		case '102':
		case 'ACTIVE-PENDING INSPECTION': // AK
		case 'COMPLETE': // AK-Web
		case 'PENDING (ISSUED)': // WA-XLS
			$this->offsetSet('stat', 100);
			$this->delFlag(License::FLAG_DEAD);
			$this->setFlag(License::FLAG_LIVE);
			break;
		case '200':
		case 'ACTIVE':
		case 'ACTIVE (ISSUED)':
		case 'ACTIVE-OPERATING': // Alaska
		case 'ACTIVE TITLE CERTIFICATE': // usa/wa
			$this->offsetSet('stat', 200);
			$this->delFlag(\OpenTHC\License::FLAG_DEAD);
			$this->setFlag(\OpenTHC\License::FLAG_LIVE);
			break;
		case '201':
		case 'ACTIVE-PENDING INSPECTION': // AK
		case 'COMPLETE': // AK-Web
		case 'PENDING (ISSUED)': // WA-XLS
			$this->offsetSet('stat', 201);
			$this->delFlag(\OpenTHC\License::FLAG_DEAD);
			$this->setFlag(\OpenTHC\License::FLAG_LIVE);
			break;
		case '410':
		case 'CLOSED (PERMANENT)': // WA-XLS
		case 'EXPIRED': // AK
		case 'REVOKED': // AK
			$this->offsetSet('stat', 410);
			$this->delFlag(\OpenTHC\License::FLAG_LIVE);
			$this->setFlag(\OpenTHC\License::FLAG_DEAD);
			break;
		case '404':
		case 'CLOSED (TEMPORARY)': // WA-XLS
		case 'DELEGATED': // AK-Web
		case 'INACTIVE': // WA-XLS-Labs
		case 'RESCINDED': // AK - Web
		case 'RETURNED': // AK
			$this->offsetSet('stat', 404);
			$this->delFlag(\OpenTHC\License::FLAG_LIVE);
			$this->setFlag(\OpenTHC\License::FLAG_DEAD);
			break;
		case 'PENDING (NOT ISSUED)':
		case 'FAILED TO COMPLETE': // Alaska
		case 'INCOMPLETE': // AK
		case 'QUEUE': // AK
		case 'TABLED': // AK
			$this->offsetSet('stat', 100);
			$this->offsetSet('flag', 0);
			break;
		case '451':
		case 'SURRENDERED': // AK
		case 'SUSPENDED':
			$this->offsetSet('stat', 451);
			$this->delFlag(\OpenTHC\License::FLAG_LIVE);
			$this->setFlag(\OpenTHC\License::FLAG_DEAD);
			break;
		default:
			throw new \Exception("Stat Not Handled: '{$s}'");
		}

		return $this->_data['stat'];
	}

	/**
	 * Hand me any text and we'll map to an OpenTHC License Type
	 * @param string $t some text describing the license
	 * @return [type] [description]
	 *
	 * Single Letter Options, from usa/wa/lcb prefix on license number
	 * E|G|M|J|P|R|T|Z
	 */
	function setType($t)
	{
		$t = strtoupper($t);

		$type_list = [

			'D' => 'Retail',
			'R' => 'Retail',
			'CANNABIS RETAILER' => 'Retail',
			'DISPENSARY' => 'Retail',
			'RETAIL' => 'Retail',
			'RETAIL CERTIFICATE HOLDER' => 'Retail',

			'E' => 'CO-OP',
			'CO-OP' => 'CO-OP',

			'G' => 'Grower',
			'CULTIVATOR' => 'Grower',
			'CANNABIS PRODUCER TIER 1' => 'Grower',
			'CANNABIS PRODUCER TIER 2' => 'Grower',
			'CANNABIS PRODUCER TIER 3' => 'Grower',
			'GROWER' => 'Grower',

			'J' => 'Grower+Processor',
			'CULTIVATOR_PRODUCTION' => 'Grower+Processor',
			'CANNABIS PRODUCER TIER 1,CANNABIS PROCESSOR' => 'Grower+Processor',
			'CANNABIS PRODUCER TIER 2,CANNABIS PROCESSOR' => 'Grower+Processor',
			'CANNABIS PRODUCER TIER 3,CANNABIS PROCESSOR' => 'Grower+Processor',

			'L' => 'Laboratory',
			'LAB' => 'Laboratory',
			'TESTING LABORATORY' => 'Laboratory', // usa/ok

			'M' => 'Processor', // Processor / Manufacturer
			'CANNABIS PROCESSOR' => 'Processor',
			'PROCESSOR' => 'Processor', // usa/ok
			'PRODUCTION' => 'Processor', // usa/wa/leafdata


			'T' => 'Tribe',
			'TRIBE' => 'Tribe', // usa/wa

			'Z' => 'Carrier',
			'TRANSPORTER' => 'Carrier',
			'CARRIER' => 'Carrier',

			'WASTE DISPOSAL FACILITY' => 'Disposal',

		];

		// switch ($r) {
		// 	// Big Text from LCB
		// 	case 'MARIJUANA PROCESSOR':
		// 		return 'P';
		// 	case 'MARIJUANA PRODUCER TIER 1':
		// 		return 'G1';
		// 	case 'MARIJUANA PRODUCER TIER 2':
		// 		return 'G2';
		// 	case 'MARIJUANA PRODUCER TIER 2; MARIJUANA PROCESSOR':
		// 		return 'G2+P';
		// 	case 'MARIJUANA PRODUCER TIER 3':
		// 		return 'G3';
		// 	case 'MARIJUANA PRODUCER TIER 3; MARIJUANA PROCESSOR':
		// 	case 'MARIJUANA PROCESSOR; MARIJUANA PRODUCER TIER 3':
		// 		return 'G3+P';
		// 	case 'MARIJUANA RETAILER':
		// 		return 'R';
		// 	case 'MARIJUANA RETAILER; MEDICAL MARIJUANA ENDORSEMENT':
		// 		return 'R+M';
		// 	case 'MARIJUANA TRANSPORTATION':
		// 		return 'C';
		// 	default:
		// 		throw new \Exception("Invalid License Type to Map: '$t'");
		// 	}

		$r = $type_list[$t];
		if (empty($r)) {
			throw new \Exception("Invalid License Type to Map: '$t'");
		}

		$this->offsetSet('type', $r);

		return $r;

	}
}
