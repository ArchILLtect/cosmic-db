<!--    Author: Nick Hanson
	      Version: 0.3
	      Date: 4/20/25
-->
<?php
  require_once('fileconstants.php');
  $page_title = isset($page_title) ? $page_title : "";

  if (session_status() == PHP_SESSION_NONE)
  {
	  session_start();
  }
?>
<nav class="navbar sticky-top navbar-expand-md navbar-dark"
	  style="background-color: #569f32;">
  <a class="navbar-brand" href="/cosmic-db/">
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
          href="/cosmic-db/">Home </a>
      <?php if (isset($_SESSION['user_access_privileges']) &&
          ($_SESSION['user_access_privileges'] == 'user' ||
          $_SESSION['user_access_privileges'] == 'admin')): ?>
        <a class="nav-item nav-link<?= $page_title == CDB_ADD_SPECIES_PAGE ? ' active' : '' ?>"
            href="/cosmic-db/species/addspecies.php">Add Species</a>
        <a class="nav-item nav-link<?= $page_title == CDB_ADD_SPECIES_PAGE ? ' active' : '' ?>"
            href="/cosmic-db/characters/addcharacter.php">Add Character</a>
      <?php endif; ?>
      <?php if (!isset($_SESSION['user_name'])): ?>
        <a class="nav-item nav-link<?= $page_title == CDB_HOME_PAGE ? ' active' : '' ?>"
            href="/cosmic-db/login.php">Login </a>
        <a class="nav-item nav-link<?= $page_title == CDB_SIGNUP_PAGE ? ' active' : '' ?>"
            href="/cosmic-db/signup.php">Signup </a>
      <?php else: ?>
        <a class="nav-item nav-link"
            href="/cosmic-db/logout.php">Logout (<?= $_SESSION['user_name'] ?>)</a>
      <?php endif; ?>
    </div>
  </div>
</nav>