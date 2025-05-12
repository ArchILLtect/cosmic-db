<!--    Author: Nick Hanson
	      Version: 0.3
	      Date: 4/20/25
-->
<?php
    session_start();

    require_once('../pagetitles.php');
    $page_title = CDB_SPECIES_DETAILS_PAGE;
?>
<!DOCTYPE html>
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
            require_once('../navmenu.php');
        ?>
        <div class="card">
            <div class="card-body" style="margin: 0 10% 0 10%;">
                <h1>Species Details</h1>
                <hr/>
                <?php
                    if (isset($_GET['id'])):

                        require_once('../dbconnection.php');
                        require_once('../fileconstants.php');
                        require_once('speciesfileconstants.php');

                        $id = $_GET['id'];

                        $sql = "SELECT * FROM species WHERE species_id = ?";

                        $stmt = mysqli_prepare($dbc, $sql);

                        mysqli_stmt_bind_param($stmt, "i", $id);

                        mysqli_stmt_execute($stmt);

                        $result = mysqli_stmt_get_result($stmt)
                                or trigger_error('Error querying database species',
                                E_USER_ERROR);

                    if (mysqli_num_rows($result) == 1):

                        $row = mysqli_fetch_assoc($result);
                        
                        $species_image_file = $row['image_file'];
                        
                        if (empty($species_image_file)){

                            $species_image_file = CDB_UPLOAD_WEB_PATH
                                    . CDB_DEFAULT_SPECIES_FILENAME;

                        } else {
                            $species_image_file = CDB_UPLOAD_WEB_PATH . $row['image_file'];
                        }
                ?>
                <h2><?= htmlspecialchars($row['name']) ?></h2>
                <div class="row">
                    <div class="col-2">
                        <img src="<?= htmlspecialchars($species_image_file) ?>"
                                class="img-thumbnail"
                                style="max-height: 200px;"
                                alt="Species Image">
                    </div>
                    <div class="col">
                      <table class="table table-striped">
                        <tbody>
                          <tr>
                            <th scope="row">Description</th>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                          </tr>
                          <tr>
                            <th scope="row">Homeworld</th>
                            <td><?= htmlspecialchars($row['homeworld']) ?></td>
                          </tr>
                          <tr>
                            <th scope="row">Traits</th>
                            <td><?= htmlspecialchars($row['traits']) ?></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                </div>
                <hr/>
                <?php
                  if (isset($_SESSION['user_access_privileges']) &&
                      $_SESSION['user_access_privileges'] == 'admin'):
                ?>
                <nav class='nav-link'>Feel free to
                        <a href="editspecies.php?id_to_edit=<?= $row['species_id'] ?>">Edit Details</a>
                        any time.
                </nav>
                <?php endif; ?>
                <?php
                    else:
                ?>
                <h3>No Species Details</h3>
                <?php
                        endif;
                    else:
                ?>
                <h3>No Species Details</h3>
                <?php
                    endif;
                ?>
            </div>
        </div>
        <?php require_once('../footer.php'); ?>
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
