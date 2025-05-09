<!--    Author: Nick Hanson
	      Version: 0.3
	      Date: 4/20/25
-->
<?php
    $required_access_level = 'admin';
    require_once('../authorizeaccess.php');
    require_once('../pagetitles.php');
    $page_title = CDB_REMOVE_CHARACTER_PAGE;
?>
<html>
    <head>
        <title>Remove a Species</title>
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
            <div class="card-body">
                <h1>Remove a Species</h1>
                <?php
                require_once('../dbconnection.php');
                require_once('../fileconstants.php');
                require_once('speciesimagefileutil.php');

                $dbc = mysqli_connect(  DB_HOST,
                                        DB_USER,
                                        DB_PASSWORD,
                                        DB_NAME,
                                        DB_PORT)
                        or trigger_error('Error connecting to MySQL server for'
                        .  DB_NAME, E_USER_ERROR);
                
                if (isset($_POST['delete_species_submission']) && isset($_POST['id'])):
                
                    $id = filter_var($_POST['id'],
                            FILTER_SANITIZE_SPECIAL_CHARS);

                    // Query image file from DB
                    $sql = "SELECT image_file FROM species WHERE species_id = ?";

                    $stmt = mysqli_prepare($dbc, $sql);

                    mysqli_stmt_bind_param($stmt, "i", $id);

                    mysqli_stmt_execute($stmt);

                    $result = mysqli_stmt_get_result($stmt)
                            or trigger_error('Error querying database species',
                            E_USER_ERROR);
                    
                    if (mysqli_num_rows($result) == 1)
                    {
                        $row = mysqli_fetch_assoc($result);

                        $species_image_file = $row['image_file'];

                        if (!empty($species_image_file))
                        {
                            removeSpeciesImageFile($species_image_file);
                        }
                    }

                    $query = "DELETE FROM species WHERE species_id = $id"; // TODO Why not parameterized?

                    $result = mysqli_query($dbc, $query)
                            or trigger_error('Error querying database species', 
                            E_USER_ERROR);
                    
                    header("Location: index.php");
                    exit;
                    
                elseif (isset($_POST['do_not_delete_species_submission'])):

                    header("Location: index.php");
                    exit;
                
                elseif (isset($_GET['id_to_delete'])):
                    ?>
                    <h3 class="text-danger">Confirm Deletion of the Following
                            Species Details</h3>
                    <?php
                        $id = $_GET['id_to_delete'];
                        
                        $query = "SELECT * FROM species WHERE species_id = $id"; // TODO Why not parameterized?

                        $result = mysqli_query($dbc, $query)
                                or trigger_error('Error querying database species', 
                                E_USER_ERROR);

                        if (mysqli_num_rows($result) == 1):
                            $row = mysqli_fetch_assoc($result);

                            $species_image_file = $row['image_file'];

                            if (empty($species_image_file))
                            {
                                $species_image_file = CDB_UPLOAD_WEB_PATH
                                        . CDB_DEFAULT_SPECIES_FILENAME;
                            }
                    ?>
                    <h1><?= htmlspecialchars($row['name']) ?></h1>
                    <div class='row'>
                        <div class='col-2'>
                            <img src="<?= htmlspecialchars($species_image_file) ?>"
                                    class="img-thumbnail"
                                    style="max-height: 200px;"
                                    alt="Species image">
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
                    <form method="POST" action="<?= $_SERVER['PHP_SELF'] ?>">
                        <div class="from-group row">
                            <div class="col-sm-8">
                                <button class="btn btn-danger" type="submit"
                                        name="delete_species_submission">
                                    Delete Species
                                </button>
                            </div>
                            <div class="col-sm-8">
                                <button class="btn btn-success" type="submit"
                                        name="do_not_delete_species_submission">
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
                        <h3>No Species Details</h3>
                    <?php
                        endif;
                else: // Unintended page link = No species to remove, go back to index

                    header("Location: index.php");
                    exit;
                endif;
                ?>
            </div>
        </div>
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
