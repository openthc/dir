<?php
/**
 * Contact List
 */

$Contact_List = $data['Contact_List'];

?>

<div class="container">
<div style="outline: 1px dashed red;">

<form action="/contact/create" method="post">

<h3>Contacts</h3>

<table class="table table-sm">

<?php
if (empty($Contact_List)) {
?>

	<tr>
		<td colspan="4"><h3 style="text-align:center;">No Contacts</h3></td>
	</tr>

<?php
} else {
?>

	<thead>
		<tr>
			<!-- <th>Type</th> -->
			<th>Name</th>
			<th>Email</th>
			<th>Phone</th>
			<th></th>
			<th></th>
		</tr>
	</thead>

	<tbody>

	<?php
	$cX = [];
	foreach ($Contact_List as $Contact) {
	?>
		<tr style="<?= ($cX['id'] == $Contact['id'] ? 'background:#eee;' : null) ?>">
			<td><a href="/contact/<?= rawurlencode($Contact['id']) ?>"><?= __h($Contact['name'] ?: '--' ) ?></a></td>
			<td><?= __h($Contact['email']) ?></td>
			<td><?= __h($Contact['phone']) ?></td>
			<td><?= __h($Contact['contact_type']) ?></td>
			<td class="r">
				<a class="btn btn-sm btn-outline-secondary" href="/contact/<?= rawurlencode($Contact['id']) ?>"><i class="fas fa-edit"></i></a>
				<button
					class="btn btn-sm btn-outline-warning btn-contact-merge"
					data-id="<?= __h($Contact['id']) ?>"
					type="button">
						<i class="fas fa-compress"></i>
				</button>
			</td>
		</tr>
	<?php
		$cX = $Contact;
	}
}
?>
	<tr>
		<!-- <td></td> -->
		<td>
			<input class="form-control" name="name" placeholder="Name">
		</td>
		<td>
			<input class="form-control" name="email" placeholder="email">
		</td>
		<td>
			<input class="form-control" name="phone" placeholder="phone">
		</td>
		<td class="r" colspan="2">
			<input name="company_id" type="hidden" value="<?= __h($Company['id']) ?>">
			<button class="btn btn-outline-primary" name="a" value="create-contact"><i class="fas fa-plus"></i> Create</button>
		</td>
	</tr>

</tbody>
</table>

</form>

</div>
</div>
