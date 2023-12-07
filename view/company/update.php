<?php
/**
 * Allow Administrators to Edit a Company
 */

use OpenTHC\Company;
use OpenTHC\License;

$_ENV['h1'] = null;
$_ENV['title'] = 'Directory :: Company :: Edit';

$Company = $data['Company'];

// Fixup Data
// if (!preg_match('/[a-z]/', $Company['name'])) {
// 	$Company['name'] = str_replace('()', null, $Company['name']);
// 	$Company['name'] = trim($Company['name']);
// 	$Company['name'] = ucwords(strtolower($Company['name']));
// }
$Company['phone'] = trim($Company['phone']);
if (strlen($Company['phone']) < 4) {
	$Company['phone'] = null;
}

$Company = $Company->toArray();
ksort($Company);

$_ENV['h1'] = null;
$_ENV['title'] = 'Directory :: Company :: ' . h($Company['name']) . ' :: Edit';

?>

<h1><?= h($_ENV['title']) ?></h1>

<form method="post">
<div class="container">

<table class="table table-sm">

<?php
foreach (array('name', 'name_alt', 'phone', 'email', 'address_full') AS $k) {
	echo _draw_edit_row($Company, $k, $Company[$k]);
}

echo '<tr><td colspan="2"><hr></td></tr>';

foreach ($Company as $k => $v) {

	switch ($k) {
	case 'flag':
	case 'geo_lat':
	case 'geo_lon':
	case 'hash':
	case 'id':
	case 'software':
	case 'stat_violation':
	case 'created_at':
	case 'updated_at':
	case 'name':
	case 'name_alt':
	case 'name_code':
	case 'phone':
	case 'email':
	case 'address_full':
	case 'address_meta':
	case 'profile_meta':
	case 'weblink_meta':
		// Ignore
		break;
	default:
		_draw_edit_row($Company, $k, $v);
	}

}
echo '</table>';

// Profile Links
$profile_field_list = array(
	'address_meta',
	'profile_meta',
	'weblink_meta',
);

foreach ($profile_field_list as $cf) {

	$company_field_data = json_decode($Company[$cf], true);
	if (empty($company_field_data)) {
		$company_field_data = array();
	}

	$output_field_list = array();

	switch ($cf) {
	case 'address_meta':
		unset($company_field_data['geo']);
		$output_field_list = array(
			'street',
			'city',
			'state',
			'county',
			'country',
		);
		break;
	case 'profile_meta':
		$output_field_list = array(
			'lead-content',
			'lead-content-type',
			'menu-serivce',
		);
		break;
	case 'weblink_meta':
		$output_field_list = array(
			'website',
			'facebook',
			'twitter',
			'instagram',
			'pinterest',
			'youtube',
			'vimeo',
			'leafly', // 5430 /djb 20170719
			'weedmaps', // 10500 /djb 20170719
			'allbud', // 63787 /djb 20170719
			'merryjane', // 71,969
			'dispensaries', // 111222 /djb 20170719
			'wikileaf', // 163046 /djb 20170719
			'ganjapreneur', // rank=169887 /djb 20170719
			'potguide', // 196023
			'wheresweed', // 210,262
			'leafbuyer', // 266,190
			'headshopfinder', // 323,101
			'yelp',
			'linkedin',
			'crunchbase',
			'weednative',
		);
		break;
	}

	$output_field_list = array_merge($output_field_list, array_keys($company_field_data));
	$output_field_list = array_unique($output_field_list);
	sort($output_field_list);

	echo "<h3>Edit: $cf</h3>";

	echo '<table class="table">';

	foreach ($output_field_list as $f) {
		echo '<tr>';
		echo '<td>' . $f . '</td>';
		echo '<td><input class="form-control" name="' . $cf . '-' . $f . '" value="' . h($company_field_data[$f]) . '"></td>';
		//echo '<td><a href="https://www.google.com/?q=' . rawurlencode(sprintf('%s %s', $f, $Company['name'])) . '" target="_blank"><i class="fas fa-search"></i></td>';
		echo '</tr>';
	}

	// Add One?

	echo '</table>';

}

?>

<div class="form-actions">
	<button class="btn btn-lg btn-outline-primary" name="a" value="save">Save</button>
	<button class="btn btn-lg btn-outline-warning" name="a" value="merge">Merge</button>
	<button class="btn btn-lg btn-outline-warning" name="a" value="mute">Mute</button>
	<button class="btn btn-lg btn-outline-danger" name="a" value="dead">Dead</button>
	<button class="btn btn-lg btn-outline-danger" name="a" value="drop">Drop</button>
</div>

</div>
</form>

<?php

return(0);


// License Data
$res_license = $dbc->fetchAll('SELECT * FROM license WHERE company_id = ?', array($Company['id']));
foreach ($res_license as $License) {
	// var_dump($License)
}

// Notes
$res_notes = $dbc->fetchAll('SELECT * FROM company_note WHERE company_id = ?', array($Company['id']));
foreach ($res_notes as $n) {
	// var_dump($n);
}

// if (!empty($Company['guid'])) {
// 	$file_list = glob(sprintf('%s/var/company/wa/%s*.json', APP_ROOT, $Company['guid']));
// 	print_r($file_list);
// }
