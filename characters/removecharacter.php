<?php
/*  Author: Nick Hanson
	Version: 1.0
	Date: 4/20/25
*/
    $required_access_level = 'admin';
    require_once('../authorizeaccess.php');
    require_once('../pagetitles.php');
    $page_title = CDB_REMOVE_CHARACTER_PAGE;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Remove a Character</title>
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
                <h1>Remove a Character</h1>
                <?php
                require_once('../dbconnection.php');
                require_once('../fileconstants.php');
                require_once('characterfileconstants.php');
                require_once('characterimagefileutil.php');
                
                if (isset($_POST['delete_character_submission']) && isset($_POST['id'])):
                
                    $id = filter_var($_POST['id'],
                            FILTER_SANITIZE_NUMBER_INT);

                    // Query image file from DB
                    $sql = "SELECT image_file FROM characters WHERE character_id = ?";

                    $stmt = mysqli_prepare($dbc, $sql);

                    mysqli_stmt_bind_param($stmt, "i", $id);

                    mysqli_stmt_execute($stmt);

                    $result = mysqli_stmt_get_result($stmt)
                            or trigger_error('Error querying database chracters',
                            E_USER_ERROR);
                    
                    if (mysqli_num_rows($result) == 1)
                    {
                        $row = mysqli_fetch_assoc($result);

                        $character_image_file = $row['image_file'];

                        if (!empty($character_image_file))
                        {
                            removeCharacterImageFile($character_image_file);
                        }
                    }
                    $sql = "DELETE FROM character_species WHERE character_id = ?";
                    $stmt = mysqli_prepare($dbc, $sql);
                    mysqli_stmt_bind_param($stmt, "i", $id);
                    mysqli_stmt_execute($stmt)
                            or trigger_error('Error querying database character', 
                            E_USER_ERROR)
                    ;

                    $sql = "DELETE FROM characters WHERE character_id = ?";
                    $stmt = mysqli_prepare($dbc, $sql);
                    mysqli_stmt_bind_param($stmt, "i", $id);
                    mysqli_stmt_execute($stmt)
                            or trigger_error('Error querying database character', 
                            E_USER_ERROR)
                    ;

                    header("Location: index.php");
                    exit;
                    
                elseif (isset($_POST['do_not_delete_character_submission'])):

                    header("Location: index.php");
                    exit;
                
                elseif (isset($_GET['id_to_delete'])):
                    ?>
                    <h3 class="text-danger">Confirm Deletion of the Following
                        Character</h3>
                    <?php
                        $id = $_GET['id_to_delete'];
                        
                        $sql = "SELECT * FROM characters WHERE character_id = ?";

                        $stmt = mysqli_prepare($dbc, $sql);

                        mysqli_stmt_bind_param($stmt, "i", $id);

                        mysqli_stmt_execute($stmt);

                        $result = mysqli_stmt_get_result($stmt)
                                or trigger_error('Error querying database characters',
                                E_USER_ERROR);

                        if (mysqli_num_rows($result) == 1):
                            $row = mysqli_fetch_assoc($result);

                            $character_image_file = $row['image_file'];

                            if (empty($character_image_file)){

                                $character_image_file = CDB_UPLOAD_WEB_PATH
                                        . CDB_DEFAULT_CHARACTER_FILENAME;
    
                            } else {
                                $character_image_file = CDB_UPLOAD_WEB_PATH . $row['image_file'];
                            }
                    ?>
                    <h1><?= htmlspecialchars($row['name']) ?></h1>
                    <div class='row'>
                        <div class='col-2'>
                            <img src="<?= htmlspecialchars($character_image_file) ?>"
                                    class="img-thumbnail"
                                    style="max-height: 200px;"
                                    alt="Character image">
                        </div>
                        <div class="col">
                    <table class="table table-striped">
                        <tbody>
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
                    <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
                        <div class="form-group">
                            <div class="d-flex justify-content-center mb-5">
                                <button class="btn btn-danger btn-secondary mr-3" type="submit"
                                        name="delete_character_submission">
                                    Delete Character
                                </button>
                                <button class="btn btn-primary ml-3" type="submit"
                                        name="do_not_delete_character_submission">
                                    Don't Delete!
                                </button>
                            </div>

                            <input type="hidden" name="id"
                                    value="<?= $id ?>">
                        </div>
                    </form>
                    <?php
                        else:
                    ?>
                        <h3>No Character Details</h3>
                    <?php
                        endif;
                else: // Unintended page link = No character to remove, go back to index

                    header("Location: index.php");
                    exit;
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
