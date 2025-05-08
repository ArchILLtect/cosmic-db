<!--    Author: Nick Hanson
        Version: 0.3
        Date: 3/12/25
-->
<?php
    require_once('authorizeaccess.php');
    require_once('pagetitles.php');
    $page_title = MR_EDIT_MOVIE_PAGE;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Edit a Movie</title>
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
                <h1>Edit a Movie</h1>
                <hr/>
                <?php
                    require_once('dbconnection.php');
                    require_once('movieimagefileutil.php');

                    $dbc = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
                                or trigger_error('Error connecting to MySQL server for '
                                . DB_NAME, E_USER_ERROR);

                    $genres = [
                        'Action', 'Adventure', 'Comedy', 'Documentary', 'Drama',
                        'Fantasy', 'Horror', 'Mystery', 'Romance',
                        'Science Fiction', 'Thriller'
                    ];
                    
                    if (isset($_GET['id_to_edit'])) {
                        $id_to_edit = $_GET['id_to_edit'];
                        $query = "SELECT * FROM movieListing WHERE id = $id_to_edit";

                        $result = mysqli_query($dbc, $query)
                                or trigger_error('Error querying database movieListing',
                                E_USER_ERROR);
                        
                         if (mysqli_num_rows($result) == 1) {
                            $row = mysqli_fetch_assoc($result);
                            
                            $movie_title = $row['title'];
                            $movie_year = $row['release_year'];
                            $movie_rating = $row['rating'];
                            $movie_director = $row['director'];
                            $movie_runtime = $row['running_time_in_minutes'];
                            $movie_genre_text = $row['genre'];
                            $movie_image_file = $row['image_file'];
                            
                            if (empty($movie_image_file))
                            {
                                $movie_image_file_displayed = ML_UPLOAD_PATH
                                        . ML_DEFAULT_MOVIE_FILENAME;
                            }
                            else
                            {
                                $movie_image_file_displayed = $movie_image_file;
                            }
                            
                            $checked_movie_genres = explode(",", $movie_genre_text);
                        }
                    }
                    elseif (isset(
                            $_POST['edit_movie_submission'],
                            $_POST['movie_title'],
                            $_POST['movie_release_year'],
                            $_POST['movie_rating'],
                            $_POST['movie_director'],
                            $_POST['movie_running_time_in_minutes'],
                            $_POST['id_to_update'],
                            $_POST['movie_image_file']
                    )) {

                        $movie_title = filter_var($_POST['movie_title'],
                                FILTER_SANITIZE_SPECIAL_CHARS);
                        $movie_year = filter_var($_POST['movie_release_year'],
                                FILTER_SANITIZE_NUMBER_INT);
                        $movie_rating = filter_var($_POST['movie_rating'],
                                FILTER_SANITIZE_SPECIAL_CHARS);
                        $movie_director = filter_var($_POST['movie_director'],
                                FILTER_SANITIZE_SPECIAL_CHARS);
                        $movie_runtime = filter_var($_POST['movie_running_time_in_minutes'],
                                FILTER_SANITIZE_NUMBER_INT);
                        $checked_movie_genres = $_POST['movie_genre_checkbox'];
                        $id_to_update = filter_var($_POST['id_to_update'],
                                FILTER_SANITIZE_NUMBER_INT);
                        $movie_image_file = filter_var($_POST['movie_image_file'],
                                FILTER_SANITIZE_NUMBER_INT);
                        
                        $movie_genre_text = "";
                    
                        if (isset($checked_movie_genres)) {
                                $movie_genre_text = implode(",", $checked_movie_genres);
                        }

                        if (empty($movie_image_file))
                        {
                            $movie_image_file_displayed = ML_UPLOAD_PATH
                                    . ML_DEFAULT_MOVIE_FILENAME;
                        }
                        else
                        {
                            $movie_image_file_displayed = $movie_image_file;
                        }

                        /*
                        Here is where we will deal with the file by calling validateMovieImageFile().
                        The function will validate that the movie image file is not greater than 128
                        characters, is the right image type (jpg/png/gif), and not greater than 512kb.
                        This function will return an empty string ('') if the file validates successfully,
                        otherwise, the string will contain error text to be output to the web page before
                        re-displaying the form.
                        */

                        $file_error_message = validateMovieImageFile();

                        if (empty($file_error_message))
                        {
                            $movie_image_file_path = addMovieImageFileReturnPathLocation();

                            // IF new image selected, set it to be updated in the db.
                            if(!empty($movie_image_file_path))
                            {
                                // IF replacing an image (other then the default), remove it
                                if(!empty($movie_image_file))
                                {
                                    removeMovieImageFile($movie_image_file);
                                }

                                $movie_image_file = $movie_image_file_path;
                            }

                            $sql = "UPDATE movieListing SET title = ?, "
                                    . "release_year = ?, rating = ?, director = ?, "
                                    . "running_time_in_minutes = ?, genre = ?,"
                                    . "image_file = ? WHERE id = ?";
                            
                            $stmt = mysqli_prepare($dbc, $sql);

                            mysqli_stmt_bind_param($stmt, "sississi", $movie_title, $movie_year,
                                    $movie_rating, $movie_director, $movie_runtime,
                                    $movie_genre_text, $movie_image_file, $id_to_update);
                            
                            mysqli_stmt_execute($stmt);
                            
                            $nav_link = 'moviedetails.php?id=' . $id_to_update;

                            header("Location: $nav_link");
                            exit;
                        }
                        else
                        {
                            // Display error message
                            echo "<h5><p class='text-danger'>" . $file_error_message
                                    . "</p></h5>";
                        }
                    } else { // Unintended page link - No movie to edit, link back to index
                        header("Location: index.php");
                        exit;
                    }
                ?>
                <div class="row">
                    <div class="col">
                        <form enctype="multipart/form-data" class="needs-validation" novalidate method="POST"
                                action="<?= $_SERVER['PHP_SELF'] ?>">
                            <div class="from-group row">
                                <label for="movie_title" class="col-sm-3 col-form-label-lg">
                                    Title
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="movie_title"
                                            name="movie_title" placeholder="Title"
                                            value="<?= $movie_title ?>" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid movie title.
                                    </div>
                                </div>
                            </div>
                            <div class="from-group row">
                                <label for="movie_release_year" class="col-sm-3 col-form-label-lg">
                                    Release Year
                                </label>
                                <div class="col-sm-8">
                                    <input type="number" class="form-control" id="movie_release_year"
                                            min="1880" max="2100" step="1" name="movie_release_year"
                                            placeholder="Release Year"
                                            value="<?= $movie_year ?>" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid movie release year.
                                    </div>
                                </div>
                            </div>
                            <div class="from-group row">
                                <label for="movie_rating" class="col-sm-3 col-form-label-lg">
                                    Rating
                                </label>
                                <div class="col-sm-8">
                                    <select class="custom-select" id="movie_rating"
                                            name="movie_rating" value="<?= $movie_rating ?>" required>
                                        <option value="" disabled selected>Rating...</option>
                                        <option value="G" <?= $movie_rating == 'G' ?
                                                'selected' : '' ?>>G
                                        </option>
                                        <option value="PG" <?= $movie_rating == 'PG' ?
                                                'selected' : '' ?>>PG
                                        </option>
                                        <option value="PG-13" <?= $movie_rating == 'PG-13' ?
                                                'selected' : '' ?>>PG-13
                                        </option>
                                        <option value="R" <?= $movie_rating == 'R' ?
                                                'selected' : '' ?>>R
                                        </option>
                                    </select>
                                    <div class="invalid-feedback">
                                        Please select a valid movie rating.
                                    </div>
                                </div>
                            </div>
                            <div class="from-group row">
                                <label for="movie_director" class="col-sm-3 col-form-label-lg">
                                    Director
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="movie_director"
                                            name="movie_director" placeholder="Director"
                                            value="<?= $movie_director ?>" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid movie director.
                                    </div>
                                </div>
                            </div>
                            <div class="from-group row">
                                <label for="movie_running_time_in_minutes"
                                        class="col-sm-3 col-form-label-lg">
                                    Running Time (min.)
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control"
                                            id="movie_running_time_in_minutes"
                                            name="movie_running_time_in_minutes"
                                            placeholder="Running time (in minutes)"
                                            value="<?= $movie_runtime ?>" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid running time in minutes.
                                    </div>
                                </div>
                            </div>
                            <div class="from-group row">
                                <label class="col-sm-3 col-form-label-lg">Genre</label>
                                <div class="col-sm-8">
                                <?php
                                    foreach ($genres as $genre) {
                                ?>
                                        <div class="form-check form-check-inline col-sm-3">
                                            <input class="form-check-input" type="checkbox"
                                                id="movie_genre_checkbox_action_<?= $genre ?>"
                                                name="movie_genre_checkbox[]"
                                                value="<?= $genre ?>"
                                                <?= in_array(
                                                        $genre, $checked_movie_genres
                                                        ) ? 'checked' : '' ?>>
                                            <label class="form-check-label"
                                                    for="movie_genre_checkbox_action_<?= $genre ?>">
                                                <?= $genre ?></label>
                                        </div>
                                        <div class="invalid-feedback">
                                            Please provide a valid movie genre.
                                        </div>
                                <?php
                                    }
                                ?>
                                </div>
                            </div>
                            <div class="from-group row">
                                <label for="movie_image_file"
                                        class="col-sm-3 col-form-label-lg">Movie Image File</label>
                                <div class="col-sm-8">
                                    <input type="file" class="form-control-file"
                                            id="movie_image_file" name="movie_image_file">
                                </div>
                            </div>
                            <button class="btn btn-primary" type="submit"
                                    name="edit_movie_submission">
                                Update Movie
                            </button>
                            <input type="hidden" name="id_to_update"
                                    value="<?= $id_to_edit ?>">
                            <input type="hidden" name="movie_image_file"
                                    value="<?= $movie_image_file ?>">
                        </form>
                    </div>
                    <div class="col-3">
                        <img src="<?= htmlspecialchars($movie_image_file_displayed) ?>"
                                class="img-thumbnail" style="max-height:400 px;"
                                alt="Movie Image">
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
                </script>
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
