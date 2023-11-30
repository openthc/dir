<?php
/**
 * Create a new Directory Listing
 */

use OpenTHC\Company;

$dbc = _dbc();


// Company Type Options
$Company_Type_list = array();
$res = $dbc->fetchAll('SELECT count(id) AS c, type FROM company GROUP BY type ORDER BY 1');
foreach ($res as $rec) {
	$Company_Type_list[ $rec['type'] ] = sprintf('%s (%d)', $rec['type'], $rec['c']);
}

?>

<form autocomplete="off" method="post">

<div class="container">

<div class="row">
<div class="col-md-6">
	<div class="form-group">
		<label>Company Name:</label>
		<input autocomplete="off" class="form-control" name="company-name" value="<?= h($C['name']) ?>">
	</div>
</div>
<div class="col-md-6">
	<div class="form-group">
		<label>Company Type:</label>
		<!-- <input class="form-control" id="company-type" name="company-type"> -->
		<select class="form-control" id="company-type" name="company-type">
		<?php
		foreach ($Company_Type_list as $t => $v) {
			printf('<option value="%s">%s</option>', $t, $v);
		}
		?>
		</select>
	</div>
</div>
</div> <!-- /.row -->

<div class="row">
<div class="col-md-4">
	<div class="form-group">
		<label>Phone:</label>
		<input autocomplete="off" class="form-control" name="phone">
	</div>
</div>
<div class="col-md-4">
	<div class="form-group">
		<label>Email:</label>
		<input autocomplete="off" class="form-control" name="email">
	</div>
</div>
<div class="col-md-4">
	<div class="form-group">
		<label>Website:</label>
		<input autocomplete="off" class="form-control" name="website">
	</div>
</div>
</div> <!-- /.row -->

<div class="row">
	<div class="col-md-8">
	<div class="form-group">
		<label>Address:</label>
		<input autocomplete="off" class="form-control" name="address_full">
	</div>
	</div>
</div>

<div class="form-actions">
	<button class="btn btn-lg btn-outline-primary" name="a" value="save"><i class="fas fa-save"></i> Submit</button>
</div>

</div> <!-- /.container -->
</form>
