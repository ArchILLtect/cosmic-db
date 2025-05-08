<!--    Author: Nick Hanson
	      Version: 0.3
	      Date: 4/20/25
-->
<!DOCTYPE html>
<?php
	require_once('pagetitles.php');
	$page_title = CDB_SIGNUP_PAGE;
?>
<html>
	<head>
		<title><?= $page_title ?></title>
		<link rel="stylesheet"
				href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
				integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS"
				crossorigin="anonymous">
	</head>
	<body>
		<?php
            require_once('navmenu.php');
        ?>
		<div class="card">
			<div class="card-body">
				<h1>Signup for a Cosmic DB Account</h1>
				<hr/>
				<?php
					$show_signup_form = true;

					if (isset($_POST['signup_submission']))
					{
						// Get user name and password
						$user_name = $_POST['user_name'];
						$password = $_POST['password'];

						if (!empty($user_name) && !empty($password))
						{
							require_once('dbconnection.php');
							require_once('queryutils.php');

							$dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
									or trigger_error('Error connecting to MySQL server for '
									. DB_NAME, E_USER_ERROR);

							// Check if user already exists
							$query = "SELECT * FROM user WHERE user_name = ?";

							$results = parameterizedQuery($dbc, $query, 's', $user_name)
									or trigger_error(mysqli_error($dbc), E_USER_ERROR);
							
							// If user does not exist, create an account for them
							if (mysqli_num_rows($results) == 0)
							{
								$salted_hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $query = "INSERT INTO user (user_name, password_hash) VALUES (?, ?)";
                $results = parameterizedQuery($dbc, $query, 'ss', $user_name, $salted_hashed_password)
                          or trigger_error(mysqli_error($dbc, E_USER_ERROR));
                
                // Direct user to the login page
                echo "<h4><p class='text-success'>Thank you for signing up <strong>$user_name"
                    . "</strong>! Your new account has ben successfully created.<br/>"
                    . "Your now ready to <a href='login.php'>log in</a>.</p></h4>";
                
                $show_signup_form = false;
							}
							else
							{
								echo "<h4><p class='text-danger'>An account already exists
										for this username:<span class='font-weight-bold'> (user_name)
										</span>Please use a different user name.</p></h4><hr/>";
							}
						}
						else
						{
							// Output error message
							echo "<h4><p class='text-danger'>You must enter both a "
									. "user name and password.</p></h4><hr/>";
						}
					}

					if ($show_signup_form):
				?>
				<form class="needs-validation" novalidate method="POST"
						action="<?= $_SERVER['PHP_SELF'] ?>">
					<div class="from-group row">
						<label for="user_name" class="col-sm-2 col-form-label-lg">
							User Name
						</label>
						<div class="col-sm-4">
							<input type="text" class="form-control"
									id="user_name" name="user_name"
									placeholder="Enter a user name" required>
							<div class="invalid-feedback">
								Please provide a valid user name.
							</div>
						</div>
					</div>
					<div class="from-group row">
						<label for="password"class="col-sm-2 col-form-label-lg">
							Password
						</label>
						<div class="col-sm-4">
							<input type="password" class="form-control"
									id="password" name="password"
									placeholder="Enter a password" required>
							<div class="form-group form-check">
								<input type="checkbox"
										class="form-check-input"
										id="show_password_check"
										onclick="togglePassword()">
								<label class="form-check-label"
										for="show_password_check">Show Password</label>
							</div>
							<div class="invalid-feedback">
								Please provide a valid password.
							</div>
						</div>
					</div>
					<button class="btn btn-primary" type="submit" name="signup_submission">
            			Sign Up
					</button>
				</form>
				<?php endif; ?>
			</div>
		</div>
		<script>
			// JS for disabling form submissions if there are invalid fields
			(function() {
				'use strict'
				window.addEventListener('load', function() {
					// Fetch all forms and apply custom Bootstrap validation styles
					var forms = document.getElementsByClassName('needs-validation');
					// Loop over them and prevent submission
					var validation = Array.prototype.filter.call(forms, function(form) {
						form.addEventListener('submit', function(event) {
							if (form.checkValidity() == false) {
								event.preventDefault();
								event.stopPropagation();
							}
							form.classList.add('was-validated');
						}, false)
					});
				}, false)
			})();
			// Toggles between chowing and hiding the entered password
			function togglePassword() {
				var password_entry = document.getElementById("password");
        var checkbox_label = document.querySelector("label[for='show_password_check']");

				if (password_entry.type === "password") {
					password_entry.type = "text";
          checkbox_label.textContent = "Hide Password";
				} else {
					password_entry.type = "password";
          checkbox_label.textContent = "Show Password";
				}
			}
		</script>
		<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
				integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
				crossorigin="anonymous"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"
				integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut"
				crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"
				integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k"
				crossorigin="anonymous"></script>
	</body>
</html>