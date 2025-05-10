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
class Species
{
    private $id;
    private $name;
    private $description;
    private $homeworld;
    private $traits;
    private $image_file;

    private $dbc;

    /**
     * Constructor method for the Species class
     * @param mysqli $dbc - The database connection object
     */
    public function __construct($dbc)
    {
        $this->dbc = $dbc;
    }

    /**
     * Getter method for species id
     * @return int - The species ID
     */
    public function getId() { return $this->id; }
    /**
     * Setter method for species id
     * @param mixed $id - The species ID
     * @return void
     */
    public function setId($id) { $this->id = $id; }

    /**
     * Getter method for species name
     * @return string - The species name
     */
    public function getName() { return $this->name; }
    /**
     * Setter method for species name
     * @param mixed $name - The species name
     * @return void
     */
    public function setName($name) { $this->name = $name; }

    /**
     * Getter method for species description
     * @return string - The species description
     */
    public function getDescription() { return $this->description; }
    /**
     * Setter method for species description
     * @param mixed $description - The species description
     * @return void
     */
    public function setDescription($description) { $this->description = $description; }

    /**
     * Getter method for species homeworld
     * @return string - The species homeworld
     */
    public function getHomeworld() { return $this->homeworld; }
    /**
     * Setter method for species homeworld
     * @param mixed $homeworld - The species homeworld
     * @return void
     */
    public function setHomeworld($homeworld) { $this->homeworld = $homeworld; }

    /**
     * Getter method for species traits
     * @return string - The species traits
     */
    public function getTraits() { return $this->traits; }
    /**
     * Setter method for species traits
     * @param mixed $traits - The species traits
     * @return void
     */
    public function setTraits($traits) { $this->traits = $traits; }

    /**
     * Getter method for species image file
     * @return string - The species image file
     */
    public function getImageFile() { return $this->image_file; }
    /**
     * Setter method for species image file
     * @param mixed $image_file - The species image file
     * @return void
     */
    public function setImageFile($image_file) { $this->image_file = $image_file; }

    /**
     * Insert method to add a new species to the database
     * @return bool
     */
    public function insert()
    {
        $query = "INSERT INTO species (name, description, homeworld, traits, image_file) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->dbc->prepare($query);
        $stmt->bind_param('ssssi', $this->name, $this->description, $this->homeworld, $this->traits, $this->image_file);
        return $stmt->execute();
    }

    /**
     * Query method to retrieve all species from the database
     * @return bool mysqli_result - The result set from the database query
     */
    public function queryAll()
    {
        $query = "SELECT * FROM species";
        return $this->dbc->query($query);
    }


    /**
     * Display method to format the species data as a table
     * @param mysqli_result $result - The result set from the database query
     * @return string - The formatted HTML table rows
    */
    public function displayAsTable($result)
    {
        $output = '';
        while ($row = $result->fetch_assoc())
        {
            //$imagePath = CDB_UPLOAD_WEB_PATH . htmlspecialchars($row['image_file']);
            $species_image_file = htmlspecialchars($row['image_file']);
                                    
            if (empty($species_image_file))
            {
                /* TODO: Return to this code when the fileutil is fixed
                $species_image_file = CDB_UPLOAD_WEB_PATH
                        . CDB_DEFAULT_SPECIES_FILENAME;*/
                
                $species_image_file = CDB_DEFAULT_SPECIES_FILENAME;
            }
            $output .= "<tr>";
            $output .= "<td><a href='/cosmic-db/species/speciesdetails.php?id={$row['species_id']}'>";
            $output .= "<img src='images/$species_image_file' class='img-thumbnail' style='max-height: 100px;'></a></td>";
            $output .= "<td><a href='/cosmic-db/species/speciesdetails.php?id={$row['species_id']}'>" . htmlspecialchars($row['name']) . "</a></td>";
            $output .= "<td class='text-right align-middle'><a href='/cosmic-db/species/removespecies.php?id={$row['species_id']}'><i class='fas fa-trash'></i></a></td>";
            $output .= "</tr>";
        }
        return $output;
    }
}
?>
