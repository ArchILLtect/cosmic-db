<!--    Author: Nick Hanson
	      Version: 0.3
	      Date: 4/20/25
-->
<?php
/**
 * Class representing a species in the Cosmic Database
 * This class handles the properties and methods related to species,
 * including database interactions for inserting and querying species data.
 * It also provides methods for displaying species data in a formatted table.
 */
class Character
{
    private $id;
    private $name;
    private $age;
    private $role;
    private $personality;
    private $evo_powers;
    private $history;
    private $notes;
    private $traits;
    private $skills;
    private $image_file;
    private $species_id;

    private $dbc; // database connection

    /**
     * Constructor method for the Character class
     * @param mysqli $dbc - The database connection object
     */
    public function __construct($dbc)
    {
        $this->dbc = $dbc;
    }

    /**
     * Getter method for character id
     * @return int - The character ID
     */
    public function getId() { return $this->id; }
    /**
     * Setter method for character id
     * @param mixed $id - The character ID
     * @return void
     */
    public function setId($id) { $this->id = $id; }


    /**
     * Getter method for character name
     * @return string - The character name
     */
    public function getName() { return $this->name; }
    /**
     * Setter method for character name
     * @param mixed $name - The character name
     * @return void
     */
    public function setName($name) { $this->name = $name; }


    /**
     * Getter method for character age
     * @return int - The character age
     */
    public function getAge() { return $this->age; }
    /**
     * Setter method for character age
     * @param mixed $age - The character age
     * @return void
     */
    public function setAge($age) { $this->age = $age; }


    /**
     * Getter method for character role
     * @return string - The character role
     */
    public function getRole() { return $this->role; }
    /**
     * Setter method for character role
     * @param mixed $role - The character role
     * @return void
     */
    public function setRole($role) { $this->role = $role; }


    /**
     * Getter method for character personality
     * @return string - The character personality
     */
    public function getPersonality() { return $this->personality; }
    /**
     * Setter method for character personality
     * @param mixed $personality - The character personality
     * @return void
     */
    public function setPersonality($personality) { $this->personality = $personality; }


    /**
     * Getter method for character evo_powers
     * @return string - The character evo_powers
     */
    public function getEvoPowers() { return $this->evo_powers; }
    /**
     * Setter method for character evo_powers
     * @param mixed $evo_powers - The character evo_powers
     * @return void
     */
    public function setEvoPowers($evo_powers) { $this->evo_powers = $evo_powers; }


    /**
     * Getter method for character history
     * @return string - The character history
     */
    public function getHistory() { return $this->history; }
    /**
     * Setter method for character history
     * @param mixed $history - The character history
     * @return void
     */
    public function setHistory($history) { $this->history = $history; }


    /**
     * Getter method for character notes
     * @return string - The character notes
     */
    public function getNotes() { return $this->notes; }
    
    /**
     * Setter method for character notes
     * @param mixed $notes - The character notes
     * @return void
     */
    public function setNotes($notes) { $this->notes = $notes; }


    /**
     * Getter method for character traits
     * @return string - The character traits
     */
    public function getTraits() { return $this->traits; }
    /**
     * Setter method for character traits
     * @param mixed $traits - The character traits
     * @return void
     */
    public function setTraits($traits) { $this->traits = $traits; }


    /**
     * Getter method for character skills
     * @return string - The character skills
     */
    public function getSkills() { return $this->skills; }
    /**
     * Setter method for character skills
     * @param mixed $skills - The character skills
     * @return void
     */
    public function setSkills($skills) { $this->skills = $skills; }


    /**
     * Getter method for character image_file
     * @return string - The character image file name
     */
    public function getImageFile() { return $this->image_file; }
    /**
     * Setter method for character image_file
     * @param mixed $image_file - The character image file name
     * @return void
     */
    public function setImageFile($image_file) { $this->image_file = $image_file; }


    /**
     * Getter method for character species_id
     * @return int - The character species ID
     */
    public function getSpeciesId() { return $this->species_id; }
    /**
     * Setter method for character species_id
     * @param mixed $species_id - The character species ID
     * @return void
     */
    public function setSpeciesId($species_id) { $this->species_id = $species_id; }

    /**
     * Insert a new character into the database
     * @return bool - True on success, false on failure
     */
    public function insert()
    {
        $query = "INSERT INTO characters (name, age, role, personality, evo_powers, history, notes, traits, skills, image_file)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->dbc->prepare($query);
        $stmt->bind_param('sissssssss',
            $this->name, $this->age, $this->role, $this->personality,
            $this->evo_powers, $this->history, $this->notes,
            $this->traits, $this->skills, $this->image_file
        );
        $success = $stmt->execute();

        if ($success) {
            $this->id = $stmt->insert_id;

            $junction_query = "INSERT INTO character_species (character_id, species_id) VALUES (?, ?)";
            $junction_stmt = $this->dbc->prepare($junction_query);
            $junction_stmt->bind_param('ii', $this->id, $this->species_id);
            return $junction_stmt->execute();
        }
        return false;
    }

    /**
     * Query all characters with their species name
     * @return mysqli_result - The result set from the database query
     */
    public function queryAll()
    {
        $query = "SELECT characters.*, species.name AS species_name
                  FROM characters
                  LEFT JOIN character_species ON characters.character_id = character_species.character_id
                  LEFT JOIN species ON character_species.species_id = species.species_id";
        return $this->dbc->query($query);
    }

    /**
     * Display characters as a table
     * @param mixed $result - The result set from the queryAll method
     * @return string - HTML string representing the table rows
     */
    public function displayAsTable($result)
    {
        $output = '';
        while ($row = $result->fetch_assoc())
        {
            //$imagePath = CDB_UPLOAD_WEB_PATH . htmlspecialchars($row['image_file']);
            $character_image_file = htmlspecialchars($row['image_file']);

            if (empty($character_image_file))
            {
                /* TODO: Return to this code when the fileutil is fixed
                $species_image_file = CDB_UPLOAD_WEB_PATH
                        . CDB_DEFAULT_SPECIES_FILENAME;*/
                
                $character_image_file = CDB_DEFAULT_CHARACTER_FILENAME;
            }
            $output .= "<tr>";
            $output .= "<td><a href='/cosmic-db/characters/characterdetails.php?id={$row['character_id']}'>";
            $output .= "<img src='images/$character_image_file' class='img-thumbnail' style='max-height: 100px;'></a></td>";
            $output .= "<td><a href='/cosmic-db/characters/characterdetails.php?id={$row['character_id']}'>" . htmlspecialchars($row['name']) . "</a></td>";
            $output .= "<td>" . htmlspecialchars($row['species_name']) . "</td>";
            if (isset($_SESSION['user_access_privileges']) && $_SESSION['user_access_privileges'] === 'admin') {
                $output .= "<td class='text-right align-middle'>
                    <a href='/cosmic-db/characters/removecharacter.php?id_to_delete={$row['character_id']}'>
                    <i class='fas fa-trash'></i></a></td>"
                ;
            } else {
                $output .= "<td></td>";
            }
            $output .= "</tr>";

        }
        return $output;
    }
}
?>
