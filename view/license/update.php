<?php
/**
 * Allow Administrators to Edit a License
 */

use Edoceo\Radix;
use Edoceo\Radix\Session;

use OpenTHC\Company;
use OpenTHC\License;

$_ENV['h1'] = null;
$_ENV['title'] = 'Directory :: License :: Edit';

_acl_exit($_SESSION['Contact']['id'], 'license', 'update');

if (empty($_GET['id'])) {
	__exit_text('Missing Parameter [VLE-020]', 400);
}

$dbc = _dbc();


// Load License
$License = new License($dbc, $_GET['id']);
if (empty($License['id'])) {
	__exit_text('Not Found [VLE-026]', 404);
}

switch (strtolower($_POST['a'])) {
case 'license-confirm':
	$dbc->query('UPDATE license SET updated_at = now() WHERE id = :l1', [ ':l1' => $License['id'] ]);
	Radix::redirect(sprintf('/license/%s', $License['id']));
	break;

case 'license-company-unlink':

	var_dump($_POST); exit;

	break;

case 'drop':

	// $arg = array(':id' => $_GET['id']);
	// SQL::query('DELETE FROM fire_member_checkin WHERE license_id = :id', $arg);
	// SQL::query('DELETE FROM license WHERE id = :id', $arg);
	$License->delete();
	Session::flash('info', 'License Deleted');
	Radix::redirect('/company?id=' . $License['company_id']);

	break;

case 'merge':

	_acl_exit($_SESSION['Contact']['id'], 'license', 'update-merge');

	if (empty($_SESSION['license-merge-list'])) {
		$_SESSION['license-merge-list'] = array();
	}
	$_SESSION['license-merge-list'][] = $_GET['id'];
	Radix::redirect('/license/merge?id=' . implode(',', $_SESSION['license-merge-list']));

	break;

case 'mute':

	$License['stat'] = 404;
	$License->save();
	Radix::redirect('/company?id=' . $License['company_id']);

	break;

case 'save':

	_acl_exit($_SESSION['Contact']['id'], 'license', 'update');

	$key_list = [
		'cre',
		'name',
		'name_cre',
		'name_dba',
//		'code',
		'stat',
		'type',
		'phone',
		'email',
		'address_full',
	];

	$L1 = [];
	$L1['id'] = $License['id'];
	// $m0 = json_decode($License['meta'], true);
	// $m1 = array();

	foreach ($key_list as $k) {
		if (isset($_POST[$k])) {
			$v = trim($_POST[$k]);
			if (0 == strlen($v)) {
				$v = null;
			}
			$L1[$k] = $v;
		}
	}

	$res = _license_post_update([
		'license' => $L1,
	]);
	switch ($res['code']) {
	case 200:
		// OK
		// var_dump([$res]); die;
	break;
	default:
		header('content-type: text/plain');
		var_dump($res);
		die("\nFailed [VLE-105]\n");
	}

	Session::flash('info', 'License Updated');
	if ( ! empty($License['company_id'])) {
		Radix::redirect('/company?id=' . $License['company_id']);
	} else {
		Radix::redirect(sprintf('/license/%s', $License['id']));
	}

	break;

}

$Company = new Company($dbc, $License['company_id']);

$License = $License->toArray();
ksort($License);


// $_ENV['h1'] = null;
$_ENV['title'] = 'Directory :: License :: ' . __h($License['name']) . ' :: Edit';

?>

<div class="d-flex justify-content-between">
<h1><?= $_ENV['title'] ?></h1>
<div>
	<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-license-note">Add Note</button>
</div>
</div>

<form autocomplete="off" method="post">
<div class="container">

<div class="btn-group">
	<a class="btn btn-sm" href="/license/address?id=<?= $License['id'] ?>">Address</a>
	<a class="btn btn-sm" href="/delta?tb=license&amp;pk=<?= $License['id'] ?>" title="View History"><i class="fas fa-history"></i></a>
</div>


<table class="table table-sm" style="font-size: 24px;">
<tr>
	<td>Code:</td>
	<td><input class="form-control" disabled value="<?= __h($License['code']) ?>"></td>
</tr>
<?php
if ( ! empty($License['guid']) && ($License['code'] != $License['guid'])) {
?>
	<tr>
		<td>GUID:</td>
		<td><input class="form-control" disabled value="<?= __h($License['guid']) ?>"></td>
	</tr>
<?php
}
?>
<tr>
	<td>Company:</td>
	<td>
		<div class="input-group">
			<input class="form-control company-autocomplete" value="<?= __h($Company['name']) ?>">
			<div class="input-group-append">
				<a class="btn btn-outline-secondary"
					href="/company?id=<?= $License['company_id'] ?>"
					target="_blank"><i class="fas fa-link"></i></a>
				<button class="btn btn-danger" name="a" type="submit" value="license-company-unlink"><i class="fas fa-trash"></i></button>
			</div>
		</div>
	</td>
</tr>
<?php
foreach (array('name', 'name_cre', 'name_dba', 'phone', 'email', 'address_full') AS $k) {
	echo _draw_edit_row($License, $k, $License[$k]);
}

echo '<tr><td colspan="2"><hr></td></tr>';

foreach ($License as $k => $v) {

	switch ($k) {
	case 'id':
	case 'company_id':
	case 'name':
	case 'name_cre':
	case 'name_dba':
	case 'code':
	case 'flag':
	case 'guid':
	case 'meta':
	case 'tags':
	case 'email':
	case 'phone':
	case 'cre_meta':
	case 'cre_meta_hash':
	case 'name_code':
	case 'created_at':
	case 'updated_at':
	case 'address_full':
	case 'address_meta':
	case 'weblink_meta':
	case 'license_type_id':
		// Ignore
		break;
	default:
		echo _draw_edit_row($License, $k, $License[$k]);
	}
}
?>
</table>

<?php
// Profile Links
$profile_field_list = array(
	// 'address_meta',
	// 'profile_meta',
	// 'weblink_meta',
);

foreach ($profile_field_list as $cf) {

	$profile_field_data = json_decode($License[$cf], true);
	if (empty($profile_field_data)) {
		$profile_field_data = array();
	}

	$output_field_list = array();

	switch ($cf) {
	// case 'address_meta':
	// 	unset($profile_field_data['geo']);
	// 	$output_field_list = array(
	// 		'street',
	// 		'city',
	// 		'state',
	// 		'county',
	// 		'country',
	// 	);
	// 	break;
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

	$output_field_list = array_merge($output_field_list, array_keys($profile_field_data));
	$output_field_list = array_unique($output_field_list);
	sort($output_field_list);

	echo "<h3>Edit: $cf</h3>";
	//Radix::dump($data);

	echo '<table class="table table-sm">';

	foreach ($output_field_list as $f) {
		echo '<tr>';
		echo '<td>' . $f . '</td>';
		echo '<td><input class="form-control form-control-sm" name="' . $cf . '-' . $f . '" value="' . __h($profile_field_data[$f]) . '"></td>';
		echo '</tr>';
	}

	// Add One?

	echo '</table>';

}
?>


<div class="form-actions">
	<button class="btn btn-lg btn-outline-primary" name="a" value="license-confirm">Confirm</button>
	<button class="btn btn-lg btn-outline-primary" name="a" value="save">Save</button>
	<button class="btn btn-lg btn-outline-warning" name="a" value="merge">Merge</button>
	<button class="btn btn-lg btn-outline-danger" name="a" value="mute">Mute</button>
	<button class="btn btn-lg btn-outline-danger" name="a" value="drop">Drop</button>
</div>

</div>
</form>



<div class="modal" id="modal-license-note" tabindex="-1">
<div class="modal-dialog">

</div>
</div>
