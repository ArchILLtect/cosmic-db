<!--    Author: Nick Hanson
	      Version: 0.3
	      Date: 3/12/25
-->
<?php
  $page_title = isset($page_title) ? $page_title : "";

  if (session_status() == PHP_SESSION_NONE)
  {
	  session_start();
  }
?>
<nav class="navbar sticky-top navbar-expand-md navbar-dark"
	  style="background-color: #569f32;">
  <a class="navbar-brand" href="<?= dirname($_SERVER['PHP_SELF']) ?>">
    <img src="resources/movie_rental_icon.png" width="30" height="30"
        class="d-inline-block align-top" alt="Movie Rental Icon">
    <?= MR_HOME_PAGE ?>
  </a>
  <button class="navbar-toggler" type="button" data-toggle="collapse"
      data-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup"
      aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
    <div class="navbar-nav ml-auto">
      <a class="nav-item nav-link<?= $page_title == MR_HOME_PAGE ? ' active' : '' ?>"
          href="<?= dirname($_SERVER['PHP_SELF']) ?>">Home </a>
      <?php if (isset($_SESSION['user_access_privileges']) &&
          ($_SESSION['user_access_privileges'] == 'user' ||
          $_SESSION['user_access_privileges'] == 'admin')): ?>
        <a class="nav-item nav-link<?= $page_title == MR_ADD_MOVIE_PAGE ? ' active' : '' ?>"
            href="addmovie.php">Add Movie</a>
      <?php endif; ?>
      <?php if (!isset($_SESSION['user_name'])): ?>
        <a class="nav-item nav-link<?= $page_title == MR_LOGIN_PAGE ? ' active' : '' ?>"
            href="login.php">Login </a>
        <a class="nav-item nav-link<?= $page_title == MR_SIGNUP_PAGE ? ' active' : '' ?>"
            href="signup.php">Signup </a>
      <?php else: ?>
        <a class="nav-item nav-link"
            href="logout.php">Logout (<?= $_SESSION['user_name'] ?>)</a>
      <?php endif; ?>
    </div>
  </div>
</nav>