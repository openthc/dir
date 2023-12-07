<?php
/**
 * License Update from General Web-Request
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

use OpenTHC\License;

$_ENV['h1'] = $_ENV['title'] = 'Directory :: License :: Update';

$dbc_read = _dbc();
$License = new License($dbc_read, $_GET['id']);
if (empty($License['id'])) {
	_exit_html_warn('<h1>License Not Found [VLU-011]</h1><p>License not found, check the ID and try again</p>', 404, [ 'title' => 'Not Found ']);
}


$License = $License->toArray();
$License['email'] = null;
$License['phone'] = null;

$field_list = array(
	'name' => 'Name',
	'name_alt' => 'Name/Alternate',
	'email' => 'Email',
	'phone' => 'Phone',
	'address' => 'Address',
	'website' => 'Web Site',
	'twitter' => 'Twitter',
	'instagram' => 'Instagram',
	'facebook' => 'Facebook',
	'yelp' => 'Yelp',
	'leafly' => 'Leafly',
	'weedmaps' => 'WeedMaps',
);

?>

<form autocomplete="off" method="post">
<div class="container">
<h2>License: <?= h($License['name']) ?></h2>
<table class="table" style="font-size: 110%;">
<?php
foreach ($field_list AS $k => $n) {

	$v = $License[$k];

	echo '<tr>';
	echo '<td>' . $n . '</td>';
	echo '<td>';
	echo '<input class="form-control" name="license-' . $k . '" type="text" value="' . h($v) . '">';
	echo '</td>';
	echo '</tr>';
}

?>
<tr>
<td>Other Notes:</td>
<td><textarea class="form-control" name="license-note"></textarea></td>
</tr>
</table>

<div class="form-actions">
	<button class="btn btn-lg btn-outline-primary" name="a" value="license-update-save"><i class="fas fa-save"></i> Submit Updates</button>
</div>

</div>
</form>
