<!--    Author: Nick Hanson
        Version: 0.4
        Date: 3/12/25
-->
<?php
    session_start();

    require_once('pagetitles.php');
    $page_title = MR_HOME_PAGE;
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
                        require_once('movielistingfileconstants.php');

                        $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                                or trigger_error('Error connecting to MySQL server for'
                                .  DB_NAME, E_USER_ERROR);

                        $query = "SELECT id, title, image_file FROM movieListing ORDER BY title";

                        $result = mysqli_query($dbc, $query)
                                or trigger_error('Error querying database movieListing', 
                                E_USER_ERROR);

                        if (mysqli_num_rows($result) > 0):
                    ?>
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th scope="col">Movie Title</th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php
                                    while($row = mysqli_fetch_assoc($result))
                                    {
                                        $movie_image_file = $row['image_file'];
                                        
                                        if (empty($movie_image_file))
                                        {
                                            $movie_image_file = ML_UPLOAD_PATH
                                                    . ML_DEFAULT_MOVIE_FILENAME;
                                        }
                                        
                                        echo "<tr><td><img src=" . htmlspecialchars($movie_image_file)
                                                . " class='img-thumbnail'"
                                                . "style='max-height: 75px;' alt='Movie Image'"
                                                . "</td><td class='align-middle'>"
                                                . "<a class='nav-link' href='moviedetails.php?id="
                                                . $row['id'] . "'>" . htmlspecialchars($row['title'])
                                                . "</a></td><td>";
                                        if (isset($_SESSION['user_access_privileges']) &&
                                                $_SESSION['user_access_privileges'] == 'admin') {
                                            echo "<a class='nav-link'"
                                                    . "href='removemovie.php?id_to_delete=" . $row['id']
                                                    . "'><i class='fas fa-trash-alt'></i></a></td></tr>";
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>        
                    <?php
                        else:
                    ?>
                            <h3>No Movies Found :-(</h3>
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