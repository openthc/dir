
<form action="/search" autocomplete="off" method="get">
<div class="container mt-2">
<div class="form-inline">

	<div class="form-group mr-2">
		<input autocomplete="off" autofocus class="form-control" name="q" placeholder="Search Name, UBI, License, City, etc" type="text" value="<?= __h($_GET['q']) ?>">
	</div>

	<div class="form-group mr-2">
		<select class="form-control" name="type">
			<option value="">- All License Types -</option>
			<?php
			foreach ($data['license_type_list'] as $lic) {
				$sel = $data['license_type_pick'] == $lic['type'] ? 'selected' : null;
				printf('<option %s value="%s">%s</option>', $sel, $lic['type'], $lic['type']);
			}
			?>
		</select>
	</div>

	<div class="form-group mr-2">
		<select class="form-control" name="cre">
			<option value="">- Any Region -</option>
			<?php
			foreach ($data['cre_list'] as $cre) {
				$sel = ($data['cre_pick'] == $cre['code'] ? 'selected' : null);
				printf('<option %s value="%s">%s</option>', $sel, $cre['code'], $cre['name']);
			}
			?>
		</select>
	</div>

	<div class="form-group mr-2">
		<div class="btn-group">
			<button class="btn btn-outline-primary" type="submit"><i class="fas fa-search"></i> Search</button>
			<a class="btn btn-outline-primary" href="<?= $data['map_link'] ?>"><i class="fas fa-map"></i> Map</a>
		</div>
	</div>

</div>
</div>
</form>


<div class="container mt-2">
<table class="table table-sm">
<thead class="thead-dark">
<tr>
	<th>Name</th>
	<th>Region</th>
	<th>Type</th>
	<th>Code/GUID</th>
	<th></th>
	<th></th>
</tr>
</thead>
<tbody>
<?php
foreach ($data['result_list'] as $k => $v) {
?>
	<tr>
	<td>
		<?= h(ucfirst($v['object_type'])) ?>:
		<a href="/<?= $v['object_type'] ?>/<?= $v['object_id'] ?>">
			<?= h($v['name']) ?>
		</a>
	</td>
	<td><?= $v['cre'] ?></td>
	<td><?= $v['type'] ?></td>
	<td>
		<?php
		$out = [];
		$out[] = $v['code'];
		if ($v['code'] != $v['guid']) {
			$out[] = sprintf('<small>[%s]</small>', $v['guid']);
		}
		$out = array_filter($out);
		echo implode('<br>', $out);
		?>
	</td>
	<td class="r">
	<?php
	switch ($v['stat']) {
		case 100:
			echo '<i class="text-secondary far fa-plus"></i>';
			break;
		case 102:
			echo '<i class="text-secondary far fa-question"></i>';
			break;
		case 200:
			echo '<i class="text-success far fa-check"></i>';
			break;
		case 308:
			echo '<i class="text-warningfar fa-arrow-right"></i>';
			break;
		case 410:
			echo '<i class="text-danger far fa-ban text-danger"></i>';
			break;
		default:
			echo sprintf('%d', $v['stat']);
	}
	?>
	</td>
	<td class="r">
	<?php
	if ($data['acl_edit']) {
	?>
		<div class="btn-group btn-group-sm">

			<a class="btn btn-outline-secondary" href="/<?= $v['object_type'] ?>/update?id=<?= $v['object_id'] ?>"><i class="fas fa-edit"></i></a>
			<button
				class="btn btn-outline-warning btn-company-merge"
				data-id="<?= $v['object_id'] ?>"
				data-type="<?= $v['object_type'] ?>"
				title="Company Merge, CTRL+Click to Activate"
				type="button">
					<i class="fas fa-compress"></i>
			</button>

			<button
				class="btn btn-outline-warning btn-license-merge"
				data-id="<?= $v['object_id'] ?>"
				data-type="<?= $v['object_type'] ?>"
				title="License Merge, CTRL+Click to Activate"
				type="button">
					<i class="fas fa-compress"></i>
			</button>

			<button
				class="btn btn-outline-warning btn-contact-merge"
				data-id="<?= $v['object_id'] ?>"
				data-type="<?= $v['object_type'] ?>"
				title="Contact Merge, CTRL+Click to Activate"
				type="button">
					<i class="fas fa-compress"></i>
			</button>


		</div>
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


<?php
/**
 * Try the Local Cache?
 */
if (!empty($_GET['shit'])) {

	$dbc_cache = new Edoceo\Radix\DB\SQL(sprintf('sqlite:%s/var/search-index-01FE7P4BQ763V6VVJJB0EP25R0.sqlite', APP_ROOT));

	// Company
	$sql = <<<SQL
SELECT *
FROM company
WHERE name MATCH :q OR body MATCH :q OR tags MATCH :q
ORDER BY rank
SQL;

	$res = $dbc_cache->fetchAll($sql, [ ':q' => $_GET['q'] ]);
	// var_dump($res);

	// License
	$sql = <<<SQL
SELECT *
FROM license
WHERE name MATCH :q OR body MATCH :q OR tags MATCH :q
ORDER BY rank
SQL;

	$res = $dbc_cache->fetchAll($sql, [ ':q' => $_GET['q'] ]);
	// var_dump($res);

	// Contact
	$sql = <<<SQL
SELECT *
FROM contact
WHERE name MATCH :q OR body MATCH :q OR tags MATCH :q
ORDER BY rank
SQL;

	$res = $dbc_cache->fetchAll($sql, [ ':q' => $_GET['q'] ]);
	// var_dump($res);

}
