<?php
/*  Author: Nick Hanson
	Version: 1.0
	Date: 4/20/25
*/
    require_once('authorizeaccess.php');
    require_once('pagetitles.php');
    $page_title = 'Edit Profile';

    require_once('dbconnection.php');
    require_once('fileconstants.php'); // optional if you have upload path/constants
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Edit Profile</title>
        <link rel="stylesheet"
                href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
                integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS"
                crossorigin="anonymous">
    </head>
    <body>
    <?php require_once('navmenu.php'); ?>

    <div class="card" style="margin-bottom: 10%;">
        <div class="card-body" style="margin: 0 10% 0 10%;">
            <h1>Edit Profile</h1>
            <hr/>
            <?php
                $user_id = $_SESSION['user_id'];

                $sql = "SELECT name, user_name, image_file FROM user WHERE id = ?";

                $stmt = mysqli_prepare($dbc, $sql);
                mysqli_stmt_bind_param($stmt, "i", $user_id);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                $user = mysqli_fetch_assoc($result);

                $name = $user['name'];
                $username = $user['user_name'];
                $profile_image = $user['image_file'];

                $profile_image_display = empty($profile_image)
                    ? CDB_UPLOAD_WEB_PATH . 'default_avatar.png'
                    : CDB_UPLOAD_WEB_PATH . $profile_image;

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $new_name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
                    $new_username = filter_var($_POST['username'], FILTER_SANITIZE_SPECIAL_CHARS);
                    $new_password = $_POST['password'];
                    $confirm_password = $_POST['confirm_password'];

                    $errors = [];

                    if ($new_password !== $confirm_password) {
                        $errors[] = "Passwords do not match.";
                    }

                    // Handle file upload (optional)
                    $new_profile_image = $profile_image;
                    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                        $file_tmp = $_FILES['profile_image']['tmp_name'];
                        $file_name = basename($_FILES['profile_image']['name']);
                        $target = CDB_UPLOAD_PATH . $file_name;
                        if (move_uploaded_file($file_tmp, $target)) {
                            $new_profile_image = $file_name;
                        } else {
                            $errors[] = "Failed to upload new profile image.";
                        }
                    }

                    if (empty($errors)) {
                        if (!empty($new_password)) {
                            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                            $update_sql = "UPDATE user SET name=?, user_name=?, password=?, image_file=? WHERE id=?";
                            $stmt = mysqli_prepare($dbc, $update_sql);
                            mysqli_stmt_bind_param($stmt, "ssssi", $new_name, $new_username,
                                    $hashed_password, $new_profile_image, $user_id);
                        } else {
                            $update_sql = "UPDATE user SET name=?, user_name=?, image_file=? WHERE id=?";
                            $stmt = mysqli_prepare($dbc, $update_sql);
                            mysqli_stmt_bind_param($stmt, "sssi", $new_name, $new_username,
                                    $new_profile_image, $user_id);
                        }

                        if (mysqli_stmt_execute($stmt)) {
                            echo "<div class='alert alert-success'>Profile updated successfully!</div>";
                            $name = $new_name;
                            $username = $new_username;
                            $profile_image_display = CDB_UPLOAD_WEB_PATH . $new_profile_image;
                        } else {
                            echo "<div class='alert alert-danger'>Update failed.</div>";
                        }
                    } else {
                        foreach ($errors as $e) {
                            echo "<div class='alert alert-danger'>$e</div>";
                        }
                    }
                }
            ?>
            <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" name="name"
                            value="<?= htmlspecialchars($name) ?>" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" name="username"
                            value="<?= htmlspecialchars($username) ?>" required>
                </div>
                <br>
                <div class="form-group">
                    <label for="password">New Password (leave blank to keep current)</label>
                    <input type="password" class="form-control" name="password">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" class="form-control" name="confirm_password">
                </div>
                <br>
                <div class="form-group">
                    <label for="profile_image">Profile Picture</label>
                    <input type="file" class="form-control-file" name="profile_image">
                </div>
                <div class="mb-5">
                    <img src="<?= htmlspecialchars($profile_image_display) ?>" class="img-thumbnail"
                            style="max-height: 200px;" alt="Profile Image">
                </div>
                <div class="d-flex justify-content-center mb-5">
                    <button type="submit" class="btn btn-primary mr-3">Update Profile</button>
                    <button type="button" class="btn btn-danger btn-secondary ml-3"
                            onclick="window.location.href='index.php'">Cancel</button>
                </div>

            </form>
        </div>
    </div>
    <?php require_once('footer.php'); ?>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
            integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
            crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"
            integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k"
            crossorigin="anonymous"></script>
    </body>
</html>
