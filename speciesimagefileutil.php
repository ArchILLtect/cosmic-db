<!--    Author: Nick Hanson
	      Version: 0.3
	      Date: 4/20/25
-->
<?php
    require_once 'specieslistingfileconstants.php';

    /** 
     * Purpose:         Validates an uploaded species image file
     *
     * Description:     Validates an uploaded species image file that is not greater than ML_MAX_FILE_SIZE
     *                  (1/2 MB) an is either a jpg or png image type, and has no errors. If the image file
     *                  validates to these constraints, an error message containing an empty string is
     *                  returned. If there is an error, a string containing the constraints the file failed
     *                  to validate is/are returned.
     *
     * @return String   Empty if validation is successful, otherwise an error string containing the
     *                  constraints the file failed to validate to.
     *
     */
    function validateSpeciesImageFile()
    {
        $error_message = "";
        
        // Check for $_FILES being set and no errors
        if (isset($_FILES) && $_FILES['species_image_file']['error'] == UPLOAD_ERR_OK)
        {
            // Check for uploaded file < Max file size AND an acceptable image type
            if ($_FILES['species_image_file']['size'] > CDB_MAX_FILE_SIZE)
            {
                $error_message = "The species file image MUST be less than " . CDB_MAX_FILE_SIZE . " Bytes";
            }
            
            $image_type = $_FILES['species_image_file']['type'];
            
            $allowed_image_types = [
                'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/gif'
            ];
            
            if (in_array($image_type, $allowed_image_types))
            {
                if (empty($error_message))
                {
                    $error_messages = "The species file image must be of type jpg, png or gif.";
                }
                else
                {
                    $error_messages .= ", and be an image of type jpg, png or gif.";                      
                }
            }
        }
        elseif (isset($_FILES) && $_FILES['species_image_file']['error'] != UPLOAD_ERR_NO_FILE
                && $_FILES['species_image_file']['error'] != UPLOAD_ERR_OK)
        {
            $error_message = "Error uploading species image file.";
        }
        return $error_message;
    }
    
    /** 
     * Purpose:         Moves an uploaded species image file to the ML_UPLOAD_PATH (images/)
     *                  folder and return the path location.
     *
     * Description:     Moves an uploaded species image file from the temporary server location
     *                  to the ML_UPLOAD_PATH (images/) folder IF a species image file was uploaded
     *                  and returns the path location of the uploaded file by appending the file
     *                  name to the ML_UPLOADED_PATH (e.g. images/species_image.png). IF a species image
     *                  file was NOT uploaded, an empty string will be returned for the path.
     *
     * @return string   Path to the species image file IF a file was uploaded AND moved to the
     *                  ML_UPLOAD_PATH (images/) folder, otherwise an empty string.
     */
     function addSpeciesImageFileReturnPathLocation()
     {
        $species_file_path = "";
        
        // Check for $_FILES being set and no errors.
        if (isset($_FILES) && $_FILES['species_image_file']['error'] == UPLOAD_ERR_OK)
        {
            $species_file_path = CDB_UPLOAD_PATH . $_FILES['species_image_file']['name'];
        
            if (!move_uploaded_file($_FILES['species_image_file']['tmp_name'], $species_file_path))
            {
                $species_file_path = "";
            }
        }        
        
        return $species_file_path;
    }
     
    /** 
     * @param $species_file_path
     */
    function removeSpeciesImageFile($species_file_path)
    {
        @unlink($species_file_path);
    }
