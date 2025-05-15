<?php
/*  Author: Nick Hanson
	Version: 1.0
	Date: 4/20/25
*/
    $required_access_level = 'admin';
    require_once('../authorizeaccess.php');
    require_once('../pagetitles.php');
    $page_title = CDB_EDIT_CHARACTER_PAGE;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Edit a Character</title>
        <link rel="stylesheet"
                href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
                integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS"
                crossorigin="anonymous">
    </head>
    <body>
        <?php
            require_once('../navmenu.php');
            require_once('../fileconstants.php');
            require_once('characterfileconstants.php');

        ?>
        <div class="card">
            <div class="card-body" style="margin: 0 10% 0 10%;">
                <h1>Edit a Character</h1>
                <hr/>
                <?php
                    require_once('../dbconnection.php');
                    require_once('characterimagefileutil.php');

                    $traits = CDB_CHARACTER_TRAITS;
                    $skills = CDB_CHARACTER_SKILLS;
                    
                    if (isset($_GET['id_to_edit'])) {
                        $id_to_edit = $_GET['id_to_edit'];
                        $query = "SELECT * FROM characters WHERE character_id = $id_to_edit";

                        $result = mysqli_query($dbc, $query)
                                or trigger_error('Error querying database character',
                                E_USER_ERROR);
                        
                         if (mysqli_num_rows($result) == 1) {
                            $row = mysqli_fetch_assoc($result);
                            
                            $character_name = $row['name'];
                            $character_age = $row['age'];
                            $character_role = $row['role'];
                            $character_personality = $row['personality'];
                            $character_evo_powers = $row['evo_powers'];
                            $character_history = $row['history'];
                            $character_notes = $row['notes'];
                            $character_trait_text = $row['traits'];
                            $character_skill_text = $row['skills'];

                            $character_image_file = $row['image_file'];
                            
                            if (empty($character_image_file)) {

                                $character_image_file = CDB_UPLOAD_WEB_PATH
                                        . CDB_DEFAULT_CHARACTER_FILENAME
                                ;
    
                            } else {
                                $character_image_file_displayed = CDB_UPLOAD_WEB_PATH . $row['image_file'];
                            }
                            
                            $checked_character_traits = explode(",", $character_trait_text);
                            $checked_character_skills = explode(",", $character_skill_text);
                        }
                    }
                    elseif (isset(
                            $_POST['add_character_submission'],
                            $_POST['character_name'],
                            $_POST['character_age'],
                            $_POST['character_role'],
                            $_POST['character_personality'],
                            $_POST['character_evo_powers'],
                            $_POST['character_history'],
                            $_POST['character_notes'],
                            $_POST['id_to_update']
                    )) {

                        $character_name = filter_var($_POST['character_name'],
                                FILTER_SANITIZE_SPECIAL_CHARS);
                        $character_age = filter_var($_POST['character_age'],
                                FILTER_SANITIZE_NUMBER_INT);
                        $character_role = filter_var($_POST['character_role'],
                                FILTER_SANITIZE_SPECIAL_CHARS);
                        $character_personality = filter_var($_POST['character_personality'],
                                FILTER_SANITIZE_SPECIAL_CHARS);
                        $character_evo_powers = filter_var($_POST['character_evo_powers'],
                                FILTER_SANITIZE_SPECIAL_CHARS);
                        $character_history = filter_var($_POST['character_history'],
                                FILTER_SANITIZE_SPECIAL_CHARS);
                        $character_notes = filter_var($_POST['character_notes'],
                                FILTER_SANITIZE_SPECIAL_CHARS);
                        $checked_character_traits = $_POST['character_trait_checkbox'];
                        $checked_character_skills = $_POST['character_skill_checkbox'];
                        $id_to_update = filter_var($_POST['id_to_update'],
                                FILTER_SANITIZE_NUMBER_INT);
                        $character_image_file = filter_var($_POST['character_image_file'],
                                FILTER_SANITIZE_SPECIAL_CHARS);
                        
                        $character_trait_text = "";
                        $character_skill_text = "";
                    
                        if (isset($checked_character_traits)) {
                                $character_trait_text = implode(",", $checked_character_traits);
                        }

                        if (isset($checked_character_skills)) {
                            $character_skill_text = implode(",", $checked_character_skills);
                    }

                        if (empty($character_image_file))
                        {
                            $character_image_file_displayed = CDB_UPLOAD_WEB_PATH
                                    . CDB_DEFAULT_CHARACTER_FILENAME;
                        }
                        else
                        {
                            $character_image_file_displayed = $character_image_file;
                        }

                        /*
                        Here is where we will deal with the file by calling validateCharacterImageFile().
                        The function will validate that the character image file is not greater than 128
                        characters, is the right image type (jpg/png/gif), and not greater than 512kb.
                        This function will return an empty string ('') if the file validates successfully,
                        otherwise, the string will contain error text to be output to the web page before
                        re-displaying the form.
                        */

                        $file_error_message = validateCharacterImageFile();

                        if (empty($file_error_message))
                        {
                            $character_image_file_path = addCharacterImageFileReturnPathLocation();

                            // IF new image selected, set it to be updated in the db.
                            if(!empty($character_image_file_path))
                            {
                                // IF replacing an image (other then the default), remove it
                                if(!empty($character_image_file))
                                {
                                    removeCharacterImageFile($character_image_file);
                                }

                                $character_image_file = $character_image_file_path;
                            }

                            $sql = "UPDATE characters SET name = ?, "
                                    . "age = ?, role = ?, personality = ?, evo_powers = ?, history = ?, "
                                    . "notes = ?, traits = ?, skills = ?, image_file = ? WHERE character_id = ?";
                            
                            $stmt = mysqli_prepare($dbc, $sql);

                            mysqli_stmt_bind_param($stmt, "sissssssssi", $character_name,
                                    $character_age, $character_role, $character_personality,
                                    $character_evo_powers, $character_history, $character_notes, $character_trait_text,
                                    $character_skill_text, $character_image_file, $id_to_update);
                            
                            mysqli_stmt_execute($stmt);
                            
                            $nav_link = 'characterdetails.php?id=' . $id_to_update;

                            redirect($nav_link);
                        }
                        else
                        {
                            // Display error message
                            echo "<h5><p class='text-danger'>" . $file_error_message
                                    . "</p></h5>";
                        }
                    } else { // Unintended page link - No characters to edit, link back to index
                        redirect('/index.php');
                    }
                ?>
                <div class="row">
                    <div class="col">
                        <form enctype="multipart/form-data" class="needs-validation" novalidate method="POST"
                            action="<?= $_SERVER['PHP_SELF'] ?>">
                            <div class="from-group row">
                                <label for="character_name" class="col-sm-3 col-form-label-lg">
                                    Name
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="character_name"
                                            name="character_name" placeholder="Character Name"
                                            value="<?= $character_name ?>" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid character name.
                                    </div>
                                </div>
                            </div>
                            <div class="from-group row">
                                <label for="character_age" class="col-sm-3 col-form-label-lg">
                                    Age
                                </label>
                                <div class="col-sm-8">
                                    <input type="int" class="form-control" id="character_age"
                                            name="character_age" placeholder="Character Age"
                                            value="<?= $character_age ?>" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid character age.
                                    </div>
                                </div>
                            </div>
                            <div class="from-group row">
                                <label for="character_role" class="col-sm-3 col-form-label-lg">
                                    Role
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="character_role"
                                            name="character_role" placeholder="Character Role"
                                            value="<?= $character_role ?>" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid character role.
                                    </div>
                                </div>
                            </div>
                            <div class="from-group row">
                                <label for="character_personality" class="col-sm-3 col-form-label-lg">
                                    Personality
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="character_personality"
                                            name="character_personality" placeholder="Character Personality"
                                            value="<?= $character_personality ?>" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid character personality.
                                    </div>
                                </div>
                            </div>
                            <div class="from-group row">
                                <label for="character_evo_powers" class="col-sm-3 col-form-label-lg">
                                    Evo Powers
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="character_evo_powers"
                                            name="character_evo_powers" placeholder="Character Evo Powers"
                                            value="<?= $character_evo_powers ?>" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid character evo powers.
                                    </div>
                                </div>
                            </div>
                            <div class="from-group row">
                                <label for="character_history" class="col-sm-3 col-form-label-lg">
                                    History
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="character_history"
                                            name="character_history" placeholder="Character History"
                                            value="<?= $character_history ?>" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid character history.
                                    </div>
                                </div>
                            </div>
                            <div class="from-group row">
                                <label for="character_notes" class="col-sm-3 col-form-label-lg">
                                    Notes
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" id="character_notes"
                                            name="character_notes" placeholder="Character Notes"
                                            value="<?= $character_notes ?>" required>
                                    <div class="invalid-feedback">
                                        Please provide a valid character notes.
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
                                                id="character_trait_checkbox_action_<?= $trait ?>"
                                                name="character_trait_checkbox[]"
                                                value="<?= $trait ?>"
                                                <?= in_array(
                                                        $trait, $checked_character_traits
                                                        ) ? 'checked' : '' ?>>
                                            <label class="form-check-label"
                                                    for="character_trait_checkbox_action_<?= $trait ?>">
                                                <?= $trait ?></label>
                                        </div>
                                        <div class="invalid-feedback">
                                            Please provide a valid character trait.
                                        </div>
                                <?php
                                    }
                                ?>
                                </div>
                            </div>
                            <hr>
                            <div class="from-group row">
                                <label class="col-sm-3 col-form-label-lg">Skills</label>
                                <div class="col-sm-8">
                                <?php
                                    foreach ($skills as $skill) {
                                ?>
                                        <div class="form-check form-check-inline col-sm-3">
                                            <input class="form-check-input" type="checkbox"
                                                id="character_skill_checkbox_action_<?= $skill ?>"
                                                name="character_skill_checkbox[]"
                                                value="<?= $skill ?>"
                                                <?= in_array(
                                                        $skill, $checked_character_skills
                                                        ) ? 'checked' : '' ?>>
                                            <label class="form-check-label"
                                                    for="character_skill_checkbox_action_<?= $skill ?>">
                                                <?= $skill ?></label>
                                        </div>
                                        <div class="invalid-feedback">
                                            Please provide a valid character skill.
                                        </div>
                                <?php
                                    }
                                ?>
                                </div>
                            </div>
                            <hr>
                            <div class="from-group row">
                                <label for="character_image_file"
                                        class="col-sm-3 col-form-label-lg">Character Image File</label>
                                <div class="col-sm-8">
                                    <input type="file" class="form-control-file"
                                            id="character_image_file" name="character_image_file">
                                </div>
                            </div>
                            <div class="from-group d-flex justify-content-center mb-5">
                                <button class="btn btn-primary" type="submit"
                                        name="add_character_submission">
                                    Update Character
                                </button>
                            <button type="button" class="btn btn-danger btn-secondary ml-3"
                                    onclick="window.location.href='characterdetails.php?id=<?= $id_to_edit ?>'">Cancel</button>
                            </div>
                            <input type="hidden" name="id_to_update"
                                    value="<?= $id_to_edit ?>">
                            <input type="hidden" name="character_image_file"
                                    value="<?= $character_image_file ?>">
                        </form>
                    </div>
                    <hr>
                    <div class="col-3">
                        <img src="<?= htmlspecialchars($character_image_file_displayed) ?>"
                                class="img-thumbnail" style="max-height:400 px;"
                                alt="Character Image">
                    </div>
                </div>
                <?php require_once('../footer.php'); ?>
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
