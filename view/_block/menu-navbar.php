<?php
/**
 * OpenTHC Main Navbar
 */

use Edoceo\Radix;

$id_menu = 'ot-menu-head';

if (empty($_ENV['home-link'])) {
	$_ENV['home-link'] = '/';
}
if (empty($_ENV['home-html'])) {
	$_ENV['home-html'] = '<i class="fas fa-home"></i>';
}

?>

<nav class="navbar navbar-expand-md navbar-dark bg-dark sticky-top">
<div class="container-fluid">

<a class="navbar-brand" href="<?= $_ENV['home-link'] ?>"><?= $_ENV['home-html'] ?></a>

<button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#<?= $id_menu ?>" aria-expanded="false" aria-controls="<?= $id_menu ?>">
	<span class="navbar-toggler-icon"></span>
</button>

<div class="navbar-collapse collapse" id="<?= $id_menu ?>">

<ul class="navbar-nav">
<?php
$menu_list = App_Menu::getMenu('main');
foreach ($menu_list as $menu) {

	if (empty($menu['id'])) {
		$menu['id'] = 'menu-' . trim(preg_replace('/[^\w]+/', '-', $menu['link']), '-');
	}

	echo '<li class="nav-item"><a ';

	if ($menu['link'] == substr(Radix::$path, 0, strlen($menu['link']))) { // == substr($menu['link'], $l)) {
		echo ' class="nav-link active"';
	} else {
		echo ' class="nav-link"';
	}

	echo ' id="' . $menu['id'] . '"';
	echo ' href="' . $menu['link'] . '">';
	echo $menu['name'];
	echo '</a></li>';

}
?>
</ul>

<div class="ms-auto">
<form action="/search" autocomplete="off" class="form-inline">
<div class="input-group">
	<input autocomplete="off" class="form-control" name="q" placeholder="Search..." type="text">
	<button class="btn btn-outline-success" type="submit">Search</button>
</div>
</form>
</div>

<ul class="navbar-nav ms-auto">
<?php
$menu_list = App_Menu::getMenu('page');
foreach ($menu_list as $menu) {

	if (empty($menu['id'])) {
		$menu['id'] = 'menu-' . trim(preg_replace('/[^\w]+/', '-', $menu['link']), '-');
	}

	if (empty($menu['drop-down'])) {

		echo '<li class="nav-item">';
		echo '<a class="nav-link"';
		echo ' id="' . $menu['id'] . '"';
		echo ' href="' . $menu['link'] . '">';
		echo $menu['name'];
		echo '</a></li>';

	} else {
		echo '<li class="dropdown nav-item">';
        echo '<a class="nav-link" href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>';
        //print_r($menu);
        //  <ul class="dropdown-menu">
        //    <li><a href="#">Action</a></li>
        //    <li><a href="#">Another action</a></li>
        //    <li><a href="#">Something else here</a></li>
        //    <li role="separator" class="divider"></li>
        //    <li><a href="#">Separated link</a></li>
        //    <li role="separator" class="divider"></li>
        //    <li><a href="#">One more separated link</a></li>
        //  </ul>
        echo '</li>';
	}

}

if (!empty($_SESSION['uid'])) {
?>
	<li class="dropdown nav-item">
	<a class="dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-expanded="false" aria-haspopup="true"><i class="fas fa-cogs"></i></a>
	<ul class="dropdown-menu dropdown-menu-right">
		<!--
		<li><a href="https://openthc.com/help" target="_blank" title="Help"><i class="fas fa-life-ring"></i> Help!</a></li>
		<li><a href="https://openthc.com/training" target="_blank" title="View Training Videos"><i class="fas fa-play-circle-o"></i> Training</a></li>
		<li><a class="bug file-a-bug" href="#" title="Send Feedback / File a Bug"><i class="fas fa-bug"></i> Feedback</a></li>
		<li role="separator" class="divider"></li>
		-->
		<li class="dropdown-item"><a href="https://openthc.com/profile" title="Contact Settings"><i class="fas fa-user"></i> Profile</a></li>
		<li class="dropdown-item"><a href="https://openthc.com/profile/company" title="Company Settings"><i class="fas fa-building"></i> Company</a></li>
		<!-- <li><a href="https://openthc.com/profile/company/billing" title="Company Settings"><i class="fas fa-usd"></i> Billing</a></li> -->
		<!-- <li role="separator" class="divider"></li>
		<li><a href="https://openthc.com/auth/sign-out" title="Sign Out"><i class="fas fa-power-off"></i> Sign Out</a></li>
		-->
	</ul>
	</li>
	<li class="nav-item"><a class="nav-link" href="/auth/shut"><i class="fas fa-power-off"></i></a></li>
<?php
} else {
	echo '<li class="nav-item"><a class="nav-link" href="/auth?r=r"><i class="fas fa-sign-in-alt" style="color:#409E40;"></i></a></li>';
}

?>

</ul>
</div> <!-- /.navbar-collapse -->

</div>
</nav>
