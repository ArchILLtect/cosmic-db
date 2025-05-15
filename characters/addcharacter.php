<?php
/*  Author: Nick Hanson
	Version: 1.0
	Date: 4/20/25
*/
    require_once('../authorizeaccess.php');
    require_once('../pagetitles.php');
    $page_title = CDB_ADD_CHARACTER_PAGE;
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Add a Character</title>
        <link rel="stylesheet"
                href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"
                integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS"
                crossorigin="anonymous">
    </head>
    <body>
        <?php
            require_once('../navmenu.php');
            require_once('../dbconnection.php');
            require_once('../fileconstants.php');
            require_once('../classes/character.php');
            require_once('../classes/species.php');
            require_once('characterfileconstants.php');
        ?>
        <div class="card" style="margin-bottom: 10%;">
            <div class="card-body" style="margin: 0 10% 0 10%;">
                <h1>Add a Character</h1>
                <hr/>
                <?php
                    // Initialization
                    $display_add_character_form = true;
                    
                    $character_name = "";
                    $character_age = "";
                    $species_id = "";
                    $character_role = "";
                    $character_personality = "";
                    $character_evo_powers = "";
                    $character_history = "";
                    $character_notes = "";
                    $character_traits_text = "";
                    $checked_character_traits = null;
                    $character_skills_text = "";
                    $checked_character_skills = null;

                    $traits = CDB_CHARACTER_TRAITS;
                    $skills = CDB_CHARACTER_SKILLS;

                    $speciesObj = new Species($dbc);
                    /** @var mysqli_result $species */
                    $species = $speciesObj->queryAll();

                    if (!$species) {
                        trigger_error("Query failed: " . mysqli_error($dbc), E_USER_ERROR);
                    }

                    if (isset(
                            $_POST['add_character_submission'],
                            $_POST['character_name'],
                            $_POST['species_id'],
                            $_POST['character_age'],
                            $_POST['character_role'],
                            $_POST['character_personality'],
                            $_POST['character_evo_powers'],
                            $_POST['character_history'],
                            $_POST['character_notes'],

                    )) {
                        require_once('characterimagefileutil.php');

                        $character_name = filter_var($_POST['character_name'],
                                FILTER_SANITIZE_SPECIAL_CHARS);
                        $species_id = filter_var($_POST['species_id'],
                                FILTER_SANITIZE_NUMBER_INT);
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
                        
                        $character_traits_text = "";
                        $character_skills_text = "";
                    
                    if (isset($checked_character_traits)) {
                        $character_traits_text = implode(",", $checked_character_traits);
                    }
                    if (isset($checked_character_skills)) {
                        $character_skills_text = implode(",", $checked_character_skills);
                    }
                        
                    /*
                    Here is where we will deal with the file by calling validateCharacterImageFile().
                    The function will validate that the character image file is the right image type
                    (jpg/png/gif), and not greater than 512kb. This function will return an empty
                    string ('') if the file validates successfully, otherwise, the string will
                    contain error text to be output to the web page before re-displaying the form.
                    */
                    $file_error_message = validateCharacterImageFile();
                    
                    if(empty($file_error_message))
                    {

                        $character = new Character($dbc);
                    
                        $character_image_file_path = addCharacterImageFileReturnPathLocation();
                        $character_image_file = $_FILES['character_image_file']['name'];
                        
                        $character->setName($_POST['character_name']);
                        $character->setAge($_POST['character_age']);
                        $character->setSpeciesId($_POST['species_id']);
                        $character->setRole($_POST['character_role']);
                        $character->setPersonality($_POST['character_personality']);
                        $character->setEvoPowers($_POST['character_evo_powers']);
                        $character->setHistory($_POST['character_history']);
                        $character->setNotes($_POST['character_notes']);
                        $character->setTraits($character_traits_text);
                        $character->setSkills($character_skills_text);
                        $character->setImageFile($_FILES['character_image_file']['name']);
                        $character->setSpeciesId($_POST['species_id']);
                    
                        if ($character->insert()) {
                            echo "<p>Character added successfully!</p>";
                        } else {
                            echo "<p>Failed to add character.</p>";
                        }

                        if(empty($character_image_file_path))
                        {
                            $character_image_file_path = CDB_UPLOAD_WEB_PATH
                                    . CDB_DEFAULT_CHARACTER_FILENAME;
                        } else {
                            $character_image_file_path = CDB_UPLOAD_WEB_PATH . $character_image_file;
                        }

                        $display_add_character_form = false;
                ?>
                <h3 class="text-info">The following character record was added:</h3>

                <h1><?= $character_name ?></h1>
                <div class="row">
                    <div class="col-2">
                        <img src="<?= $character_image_file_path ?>" class="img-thumbnail"
                                style="max-height: 200px;" alt="Character Image">
                    </div>
                    <div class="col">
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th scope="row">Name</th>
                                    <td><?= $character_name ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Species</th>
                                    <td><?= $species_id ?></td>
                                </tr>

                                <tr>
                                    <th scope="row">Age</th>
                                    <td><?= $character_age ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Role</th>
                                    <td><?= $character_role ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Personality</th>
                                    <td><?= $character_personality ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Evo Powers</th>
                                    <td><?= $character_evo_powers ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">History</th>
                                    <td><?= $character_history ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Notes</th>
                                    <td><?= $character_notes ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Traits</th>
                                    <td><?= $character_traits_text ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Skills</th>
                                    <td><?= $character_skills_text ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr/>
                <p>Would you like to <a href='<?= $_SERVER['PHP_SELF'] ?>'>
                        add another character</a>?</p>
                <?php
                        }
                        else
                        {
                            // Echo error message
                            echo "<h5><p class='text-danger'>" . $file_error_message . "</p></h5>";
                        }
                    }
                    if ($display_add_character_form)
                    {
                ?>
                <form enctype="multipart/form-data" class="needs-validation" novalidate method="POST"
                        action="<?= $_SERVER['PHP_SELF'] ?>">
                    <div class="from-group row">
                        <label for="character_name" class="col-sm-3 col-form-label-lg">
                            Name
                        </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="character_name"
                                    name="character_name" placeholder="Character Name" required>
                            <div class="invalid-feedback">
                                Please provide a valid character name.
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="species_id" class="col-sm-3 col-form-label-lg">Species</label>
                        <div class="col-sm-8">
                            <select id="species_id" name="species_id" class="form-control" required>
                                <option value="">-- Select a Species --</option>
                                <?php while ($row = mysqli_fetch_assoc($species)): ?>
                                    <option value="<?= $row['species_id'] ?>">
                                        <?= htmlspecialchars($row['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <div class="invalid-feedback">
                                Please select a valid species.
                            </div>
                        </div>
                    </div>
                    <div class="from-group row">
                        <label for="character_age" class="col-sm-3 col-form-label-lg">
                            Age
                        </label>
                        <div class="col-sm-8">
                            <input type="number" class="form-control" id="character_age"
                                    name="character_age" placeholder="Character Age"
                                    required> <!-- //TODO: Add validation for age -->
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
                                    required>
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
                                    required>
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
                                    required>
                            <div class="invalid-feedback">
                                Please provide valid character evo powers.
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
                                    required>
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
                                    required>
                            <div class="invalid-feedback">
                                Please provide valid character notes.
                            </div>
                        </div>
                    </div>
                    <hr>
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
                                        value="<?= $trait ?>">
                                    <label class="form-check-label"
                                            for="character_trait_checkbox_action_<?= $trait ?>">
                                        <?= $trait ?></label>
                                </div>
                            <div class="invalid-feedback">
                                Please provide valid character traits.
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
                                        value="<?= $skill ?>">
                                    <label class="form-check-label"
                                            for="character_skill_checkbox_action_<?= $skill ?>">
                                        <?= $skill ?></label>
                                </div>
                            <div class="invalid-feedback">
                                Please provide valid character skills.
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
                    <button class="btn btn-primary" type="submit"
                            name="add_character_submission">
                        Add Character
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
                    } // Display add character form
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