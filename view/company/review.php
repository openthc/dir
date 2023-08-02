<?php
/**
 * Review Companies
 */

use Edoceo\Radix;
use Edoceo\Radix\Session;
use Edoceo\Radix\DB\SQL;
use Edoceo\Radix\HTML\Form;

_acl_exit($_SESSION['Contact']['id'], 'company', 'review');


$_ENV['h1'] = $_ENV['title'] = '<a href="/company">Company</a> :: Review';

?>

<div class="container">

<div class="d-flex justify-between">
<ul class="nav nav-tabs">
  <li class="nav-item">
    <a class="nav-link" href="?cre=usa/ca">CA</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="?cre=usa/co">CO</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="?cre=usa/ok">OK</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="?cre=usa/or">OR</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="?cre=usa/wa">WA</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="?cre=usa/me">ME</a>
  </li>
</ul>

<div class="btn-group">
	<button class="btn btn-outline-secondary" name="p" type="submit" value="{{ page_back }}"><i class="fas fa-arrow-left"></i></button>
	<button class="btn btn-outline-secondary" name="p" type="submit" value="{{ page_next }}"><i class="fas fa-arrow-right"></i></button>
</div>

</div>

<table class="table table-sm">
<thead class="thead-dark">
<tr>
	<th>#</th>
	<th>Name</th>
	<th>Type</th>
	<th>Address</th>
	<th>Company</th>
	<th>License</th>
</tr>
</thead>

<tbody>
<?php
$idx = 0;
foreach ($data['company_list'] as $rec) {

	$idx++;

	if (empty($rec['name'])) {
		$rec['name'] = '-unknown-';
	}

	echo '<tr>';
	echo '<td>' . $idx . '</td>';

	echo '<td>';
	echo sprintf('<a href="/company/%s" target="_blank">%s</a>', $rec['id'], h($rec['name']));
	echo '</td>';

	echo '<td>' . $rec['type'] . '</td>';
	echo '<td>' . h($rec['address_full']) . '</td>';

	echo '<td><a href="/search?q=' . h($rec['guid']) . '" target="_blank">';
	echo $rec['guid'];
	echo '</a></td>';

	echo '<td>';
	echo '<a href="/search?q=' . h($rec['code']) . '" target="_blank">';
	echo h($rec['code']);
	echo '</a>';
	echo '</td>';

	echo '<td class="r"><a class="btn btn-sm btn-outline-secondary" href="/company/update?id=' . $rec['id'] . '" target="_blank"><i class="fas fa-edit"></i></a></td>';

	echo '</tr>';
}
?>
</tbody>
</table>
</div>
