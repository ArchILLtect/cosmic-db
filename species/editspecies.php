<?php
/*  Author: Nick Hanson
	Version: 1.0
	Date: 4/20/25
*/
    require_once('../authorizeaccess.php');
    require_once('../pagetitles.php');
    $page_title = CDB_EDIT_SPECIES_PAGE;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Edit a Species</title>
        <link rel="stylesheet"
                href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
                integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS"
                crossorigin="anonymous">
    </head>
    <body>
        <?php
            require_once('../navmenu.php');
            require_once('../fileconstants.php');
            require_once('../helpers.php');
            require_once('speciesfileconstants.php');
        ?>
        <div class="card">
            <div class="card-body" style="margin: 0 10% 0 10%;">
                <h1>Edit a Species</h1>
                <hr/>
                <?php
                    require_once('../dbconnection.php');
                    require_once('speciesimagefileutil.php');

                    $traits = CDB_SPECIES_TRAITS;
                    
                    if (isset($_GET['id_to_edit'])) {
                        $id_to_edit = $_GET['id_to_edit'];
                        $query = "SELECT * FROM species WHERE species_id = $id_to_edit";

                        $result = mysqli_query($dbc, $query)
                                or trigger_error('Error querying database species',
                                E_USER_ERROR);
                        
                         if (mysqli_num_rows($result) == 1) {
                            $row = mysqli_fetch_assoc($result);
                            
                            $species_name = $row['name'];
                            $species_description = $row['description'];
                            $species_homeworld = $row['homeworld'];
                            $species_trait_text = $row['traits'];
                            $species_image_file = $row['image_file'];
                            
                            if (empty($species_image_file))
                            {
                                $species_image_file_displayed = CDB_UPLOAD_WEB_PATH
                                        . CDB_DEFAULT_SPECIES_FILENAME;
                            }
                            else
                            {
                                $species_image_file_displayed = $species_image_file;
                            }
                            
                            $checked_species_traits = explode(",", $species_trait_text);
                        }
                    }
                    elseif (isset(
                            $_POST['add_species_submission'],
                            $_POST['species_name'],
                            $_POST['species_description'],
                            $_POST['species_homeworld'],
                            $_POST['id_to_update'],
                            $_POST['species_image_file']
                    )) {

                        $species_name = filter_var($_POST['species_name'],
                                FILTER_SANITIZE_SPECIAL_CHARS);
                        $species_description = filter_var($_POST['species_description'],
                                FILTER_SANITIZE_SPECIAL_CHARS);
                        $species_homeworld = filter_var($_POST['species_homeworld'],
                                FILTER_SANITIZE_SPECIAL_CHARS);
                        $checked_species_traits = $_POST['species_trait_checkbox'];
                        $id_to_update = filter_var($_POST['id_to_update'],
                                FILTER_SANITIZE_NUMBER_INT);
                        $species_image_file = filter_var($_POST['species_image_file'],
                                FILTER_SANITIZE_SPECIAL_CHARS);
                        
                        $species_trait_text = "";
                    
                        if (isset($checked_species_traits)) {
                                $species_trait_text = implode(",", $checked_species_traits);
                        }

                        if (empty($species_image_file))
                        {
                            $species_image_file_displayed = CDB_UPLOAD_WEB_PATH
                                    . CDB_DEFAULT_SPECIES_FILENAME;
                        }
                        else
                        {
                            $species_image_file_displayed = $species_image_file;
                        }

                        /*
                        Here is where we will deal with the file by calling validateSpeciesImageFile().
                        The function will validate that the species image file is not greater than 128
                        characters, is the right image type (jpg/png/gif), and not greater than 512kb.
                        This function will return an empty string ('') if the file validates successfully,
                        otherwise, the string will contain error text to be output to the web page before
                        re-displaying the form.
                        */

                        $file_error_message = validateSpeciesImageFile();

                        if (empty($file_error_message))
                        {
                            $species_image_file_path = addSpeciesImageFileReturnPathLocation();

                            // IF new image selected, set it to be updated in the db.
                            if(!empty($species_image_file_path))
                            {
                                // IF replacing an image (other then the default), remove it
                                if(!empty($species_image_file))
                                {
                                    removeSpeciesImageFile($species_image_file);
                                }

                                $species_image_file = $species_image_file_path;
                            }

                            $sql = "UPDATE species SET name = ?, "
                                    . "description = ?, homeworld = ?, traits = ?,"
                                    . "image_file = ? WHERE species_id = ?";
                            
                            $stmt = mysqli_prepare($dbc, $sql);

                            mysqli_stmt_bind_param($stmt, "sssssi", $species_name,
                                    $species_description, $species_homeworld, $species_trait_text,
                                    $species_image_file, $id_to_update);
                            
                            mysqli_stmt_execute($stmt);
                            
                            $nav_link = 'speciesdetails.php?id=' . $id_to_update;

                            redirect($nav_link);
                        }
                        else
                        {
                            // Display error message
                            echo "<h5><p class='text-danger'>" . $file_error_message
                                    . "</p></h5>";
                        }
                    } else { // Unintended page link - No species to edit, link back to index
                        redirect('index.php');
                    }
                ?>
                <div class="row">
                    <div class="col">
                        <form enctype="multipart/form-data" class="needs-validation" novalidate method="POST"
                            action="<?= $_SERVER['PHP_SELF'] ?>">
                            <div class="from-group row">
                                <label for="species_name" class="col-sm-3 col-form-label-lg">
                                    Species Name
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="species_name"
                                            name="species_name" placeholder="Species Name"
                                            value="<?= $species_name ?>" required>
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
                                            value="<?= $species_description ?>" required>
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
                                            value="<?= $species_homeworld ?>" required>
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
                                                value="<?= $trait ?>"
                                                <?= in_array(
                                                        $trait, $checked_species_traits
                                                        ) ? 'checked' : '' ?>>
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
                                Update Species
                            </button>
                            <input type="hidden" name="id_to_update"
                                    value="<?= $id_to_edit ?>">
                            <input type="hidden" name="species_image_file"
                                    value="<?= $species_image_file ?>">
                        </form>
                    </div>
                    <div class="col-3">
                        <img src="<?= htmlspecialchars($species_image_file_displayed) ?>"
                                class="img-thumbnail" style="max-height:400 px;"
                                alt="Species Image">
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
