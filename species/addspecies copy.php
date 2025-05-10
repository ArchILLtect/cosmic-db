<!--    Author: Nick Hanson
        Version: 0.1
        Date: 4/20/25
-->
<?php
    require_once('../authorizeaccess.php');
    require_once('../pagetitles.php');
    $page_title = CDB_ADD_SPECIES_PAGE;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Add a Species</title>
        <link rel="stylesheet"
                href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
                integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS"
                crossorigin="anonymous">
    </head>
    <body>
        <?php
            require_once('../navmenu.php');
            require_once('../fileconstants.php');
            require_once('speciesfileconstants.php');
        ?>
        <div class="card">
            <div class="card-body">
                <h1>Add a Species</h1>
                <hr/>
                <?php
                    // Initialization
                    $display_add_species_form = true;
                    
                    $species_name = "";
                    $species_description = "";
                    $species_homeworld = "";
                    $species_trait_text = "";
                    $checked_species_traits = null;

                    $traits = CDB_SPECIES_TRAITS;

                    if (isset(
                            $_POST['add_species_submission'],
                            $_POST['species_name'],
                            $_POST['species_description'],
                            $_POST['species_homeworld']
                    )) {
                        require_once('../dbconnection.php');
                        require_once('speciesimagefileutil.php');

                        $species_name = filter_var($_POST['species_name'],
                                FILTER_SANITIZE_SPECIAL_CHARS);
                        $species_description = filter_var($_POST['species_description'],
                                FILTER_SANITIZE_SPECIAL_CHARS);
                        $species_homeworld = filter_var($_POST['species_homeworld'],
                                FILTER_SANITIZE_SPECIAL_CHARS);
                        $checked_species_traits = $_POST['species_trait_checkbox'];
                        
                        $species_trait_text = "";
                    
                    if (isset($checked_species_traits))
                        {
                            $species_trait_text = implode(",", $checked_species_traits);
                        }
                        
                        /*
                        Here is where we will deal with the file by calling validateSpeciesImageFile().
                        The function will validate that the species image file is the right image type
                        (jpg/png/gif), and not greater than 512kb. This function will return an empty
                        string ('') if the file validates successfully, otherwise, the string will
                        contain error text to be output to the web page before re-displaying the form.
                        */
                        $file_error_message = validateSpeciesImageFile();
                        
                        if(empty($file_error_message))
                        {

                            $dbc = mysqli_connect(  DB_HOST,
                                    DB_USER,
                                    DB_PASSWORD,
                                    DB_NAME,
                                    DB_PORT)
                                or trigger_error('Error connecting to MySQL server for '
                                . DB_NAME, E_USER_ERROR);
                        
                        $species_image_file_path = addSpeciesImageFileReturnPathLocation();

                        $sql = "INSERT INTO species (name, description, homeworld, "
                                . "traits, image_file) VALUES (?, ?, ?, ?, ?)";
                        
                        $stmt = mysqli_prepare($dbc, $sql);

                        mysqli_stmt_bind_param($stmt, "sssss", $species_name, $species_description,
                                $species_homeworld, $species_trait_text, $species_image_file_path);
                        
                        if (mysqli_stmt_execute($stmt)) {
                            echo "Species added successfully!";
                        } else {
                            echo "Error adding species.";
                        }

                        if(empty($species_image_file_path))
                        {
                            $species_image_file_path = CDB_UPLOAD_WEB_PATH
                                    . CDB_DEFAULT_SPECIES_FILENAME;
                        }

                        $display_add_species_form = false;
                ?>
                <h3 class="text-info">The following species record was added:</h3>

                <h1><?= $species_name ?></h1>
                <div class="row">
                    <div class="col-2">
                        <img src="<?= htmlspecialchars($species_image_file_path) ?>" class="img-thumbnail"
                                style="max-height: 200px;" alt="Species Image">
                    </div>
                    <div class="col">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th scope="row">Description</th>
                                    <td><?= $species_description ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Homeworld</th>
                                    <td><?= $species_homeworld ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Traits</th>
                                    <td><?= $species_trait_text ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr/>
                <p>Would you like to <a href='<?= $_SERVER['PHP_SELF'] ?>'>
                        add another species</a>?</p>
                <?php
                    }
                    else
                    {
                        // Echo error message
                        echo "<h5><p class='text-danger'>" . $file_error_message . "</p></h5>";
                    }
                }
                        if ($display_add_species_form)
                    {
                ?>
                <form enctype="multipart/form-data" class="needs-validation" novalidate method="POST"
                        action="<?= $_SERVER['PHP_SELF'] ?>">
                    <div class="from-group row">
                        <label for="species_name" class="col-sm-3 col-form-label-lg">
                            Species Name
                        </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="species_name"
                                    name="species_name" placeholder="Species Name" required>
                            <div class="invalid-feedback">
                                Please provide a valid species name.
                            </div>
                        </div>
                    </div>
                    <div class="from-group row">
                        <label for="species_description" class="col-sm-3 col-form-label-lg">
                            Species Description
                        </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="species_description"
                                    name="species_description" placeholder="Species Description"
                                    required>
                            <div class="invalid-feedback">
                                Please provide a valid species description.
                            </div>
                        </div>
                    </div>
                    <div class="from-group row">
                        <label for="species_homeworld" class="col-sm-3 col-form-label-lg">
                            Species Homeworld
                        </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="species_homeworld"
                                    name="species_homeworld" placeholder="Species Howmeworld"
                                    required>
                            <div class="invalid-feedback">
                                Please provide a valid species homeworld.
                            </div>
                        </div>
                    </div>
                    <div class="from-group row">
                        <label class="col-sm-3 col-form-label-lg">Traits</label>
                        <div class="col-sm-8">
                        <?php
                            foreach ($traits as $trait) {
                        ?>
                                <div class="form-check form-check-inline col-sm-3">
                                    <input class="form-check-input" type="checkbox"
                                        id="species_trait_checkbox_action_<?= $trait ?>"
                                        name="species_trait_checkbox[]"
                                        value="<?= $trait ?>">
                                    <label class="form-check-label"
                                            for="species_trait_checkbox_action_<?= $trait ?>">
                                        <?= $trait ?></label>
                                </div>
                            <div class="invalid-feedback">
                                Please provide a valid species trait.
                            </div>
                        <?php
                            }
                        ?>
                        </div>
                    </div>
                    <br>
                    <div class="from-group row">
                        <label for="species_image_file"
                                class="col-sm-3 col-form-label-lg">Species Image File</label>
                        <div class="col-sm-8">
                            <input type="file" class="form-control-file"
                                    id="species_image_file" name="species_image_file">
                        </div>
                    </div>
                    <button class="btn btn-primary" type="submit"
                            name="add_species_submission">
                        Add Species
                    </button>
                </form>
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
                <?php
                    } // Display add species form
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