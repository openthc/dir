<?php
/**
 * Allow Anyone to Submit a Company Update
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

use OpenTHC\Company;

$_ENV['h1'] = 'Directory :: Company :: Update';
$_ENV['title'] = 'Directory :: Company :: Update';

$Company = $this->data['Company'];

$Company = $Company->toArray();
ksort($Company);

$field_list = array(
	'name' => 'Name',
	'name_alt' => 'Name/Alternate',
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

<form method="post">
<div class="container">
<h2><?= __h($Company['name']) ?></h2>
<table class="table" style="font-size: 24px;">
<?php
foreach ($field_list AS $k => $n) {

	$v = $Company[$k];

	echo '<tr>';
	echo '<td>' . $n . '</td>';
	echo '<td>';
	echo '<input class="form-control" name="company-' . $k . '" type="text" value="' . __h($v) . '">';
	echo '</td>';
	echo '</tr>';
}

?>
<tr>
<td>Other Notes:</td>
<td><textarea class="form-control" name="company-note"></textarea></td>
</tr>
</table>

<div class="form-actions">
	<button class="btn btn-lg btn-outline-primary" name="a" value="save"><i class="fas fa-save"></i> Save</button>
</div>

</div>
</form>
