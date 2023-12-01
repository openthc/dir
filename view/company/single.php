<?php
/**
 * View Single Company Profile
 *
 * SPDX-License-Identifier: GPL-3.0-only
 */

use Edoceo\Radix;
use Edoceo\Radix\DB\SQL;

use OpenTHC\Company;
use OpenTHC\Directory\App_Menu;

$_ENV['h1'] = $_ENV['title'] = 'Company';

App_Menu::addMenuItem('main', '/map', '<i class="fas fa-map-signs"></i> Map');
App_Menu::addMenuItem('page', sprintf('/company/%s.json', $Company['id']), '<i class="fas fa-code"></i>');

$Company = $data['Company'];

// Images
if (empty($Company['icon'])) {
	$Company['icon_want'] = true;
	//$Company['icon'] = 'https://placeimg.com/180/120/any';
	//$Company['icon'] = 'https://placeimg.com/180/120/nature';
	//$Company['icon'] = 'https://via.placeholder.com/180x120?Text=Upload Icon';
}

$_ENV['h1'] = null;
$_ENV['title'] = 'Company :: ' . __h($Company['name']) . ' ' . __h($Company['guid']);


// $this->Company = $Company;
$profile_meta = json_decode($Company['profile_meta'], true);

// Find Licenses
$License = $this->data['License_List'][0];

?>

<style>
.company-hero {
	background-position: center center;
	background-repeat: no-repeat;
	background-size:cover;
	bottom: 0;
	/* opacity: 0.80; */
	left:0;
	margin: 0 -15px 0 -15px;
	position: absolute;
	height: 100%;
	right: 0;
	top: 0;
}
</style>

<div class="mb-2" style="position:relative;">
<?php
if ( ! empty($Company['logo'])) {
	printf('<div class="company-hero" style="background-image: url(%s);"></div>', $Company['logo']);
}

?>

<div class="container mt-4">
<div class="row">
<div class="col-md-9">
	<div class="card">
	<div class="card-header" style="position:relative;">
		<h1 style="margin:0;"><?= __h($Company['name']) ?></h1>

		<div class="row">
		<div class="col-md-8">
		<p style="margin:0;"><?php
		$x = array();
		$x[] = $Company['guid'];
		$x[] = '/';
		$x[] = $Company['type'];
		$x = implode(' ', $x);
		$x = trim($x, ' /');
		echo __h($x);
		?>
		</p>
		</div>
		<!-- <div class="col-md-4 r">
			<div class="btn-group btn-group-sm">
				<button class="btn btn-outline-secondary btn-rate" data-rate="1" type="button"><i class="fas fa-star" style="color: gold;"></i></button>
				<button class="btn btn-outline-secondary btn-rate" data-rate="2" type="button"><i class="fas fa-star" style="color: gold;"></i></button>
				<button class="btn btn-outline-secondary btn-rate" data-rate="3" type="button"><i class="fas fa-star" style="color: gold;"></i></button>
				<button class="btn btn-outline-secondary btn-rate" data-rate="4" type="button"><i class="fas fa-star" style="color: gold;"></i></button>
				<button class="btn btn-outline-secondary btn-rate" data-rate="5" type="button"><i class="fas fa-star" style="color: gold;"></i></button>
			</div>
		</div> -->
		</div>

		<div style="position:absolute; top:0.25em; right:0.25em;">
			<!-- <?= UI_License::icon($Company) ?> -->
			<!--
			<button class="btn btn-sm" data-toggle="modal" data-target="#modal-company-icon-upload" title="Upload a Logo" type="button"><i class="fas fa-upload"></i></button>
			-->
		</div>

		<?php
		switch ($Company['stat']) {
		case 200:
			break;
		case 404:
		case 410:
			echo '<div class="alert alert-danger"><i class="fas fa-ban"></i> This business is closed</div>';
			break;
		}
		?>
	</div>
	<div class="card-body">
<?php
	if (!empty($profile_meta['lead-content'])) {
		echo '<div style="font-size: 125%;">';
		echo _markdown($profile_meta['lead-content']);
		echo '</div>';
	}

	echo _draw_weblinks($Company);
	// if (!empty($Company['email']) || !empty($Company['phone'])) {
	// 	echo '<a class="btn btn-sm btn-outline-primary" href="/company/connect?id=' . $Company['id'] . '">';
	// 	echo '<i class="fas fa-phone"></i> <i class="fas fa-envelope"></i> Connect';
	// 	echo '</a>';
	// }


	echo '<div>';
	echo '<div class="btn-group">';
	echo _draw_datasite_links($Company, $License);
	//echo ' <button class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#company-note-modal"><i class="fas fa-pencil-square-o"></i> Add Notes</button>';
	echo ' <a class="btn btn-sm btn-outline-secondary" href="/company/update?id=' . $Company['id'] . '"><i class="fas fa-save"></i> Update</a>';
	echo '</div>';
	echo '</div>';

	//if ($Company['flag'] & 2) {
	$dbc = _dbc();
	//$res = $dbc->fetchAll('SELECT id, name FROM company JOIN company_company ON company.id = company_company.company_id WHERE parent_company_id = ? ORDER BY company.name', array($Company['id']));
	$res = [];
	if (count($res) > 0) {
		// Look for Children
		$sub_list = array();
		foreach ($res as $x) {
			$sub_list[] = sprintf('<a href="/company/%s">%s</a>', $x['id'], __h($x['name']));
		}
		//var_dump($chk);
		echo '<p>Conglomerate: ';
		echo implode(', ', $sub_list);
		echo '</p>';

	} else {
		//$chk = $dbc->fetchOne('SELECT parent_company_id FROM company_company WHERE company_id = ?', array($Company['id']));
		if (!empty($chk)) {
			$PC = new Company($chk);
			echo sprintf('<p>Part of Parent Company: <a href="/company/%s">%s</a></p>', $PC['id'], __h($PC['name']));
		}
	}

?>

		<div style="text-align:right;">
			<?= _draw_government_links($Company) ?>
		</div>

	</div>
	</div>


</div>

<div class="col-md-3">
	<?php
	if (!empty($Company['icon'])) {
	?>
		<!--
		<div class="company-icon" style="<?= (empty($Company['icon']) ? 'background: #ddd; border:1px solid #333; ' : null) ?>height:240px; position: relative;">
		-->
		<div style="text-align:center;">
			<img alt="Company Icon" src="<?= $Company['icon'] ?>" style="max-height:256px; max-width:256px;">
		</div>
	<?php
	} else {
		echo '<p style="margin: 2em 0; text-align:center;">No Icon / Logo</p>';
	}
	?>

	<?php
	if (_acl($_SESSION['Contact']['id'], 'company', 'update')) {
		echo '<div style="position:absolute; right:0; bottom:0;">';
		echo '<a class="btn btn-sm btn-outline-secondary" id="company-icon-upload" data-toggle="modal" data-target="#modal-company-icon-upload"><i class="fas fa-upload"></i></a>';
		echo '</div>';
	}
	?>
	</div>
</div>
</div> <!-- /.row -->

<?php
if (_acl($_SESSION['Contact']['id'], 'company', 'update')) {

	$link_delta = sprintf('/delta?tb=company&amp;pk=%s', $Company['id']);

	echo '<div style="margin: 0; padding: 0.25rem; position: absolute; right: 0; top: 0;">';
		echo '<div class="btn-group">';
		echo '<a class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#modal-company-verify" href="#">V</a>';
		echo '<a class="btn btn-sm btn-outline-secondary" href="/company/update?id=' . $Company['id'] . '" title="Edit"><i class="fas fa-edit"></i></a>';
		printf('<a class="btn btn-sm btn-outline-secondary" href="%s" title="View Delta Log"><i class="fas fa-history"></i></a>', $link_delta);
		echo '</div>';
	echo '</div>';
}
?>

</div> <!-- /.container -->
</div>

<?php

// License Information
require_once(__DIR__ . '/single-license-list.php');

// Revenue Table
// require_once(__DIR__ . '/index-revenue-table.php');

// Visits & Violations
// require_once(__DIR__ . '/index-license-note-list.php');

// License List
if (_acl($_SESSION['Contact']['id'], 'license', 'update')) {

	echo '<div class="container">';
	echo '<div style="outline: 1px dashed red;">';
	require_once(__DIR__ . '/single-license-create.php');
	echo '</div>';
	echo '</div>';

}

// Contact List
if (_acl($_SESSION['Contact']['id'], 'contact', 'create')) {
	require_once(__DIR__ . '/single-contact-list.php');
}

//require_once(__DIR__ . '/index-retail-menu.php');

?>

</div>
</div> <!-- /.container -->

<div class="modal" id="company-note-modal" tabindex="-1" role="dialog">
<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
<div class="modal-content">
<form action="/company/note?id=<?= $Company['id'] ?>" method="post">
  <div class="modal-header">
	<h5 class="modal-title">Add Notes</h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	  <span aria-hidden="true">&times;</span>
	</button>
  </div>
  <div class="modal-body">
      <div class="form-group">
		  <textarea class="form-control" name="note"></textarea>
      </div>
      <div class="form-group">
      	<select class="form-control" name="show">
      	  <option>Privacy: Personal</option>
      	  <option>Privacy: Company</option>
      	  <option>Privacy: Public</option>
      	</select>
      </div>
  </div>
  <div class="modal-footer">
  <div class="col">
	<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
  </div>
  <div class="col r">
	<button type="submit" class="btn btn-outline-primary" name="a" value="save-note"><i class="fas fa-save"></i> Save</button>
  </div>
  </div>
</form>
</div>
</div>
</div>


<div class="modal" id="modal-company-icon-upload" tabindex="-1" role="dialog">
<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
<div class="modal-content">
<form action="/company/update?id=<?= $Company['id'] ?>" enctype="multipart/form-data" method="post">
  <div class="modal-header">
	<h5 class="modal-title">Upload Icon</h5>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	  <span aria-hidden="true">&times;</span>
	</button>
  </div>
  <div class="modal-body">
	  <div class="form-group">
		  <label>Logo File</label>
		<input class="form-control" name="logo-file" type="file">
		<p>This file is generally a wide image, it's added as the background of your profile</p>
      </div>
      <div class="form-group">
		  <label>Icon File</label>
		<input class="form-control" name="icon-file" type="file">
		<p>This file should be a square share, at least 512x512, transparent background, PNG</p>
      </div>
	  <div class="form-group">
		  <label>Icon Link</label>
		<input class="form-control" name="icon-link">
		<p>This file is generally a wide image, it's added as the background of your profile</p>
      </div>
  </div>
  <div class="modal-footer">
	<button type="submit" class="btn btn-outline-primary" name="a" value="company-save-image"><i class="fas fa-upload"></i> Upload</button>
  </div>
</form>
</div>
</div>
</div>


<?php
require_once(APP_ROOT . '/block/modal-company-verify.php');


// Drop a Pin
$cpt = array(); // Center Point
// From Company Address Meta
$x = $Company['address_meta'];
$x = json_decode($x, true);
$cpt = array(
	'latitude' => floatval($x['geo']['latitude']),
	'longitude' => floatval($x['geo']['longitude']),
);
// From First License
if (empty($cpt['latitude']) && empty($cpt['longitude'])) {
	$License = $this->data['License_List'][0];
	$cpt = array(
		'latitude' => floatval($License['geo_lat']),
		'longitude' => floatval($License['geo_lon']),
	);
}
$pin = $cpt;


?>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= \OpenTHC\Config::get('google/map_api_key_js') ?>&amp;libraries=places"></script>
<script src="/js/map.js"></script>
<script>
$(function() {

	var cpt = new google.maps.LatLng(<?= floatval($cpt['latitude']) ?>, <?= floatval($cpt['longitude']) ?>);
	if ('0,0' == cpt.toUrlValue()) {
		$('#license-map').addClass('none');
		return;
	}

	var div = document.getElementById('license-map');
	var opt = {
		center: cpt,
		disableDefaultUI: true,
		draggable: false,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		zoom: 9,
	};

	var LGM = new google.maps.Map(div, opt);
	//LGM.setCenter(cpt);
	var pin = new google.maps.Marker({
		map: LGM,
		position: cpt,
		draggable: false,
		dragCrossMove: false,
	});

});
</script>

<?php
function _draw_weblinks($rec)
{
	$link_list = array();

	$weblinks = json_decode($rec['weblink_meta'], true);
	if (!empty($weblinks)) {

		if (!empty($weblinks['website'])) {
			$host = parse_url($weblinks['website'], PHP_URL_HOST);
			$host = preg_replace('/^www\./', null, $host);
			$link_list[] = sprintf('<a class="btn" href="%s">%s</a>', $weblinks['website'], $host);
			unset($weblinks['website']);
		}

		foreach (array('facebook', 'twitter', 'instagram', 'linkedin', 'yelp', 'youtube') as $x) {
			if (!empty($weblinks[$x])) {
				$fn = sprintf('_fix_%s_link', $x);
				if (function_exists($fn)) {
					$link_list[] = sprintf('<a class="btn" href="%s"><i class="fab fa-%s"></i></a>', call_user_func($fn, $weblinks[$x]), $x);
				} else {
					$link_list[] = sprintf('<a class="btn" href="%s"><i class="fab fa-%s"></i></a>', $weblinks[$x], $x);
				}
				unset($weblinks[$x]);
			}
		}

		foreach ($weblinks as $k => $v) {

			$v = trim($v);
			if (empty($v)) {
				continue;
			}

			$h = parse_url($v, PHP_URL_HOST);
			$h = str_replace('www.', null, $h);

			$link_list[] = sprintf('<a class="btn" href="%s" target="_blank">%s</a>', $v, $h);

		}
	}

	if (count($link_list)) {
		return '<div class="btn-group">' . implode('', $link_list) . '</div>';
	}

}

function _draw_datasite_links($C, $L)
{
	$link_list = array();
	$code = preg_match('/(\d+)$/', $L['code'], $m) ? $m[1] : $L['code'];

	switch (strtolower($C['cre'])) {
	case 'usa/or':
		$link_list[] = '<a class="btn btn-sm btn-outline-secondary" href="https://headystats.com/?utm_source=openthc" target="_blank">headystats.com</a>';
		// https://opencorpdata.com/us-or/120005491
		break;
	case 'usa/wa':
		$link_list[] = '<a class="btn btn-sm btn-outline-secondary" href="https://data.openthc.org/company?id=' . $C['id'] . '" target="_blank"><i class="fas fa-database"></i> Data</a>';
		break;
	}

	return implode('', $link_list);
}



function _draw_government_links($C)
{
	$chk = _acl($_SESSION['Contact']['id'], 'company', 'detail-government');
	if (empty($chk)) {
		return null;
	}

	$link_list = [];

	switch (strtolower($C['cre'])) {
	case 'usa/co':
		$n = __h($C['name']);
		$html = <<<EOH
		<form action="https://www.sos.state.co.us/biz/AdvancedSearchCriteria.do" method="post" target="_blank">
		<input name="dateFrom" type="hidden" value="">
		<input name="dateTo" type="hidden" value="">
		<input name="includeEntity" type="hidden" value="true">
		<input name="searchName" type="hidden" value="$n">
		<input name="includeTypeExactMatch" type="hidden" value="true">
		<input name="personName_lastName" type="hidden" value="">
		<input name="personName_firstName" type="hidden" value="">
		<input name="personName_middleName" type="hidden" value="">
		<input name="personName_suffixName" type="hidden" value="">
		<input name="entityName" type="hidden" value="">
		<button class="btn btn-sm btn-outline-secondary" name="cmd" value="Search">CO-SOS</button>
		</form>
		EOH;

		$link_list[] = $html;

		break;

	case 'usa/nm':

		$link_list[] = '<a class="btn btn-sm btn-outline-secondary" href="https://portal.sos.state.nm.us/BFS/online/CorporationBusinessSearch">NM/SOS</a>';
		break;

	case 'usa/ok':

		//$link_list[] = sprintf('<a href="https://www.sos.ok.gov/business/corp/records.aspx?%s" target="_blank">OK-SOS: %s</a>', $arg, $C['name']);
		//$link_list[] = sprintf('<a href="https://publicrecords.onlinesearches.com/Oklahoma-Business-Licenses.htm?%s" target="_blank">OK-SOS: %s</a>', $arg, $C['name']);
		$link_list[] = '<a class="btn btn-sm btn-outline-secondary" href="/company/gov?' . http_build_query(array('id' => $C['id'], 'agency' => 'usa/ok/sos')) . '">OK/SOS</a>';
		if (!empty($C['guid'])) {
			$link_list[] = sprintf('<a class="btn btn-sm btn-outline-secondary" href="https://www.sos.ok.gov/corp/corpInformation.aspx?id=%s" target="_blank">OK-SOS</a>', rawurlencode($C['guid']));
		} else {
			$link_list[] = sprintf('<a class="btn btn-sm btn-outline-secondary" href="https://www.sos.ok.gov/corp/corpInquiryFind.aspx" target="_blank">OK-SOS</a>', rawurlencode($C['name']));
		}
		break;

	case 'usa/or':

		$arg = http_build_query(array(
			'p_name' => $C['name'],
			'p_regist_nbr' => '',
			'p_srch' => 'PHASE1P',
			'p_print' => 'FALSE',
		));

		$link_list[] = sprintf('<a class="btn btn-sm btn-outline-secondary" href="http://egov.sos.state.or.us/br/pkg_web_name_srch_inq.do_name_srch?%s" target="_blank">OR-SOS: %s</a>', $arg, $C['name']);

		break;

	case 'usa/wa':

		// $link_list[] = '<a href="/company/gov?' . http_build_query(array('id' => $C['id'], 'agency' => 'usa/wa/dor')) . '" target="_blank">WA/DOR</a>';
		// $link_list[] = '<a href="/company/gov?' . http_build_query(array('id' => $C['id'], 'agency' => 'usa/wa/sos')) . '" target="_blank">WA/SOS</a>';
		// $link_list[] = '<a href="/company/gov?' . http_build_query(array('id' => $C['id'], 'agency' => 'usa/wa/lni')) . '" target="_blank">WA/L&amp;I</a>';

		break;
	}

	if (count($link_list)) {
		return implode(' | ', $link_list);
	}

}
