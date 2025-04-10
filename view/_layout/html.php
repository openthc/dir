<?php
/**
 * OpenTHC HTML Layout
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

use Edoceo\Radix;
use Edoceo\Radix\Session;

if (empty($_ENV['title'])) {
	$_ENV['title'] = $this->data['Page']['title'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1, user-scalable=yes">
<meta name="application-name" content="OpenTHC">
<meta name="theme-color" content="#069420">
<meta name="mobile-web-app-capable" content="yes">
<link rel="stylesheet" href="/vendor/fontawesome/css/all.min.css">
<link rel="stylesheet" href="/vendor/jquery/jquery-ui.min.css">
<link rel="stylesheet" href="/vendor/bootstrap/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.openthc.com/css/www/0.0.1/www.css">
<title><?= __h(strip_tags($_ENV['title'])) ?></title>
<style>
body {
	font-family: -apple-system,BlinkMacSystemFont,Verdana,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";
}
footer {
	margin-top: 2em;
}
.form-actions {
	margin-top: 2em;
}
</style>
</head>
<body>
<?= $this->block('menu-navbar') ?>
<div class="container-fluid">
<?php

if (!empty($_ENV['h1'])) {
	echo '<h1>';
	echo $_ENV['h1'];
	if (!empty($_ENV['h1-sub'])) {
		echo sprintf(' <small>%s</small>', $_ENV['h1-sub']);
	}
	echo '</h1>';
}


$x = Session::flash();
if (!empty($x)) {

	$x = str_replace('<div class="good">', '<div class="alert alert-success" role="alert">', $x);
	$x = str_replace('<div class="info">', '<div class="alert alert-info" role="alert">', $x);
	$x = str_replace('<div class="warn">', '<div class="alert alert-warning" role="alert">', $x);
	$x = str_replace('<div class="fail">', '<div class="alert alert-danger" role="alert">', $x);

	echo '<div class="radix-flash">';
	echo $x;
	echo '</div>';

}

echo $this->body;

echo '</div>'; // .container-fluid

echo $this->block('footer');

?>

<script src="/vendor/jquery/jquery.min.js"></script>
<script src="/vendor/jquery/jquery-ui.min.js"></script>
<script src="/vendor/bootstrap/bootstrap.bundle.min.js"></script>
<script>
$(function() {

	$('.company-autocomplete').autocomplete({
		source: 'https://directory.openthc.com/api/autocomplete/company',
		select: function(e, ui) {
			$('#company_id').val(ui.item.company.id);
			$('#company_save').addClass('btn-success');
		},
	});

	// $('#company-autocomplete').autocomplete({
	// 	source: 'https://directory.openthc.com/api/autocomplete/company',
	// });

	// $('#license-autocomplete').autocomplete({
	// 	source: 'https://directory.openthc.com/api/autocomplete/license',
	// });

	$('.btn-company-merge').on('click', function(e) {

		var mode = $(this).data('mode');
		switch (mode) {
		case 'pick':
			$(this).addClass('btn-outline-warning');
			$(this).removeClass('active btn-warning');
			$(this).data('mode', 'none');
			break;
		default:
			$(this).addClass('active btn-warning');
			$(this).removeClass('btn-outline-warning');
			$(this).data('mode', 'pick');
			break;
		}

		var pick_list = $('.btn-company-merge.active');

		if (e.ctrlKey) {
			var company_list = [];
			pick_list.each(function(i, n) {
				company_list.push(n.dataset.id);
			});
			window.open('/company/merge?id=' + company_list.join(','));
		}

	});


	$('.btn-contact-merge').on('click', function() {

		var mode = $(this).data('mode');
		switch (mode) {
		case 'pick':
			$(this).addClass('btn-outline-warning');
			$(this).removeClass('active btn-warning');
			$(this).data('mode', 'none');
			break;
		default:
			$(this).addClass('active btn-warning');
			$(this).removeClass('btn-outline-warning');
			$(this).data('mode', 'pick');
			break;
		}

		var pick_list = $('.btn-contact-merge.active');

		if (pick_list.length >= 2) {
			var contact_list = [];
			pick_list.each(function(i, n) {
				contact_list.push(n.dataset.id);
			});
			window.location = '/contact/merge?id=' + contact_list.join(',');
		}

	});

	$('.btn-license-merge').on('click', function() {

		var mode = $(this).data('mode');
		switch (mode) {
		case 'pick':
			$(this).addClass('btn-outline-warning');
			$(this).removeClass('active btn-warning');
			$(this).data('mode', 'none');
			break;
		default:
			$(this).addClass('active btn-warning');
			$(this).removeClass('btn-outline-warning');
			$(this).data('mode', 'pick');
			break;
		}

		var pick_list = $('.btn-license-merge.active');

		if (pick_list.length >= 2) {
			var license_list = [];
			pick_list.each(function(i, n) {
				license_list.push(n.dataset.id);
			});
			window.location = '/license/merge?id=' + license_list.join(',');
		}

	});

});
</script>
<?= $this->foot_script ?>
</body>
</html>
