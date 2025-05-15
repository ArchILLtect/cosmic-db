<?php
/*  Author: Nick Hanson
    Version: 1.0
	Date: 4/20/25
*/
	require_once('fileconstants.php');
	$page_title = CDB_NAVMENU;

	if (session_status() == PHP_SESSION_NONE)
	{
		session_start();
	}
?>
<nav class="navbar sticky-top navbar-expand-md navbar-dark"
	  style="background-color: #569f32;">
	<a class="navbar-brand" href="/index.php">
		<img src="<?= CDB_ICON_PATH ?>" width="30" height="30"
			class="d-inline-block align-top" alt="Cosmic DB Icon">
		<?= CDB_HOME_PAGE ?>
	</a>
	<button class="navbar-toggler" type="button" data-toggle="collapse"
		data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
		aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<div class="collapse navbar-collapse" id="navbarNavAltMarkup">
		<div class="navbar-nav ml-auto">
		<a class="nav-item nav-link<?= $page_title == CDB_HOME_PAGE ? ' active' : '' ?>"
			href="/index.php">Home </a>
		<?php if (isset($_SESSION['user_access_privileges']) &&
			($_SESSION['user_access_privileges'] == 'user' ||
			$_SESSION['user_access_privileges'] == 'admin')): ?>
			<li class="nav-item dropdown">
			<a class="nav-link dropdown-toggle<?= $page_title == CDB_ADD_SPECIES_PAGE || $page_title == CDB_ADD_CHARACTER_PAGE ? ' active' : '' ?>"
				href="#" id="addDropdown" role="button" data-toggle="dropdown"
				aria-haspopup="true" aria-expanded="false">
				Add Asset
			</a>
			<div class="dropdown-menu" aria-labelledby="addDropdown" style="background-color: #569f32;">
				<a class="dropdown-item" href="/species/addspecies.php" style="color: white;"
					onmouseover="this.style.color = 'black';" onmouseout="this.style.color = 'white';">Add Species</a>
				<a class="dropdown-item" href="/characters/addcharacter.php" style="color: white;"
					onmouseover="this.style.color = 'black';" onmouseout="this.style.color = 'white';">Add Character</a>
				<!-- Add more here later -->
			</div>
			</li>
		<?php endif; ?>
		<?php if (!isset($_SESSION['user_name'])): ?>
			<a class="nav-item nav-link<?= $page_title == CDB_HOME_PAGE ? ' active' : '' ?>"
				href="/login.php">Login </a>
			<a class="nav-item nav-link<?= $page_title == CDB_SIGNUP_PAGE ? ' active' : '' ?>"
				href="/signup.php">Signup </a>
		<?php else: ?>
			<a class="nav-item nav-link<?= $page_title == CDB_EDIT_PROFILE_PAGE ? ' active' : '' ?>"
				href="/editprofile.php">Profile (<?= $_SESSION['user_name'] ?>)</a>
		<?php endif; ?>
		</div>
	</div>
</nav>