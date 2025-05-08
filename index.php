<!--    Author: Nick Hanson
        Version: 0.1
        Date: 4/20/25
-->
<?php
    session_start();

    require_once('pagetitles.php');
    $page_title = CDB_HOME_PAGE;
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet"
                href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
                integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS"
                crossorigin="anonymous">
        <link rel="stylesheet"
                href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"
                integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf"
                crossorigin="anonymous">
        <title><?= $page_title ?></title>
    </head>
    <body>
        <?php
            require_once('navmenu.php');
        ?>
        <div class="card">
            <div class="card-body">
                <h1><?= $page_title ?></h1>
                <?php
                    if (
                        isset($_SESSION['user_name']) &&
                        isset($_SESSION['user_access_privileges'])
                    ):
                ?>
                <h3>
                    Welcome,
                    <span class="text-success font-weight-bold">
                        <?= $_SESSION['user_name'] ?>
                    </span>
                    ! You have
                    <span class="text-success font-weight-bold">
                        <?= $_SESSION['user_access_privileges'] ?></span>-level access.
                </h3>
                <?php endif; ?>
                    <?php
                        require_once('dbconnection.php');
                        require_once('speciesfileconstants.php');
                        require_once('characterfileconstants.php');

                        $dbc = mysqli_connect(  DB_HOST,
                                                DB_USER,
                                                DB_PASSWORD,
                                                DB_NAME,
                                                DB_PORT)
                                or trigger_error('Error connecting to MySQL server for'
                                .  DB_NAME, E_USER_ERROR);

                        $query = "SELECT id, name, image_file FROM species ORDER BY name";

                        $result = mysqli_query($dbc, $query)
                                or trigger_error('Error querying database species', 
                                E_USER_ERROR);

                        if (mysqli_num_rows($result) > 0):
                    ?>
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th scope="col">Species Name</th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php
                                    while($row = mysqli_fetch_assoc($result))
                                    {
                                        $species_image_file = $row['image_file'];
                                        
                                        if (empty($species_image_file))
                                        {
                                            $species_image_file = CDB_UPLOAD_PATH
                                                    . CDB_DEFAULT_SPECIES_FILENAME;
                                        }
                                        
                                        echo "<tr><td><img src=" . htmlspecialchars($species_image_file)
                                                . " class='img-thumbnail'"
                                                . "style='max-height: 75px;' alt='Species Image'"
                                                . "</td><td class='align-middle'>"
                                                . "<a class='nav-link' href='speciesdetails.php?id="
                                                . $row['id'] . "'>" . htmlspecialchars($row['name'])
                                                . "</a></td><td>";
                                        if (isset($_SESSION['user_access_privileges']) &&
                                                $_SESSION['user_access_privileges'] == 'admin') {
                                            echo "<a class='nav-link'"
                                                    . "href='removespecies.php?id_to_delete=" . $row['id']
                                                    . "'><i class='fas fa-trash-alt'></i></a></td></tr>";
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>        
                    <?php
                        else:
                    ?>
                            <h3>No Species Found</h3>
                    <?php
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