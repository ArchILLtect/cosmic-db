<!--    Author: Nick Hanson
	      Version: 0.3
	      Date: 4/20/25
-->
<?php
    require_once 'characterfileconstants.php';

    /** 
     * Purpose:         Validates an uploaded character image file
     *
     * Description:     Validates an uploaded character image file that is not greater than ML_MAX_FILE_SIZE
     *                  (1/2 MB) an is either a jpg or png image type, and has no errors. If the image file
     *                  validates to these constraints, an error message containing an empty string is
     *                  returned. If there is an error, a string containing the constraints the file failed
     *                  to validate is/are returned.
     *
     * @return string   Empty if validation is successful, otherwise an error string containing the
     *                  constraints the file failed to validate to.
     *
     */
    function validateCharacterImageFile()
    {
        $error_message = "";
        
        // Check for $_FILES being set and no errors
        if (isset($_FILES) && $_FILES['character_image_file']['error'] == UPLOAD_ERR_OK)
        {
            // Check for uploaded file < Max file size AND an acceptable image type
            if ($_FILES['character_image_file']['size'] > CDB_MAX_FILE_SIZE)
            {
                $error_message = "The character file image MUST be less than " . CDB_MAX_FILE_SIZE . " Bytes";
            }
            
            $image_type = $_FILES['character_image_file']['type'];
            
            $allowed_image_types = [
                'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/gif'
            ];
            
            if (in_array($image_type, $allowed_image_types))
            {
                if (empty($error_message))
                {
                    $error_message = "The character file image must be of type jpg, png or gif.";
                }
                else
                {
                    $error_message .= ", and be an image of type jpg, png or gif.";                      
                }
            }
        }
        elseif (isset($_FILES) && $_FILES['character_image_file']['error'] != UPLOAD_ERR_NO_FILE
                && $_FILES['character_image_file']['error'] != UPLOAD_ERR_OK)
        {
            $error_message = "Error uploading character image file.";
        }
        return $error_message;
    }
    
    /** 
     * Purpose:         Moves an uploaded character image file to the ML_UPLOAD_PATH (images/)
     *                  folder and return the path location.
     *
     * Description:     Moves an uploaded character image file from the temporary server location
     *                  to the ML_UPLOAD_PATH (images/) folder IF a character image file was uploaded
     *                  and returns the path location of the uploaded file by appending the file
     *                  name to the ML_UPLOADED_PATH (e.g. images/character_image.png). IF a character image
     *                  file was NOT uploaded, an empty string will be returned for the path.
     *
     * @return string   Path to the character image file IF a file was uploaded AND moved to the
     *                  ML_UPLOAD_PATH (images/) folder, otherwise an empty string.
     */
     function addCharacterImageFileReturnPathLocation()
     {
        $character_file_path = "";
        
        // Check for $_FILES being set and no errors.
        if (isset($_FILES) && $_FILES['character_image_file']['error'] == UPLOAD_ERR_OK)
        {
            $character_file_path = CDB_UPLOAD_PATH . $_FILES['character_image_file']['name'];
        
            if (!move_uploaded_file($_FILES['character_image_file']['tmp_name'], $character_file_path))
            {
                $character_file_path = "";
            }
        }        
        
        return $character_file_path;
    }
     
    /** 
     * @param $character_file_path
     */
    function removeCharacterImageFile($character_file_path)
    {
        @unlink($character_file_path);
    }
