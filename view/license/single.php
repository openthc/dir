<?php
/**
 * View a Single License
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

$License = $this->data['License'];
switch ($License['stat']) {
	case '200':
		$License['stat_icon'] = '';
		break;
	case '410':
		$License['stat_icon'] = '<small><span class="text-danger" title="License is Closed/Gone"><i class="fas fa-ban"></i></span></small>';
		break;
}

$col = 'col-md-6';
if (!empty($License['guid']) && ($License['guid'] != $License['code'])) {
	$col = 'col-md-4';
}

$address_meta = json_decode($License['address_meta'], true);

$License['email'] = '-hidden-';
$License['phone'] = '-hidden-';

switch ($License['cre']) {
	case 'usa/wa':
		if (preg_match('/(\d{3})(\d{3})(\d{3})(\d{3})(\d{4})/', $License['guid'], $m)) {
			array_shift($m);
			$License['guid'] = implode('-', $m);
		}
}

?>


<div class="container mt-4">
<div class="card">

<h1 class="card-header"><?= $License['stat_icon'] ?> <?= __h($License['name']) ?></h1>

<div class="card-body">

	<div class="row mb-2">
	<div class="<?= $col ?>">
		<div class="input-group">
			<div class="input-group-text">Type:</div>
			<div class="form-control"><?= __h($License['type']) ?></div>
		</div>
	</div>
	<div class="<?= $col ?>">
		<div class="input-group">
			<div class="input-group-text">Code:</div>
			<div class="form-control"><?= __h($License['code']) ?></div>
		</div>
	</div>

	<?php
	if (!empty($License['guid']) && ($License['guid'] != $License['code'])) {
	?>
		<div class="<?= $col ?>">
			<div class="input-group">
				<div class="input-group-text">GUID:</div>
				<div class="form-control"><?= __h($License['guid']) ?></div>
			</div>
		</div>
	<?php
	}
	?>
	</div>

	<div class="row mb-2">
		<div class="col-md-6">
			<div class="input-group">
				<div class="input-group-text">Email:</div>
				<input class="form-control" readonly value="<?= __h($License['email']) ?>">
			</div>
		</div>
		<div class="col-md-6">
			<div class="input-group">
				<div class="input-group-text">Phone:</div>
				<input class="form-control" readonly value="<?= __h($License['phone']) ?>">
			</div>
		</div>
	</div>


	<?php
	if (!empty($address_meta['city'])) {
		 $addr = sprintf('%s, %s', $address_meta['city'], $address_meta['region']);
		 $addr = trim($addr, ' ,');
		 $address_html = _draw_google_map_link($addr);
	?>
			<div class="input-group mb-2">
				<div class="input-group-text">Map:</div>
				<div class="form-control">
					<?= $address_html; ?>
				</div>
			</div>
	<?php
	}
	?>

	<div class="input-group mb-2">
		<div class="input-group-text">Company:</div>
		<div class="form-control" style="overflow:hidden;">
			<a href="/company/<?= $this->data['Company']['id'] ?>">
				<?= __h($this->data['Company']['name']) ?>
			</a>
		</div>
	</div>

	<?php
	if ( ! empty($data['License_Public_Key_list'])) {
	?>
		<hr>
		<section>
		<h2>Public Communication Keys</h2>
		<?php
		foreach ($data['License_Public_Key_list'] as $pk0) {
			$pk0['meta'] = json_decode($pk0['meta'], true);
			if (empty($pk0['meta']['public-incoming-url'])) {
				$pk0['meta']['public-incoming-url'] = sprintf('https://openthc.pub/%s', $pk0['id']);
			}
			printf("<pre><strong>%s - <a href=\"%s\">%s</a></strong>\nCreated: %s; Expires: %s</pre>"
				, $pk0['id']
				, $pk0['meta']['public-incoming-url']
				, preg_replace('/^https?:\/\//', '', $pk0['meta']['public-incoming-url'])
				, $pk0['created_at']
				, $pk0['expires_at']
			);
		}
		?>
		</section>
	<?php
	}
	?>


</div>
<div class="card-footer">

	<div>
	<a class="btn btn-outline-primary" href="/license/update?id=<?= $this->data['License']['id'] ?>">Submit Update <i class="fas fa-save"></i></a>
	<a class="btn btn-outline-secondary" href="/api/license/<?= $this->data['License']['id'] ?>">Get Data</a>
	<?php
	if (_acl($_SESSION['Contact']['id'], 'license', 'update')) {

		echo '<a class="btn btn-outline-secondary" href="/license/update?id=' . $this->data['License']['id'] . '" title="Edit"><i class="fas fa-edit"></i> Edit</a>';

		$link_delta = sprintf('/delta?v=main&amp;tb=license&amp;pk=%s', $this->data['License']['id']);
		printf(' <a class="btn btn-outline-secondary" href="%s" target="_blank" title="View Delta Log"><i class="fas fa-history"></i> History</a>', $link_delta);

	}
	?>
	</div>

	<div>
		<code style="color: #777; margin:0; text-align:right; white-space: wrap;">Record: <?= $this->data['License']['id'] ?>; Created: <?= _date('m/d/y', $License['created_at']) ?>; Updated: <?= _date('m/d/y H:i', $License['updated_at']) ?>;</code>
	</div>

</div>

</div>
</div>
