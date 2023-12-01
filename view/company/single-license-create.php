<?php
/**
 * Simple Form to Create License
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

// return new class extends \Edoceo\Radix\View

?>

<form action="/license/create" autocomplete="off" method="post">

<table class="table">
<tr>
	<td><input class="form-control" name="license-name" placeholder="[ name ]"></td>
	<td><input class="form-control" name="license-code" placeholder="[ license code ]"></td>
	<td><input class="form-control" name="license-guid" placeholder="[ license guid ]"></td>
	<td class="r">
		<input name="company_id" type="hidden" value="<?= __h($data['Company']['id']) ?>">
		<button class="btn btn-outline-primary" name="a" type="submit" value="license-save"><i class="fas fa-plus"></i> Create License</button>
	</td>
</tr>
</table>

</form>
