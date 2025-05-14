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
                <h1 class="text-center"><?= $page_title ?></h1>
                <?php
                    if (
                        isset($_SESSION['user_name']) &&
                        isset($_SESSION['user_access_privileges'])
                    ):
                ?>
                <h3 class="text-center mb-5">
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
                    require_once('fileconstants.php');
                    require_once('classes/Character.php');
                    require_once('characters/characterfileconstants.php');
                    
                    $character = new Character($dbc);
                    $result = $character->queryAll();


                    if (mysqli_num_rows($result) > 0):
                ?>
                    <h2 class="text-center">Characters</h2>
                    <div class="p-5">
                <?php
                    echo "<table class='table table-hover table-striped'>";
                    echo "<tr><th>Image</th><th>Name</th><th>Species</th><th></th></tr>";
                    echo $character->displayAsTable($result);
                    echo "</table></div>";

                    else:
                ?>
                        <h3>No Characters Found</h3>
                <?php
                    endif;           
                ?>
                <?php
                    require_once('dbconnection.php');
                    require_once('fileconstants.php');
                    require_once('classes/Species.php');
                    require_once('species/speciesfileconstants.php');
                    $species = new Species($dbc);

                    $species = new Species($dbc);
                    /** @var mysqli_result $result */
                    $result = $species->queryAll();

                    if (mysqli_num_rows($result) > 0):
                ?>
                    <h2 class="text-center">Species</h2>
                    <div class="p-5">
                <?php
                    
                    echo "<table class='table table-hover table-striped'><tbody class='p-5'>";
                    echo "<tr><th>Image</th><th>Name</th><th></th></tr>";
                    echo $species->displayAsTable($result);
                    echo "</tbody></table></div>";

                    else:
                ?>
                        <h3>No Species Found</h3>
                <?php
                    endif;
                ?>
            </div>
        </div>
        <?php require_once('footer.php'); ?>
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