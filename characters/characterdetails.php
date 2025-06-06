<?php
/*  Author: Nick Hanson
	Version: 1.0
	Date: 4/20/25
*/
    session_start();

    require_once('../pagetitles.php');
    $page_title = CDB_CHARACTER_DETAILS_PAGE;
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
        <div class="card" style="margin-bottom: 10%;">
            <div class="card-body" style="margin: 0 10% 0 10%;">
                <h1>Character Details</h1>
                <hr/>
                <?php
                    if (isset($_GET['id'])):

                        require_once('../dbconnection.php');
                        require_once('../fileconstants.php');
                        require_once('characterfileconstants.php');

                        $id = $_GET['id'];

                        $sql = "SELECT * FROM characters WHERE character_id = ?";

                        $stmt = mysqli_prepare($dbc, $sql);

                        mysqli_stmt_bind_param($stmt, "i", $id);

                        mysqli_stmt_execute($stmt);

                        $result = mysqli_stmt_get_result($stmt)
                                or trigger_error('Error querying database characters',
                                E_USER_ERROR);

                    if (mysqli_num_rows($result) == 1):
                        $sql = "SELECT characters.*, species.name AS species_name
                                FROM characters
                                LEFT JOIN character_species ON characters.character_id = character_species.character_id
                                LEFT JOIN species ON character_species.species_id = species.species_id
                                WHERE characters.character_id = ?";

                        $stmt = mysqli_prepare($dbc, $sql);

                        mysqli_stmt_bind_param($stmt, "i", $id);

                        mysqli_stmt_execute($stmt);

                        $result = mysqli_stmt_get_result($stmt)
                                or trigger_error('Error querying database characters',
                                E_USER_ERROR);


                        $row = mysqli_fetch_assoc($result);
                        
                        $character_image_file = $row['image_file'];
                        
                        if (empty($character_image_file)) {

                            $character_image_file = CDB_UPLOAD_WEB_PATH
                                    . CDB_DEFAULT_CHARACTER_FILENAME;

                        } else {
                            $character_image_file = CDB_UPLOAD_WEB_PATH . $row['image_file'];
                        }
                ?>
                <h2><?= htmlspecialchars($row['name']) ?></h2>
                <div class="row">
                    <div class="col-2">
                        <img src="<?= htmlspecialchars($character_image_file) ?>"
                                class="img-thumbnail"
                                style="max-height: 200px;"
                                alt="Character Image">
                    </div>
                    <div class="col">
                      <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th scope="row">Species</th>
                                <td><?= htmlspecialchars($row['species_name'] ?? 'Unknown') ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Age</th>
                                <td><?= htmlspecialchars($row['age']) ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Role</th>
                                <td><?= htmlspecialchars($row['role']) ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Personality</th>
                                <td><?= htmlspecialchars($row['personality']) ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Evo Powers</th>
                                <td><?= htmlspecialchars($row['evo_powers']) ?></td>
                            </tr>
                            <tr>
                                <th scope="row">History</th>
                                <td><?= htmlspecialchars($row['history']) ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Notes</th>
                                <td><?= htmlspecialchars($row['notes']) ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Traits</th>
                                <td><?= htmlspecialchars($row['traits']) ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Skills</th>
                                <td><?= htmlspecialchars($row['skills']) ?></td>
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
                        <a href="editcharacter.php?id_to_edit=<?= $row['character_id'] ?>">Edit Details</a>
                        any time.
                </nav>
                <?php endif; ?>
                <?php
                    else:
                ?>
                <h3>No Character Details</h3>
                <?php
                        endif;
                    else:
                ?>
                <h3>No Character Details</h3>
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