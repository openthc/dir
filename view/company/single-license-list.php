<?php
/**
 * View License list for a Company
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

use OpenTHC\License;

// License Information
if (empty($this->data['License_List']) || (0 == count($this->data['License_List']))) {
	echo '<div class="container">';
	echo '<div class="alert alert-warning">No License records for this company</div>';
	echo '</div>';
	return(null);
}

$show_update = _acl($_SESSION['Contact']['id'], 'license', 'update');
$show_detail = _acl($_SESSION['Contact']['id'], 'address', 'view');

?>

<style>
#license-map {
	background: #ddd;
	border: 1px solid #333;
	height: 240px;
	position: relative;
}
#license-map h5 {
	display: none;
}

#license-map.none {
	display: flex;
	align-items: center;
	justify-content: center;
}

#license-map.none h5 {
	display: block;
	margin: 0;
	padding: 0;
}
</style>

<div class="container">
<div class="row">
<div class="col-md-9">
<h3>Licenses</h3>

<table class="table table-sm">
<thead class="thead-dark">
	<tr>
		<th>License</th>
		<th>Type</th>
		<th>Address</th>
		<th></th>
		<th></th>
	</tr>
</thead>
<tbody>
<?php
foreach ($this->data['License_List'] as $rec) {

	$License = new License(null, $rec);

	$meta = json_decode($rec['meta'], true);

	$link_addr = '- hidden -';
	if ($show_detail) {
		$link_addr = _draw_google_map_link($rec['address_full']);
	}
?>
	<tr>
	<td><a href="/license/<?= $rec['id'] ?>"><?= __h($rec['name']) ?></a>
		<br><code><?= __h($rec['code']) ?></code>
		<?php
		if ($rec['code'] != $rec['guid']) {
			printf('<br><small><code>%s</code></small>', __h($rec['guid']));
		}
		?>
	</td>
	<td><?= __h($rec['type']) ?></td>
	<td><?= $link_addr ?></td>
	<td class="r"><?= $License->getIcon() ?></td>
	<td class="r">
		<!-- <button class="btn btn-sm btn-outline-secondary btn-license-detail" type="button"><i class="fas fa-search"></i></button> -->
		<!-- <a class="btn btn-sm btn-outline-warning" href="/license/refresh?id=<?= $rec['id'] ?>"><i class="fas fa-sync"></i></a> -->
	<?php
	if ($show_update) {
	?>
		<a class="btn btn-sm btn-outline-secondary" href="/license/update?id=<?= $rec['id'] ?>"><i class="fas fa-edit"></i></a>
		<button
			class="btn btn-sm btn-outline-warning btn-license-merge"
			data-id="<?= $rec['id'] ?>"
			type="button">
				<i class="fas fa-compress"></i>
		</button>
	<?php
	}
	?>
	</td>
	</tr>
<?php
}
?>
</tbody>
</table>
</div>
<div class="col-md-3">
	<div id="license-map">
		<h5>Map Not Available</h5>
	</div>
</div>
</div>
</div>

<script>
$(function() {
	$('.btn-license-detail').on('click', function() {
		var arg = {
			id: $btn.data('license-id'),
		};
		$.get('/license/detail', arg, function(body, stat) {
			debugger;
		});
	});
});
</script>
