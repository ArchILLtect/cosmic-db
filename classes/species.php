<!--    Author: Nick Hanson
	      Version: 0.3
	      Date: 4/20/25
-->
<?php
// Species.php
//require_once($_SERVER['DOCUMENT_ROOT'] . '/../fileconstants/fileconstants.php'); // adjust path if needed
//require_once($_SERVER['DOCUMENT_ROOT'] . '../dbconnection.php'); // database connection

class Species
{
    // ✅ 1️⃣ PROPERTIES
    private $id;
    private $name;
    private $description;
    private $homeworld;
    private $traits;
    private $image_file;

    private $dbc; // database connection

    // ✅ 2️⃣ CONSTRUCTOR (optional but useful)
    public function __construct($dbc)
    {
        $this->dbc = $dbc;
    }

    // ✅ 3️⃣ GETTERS AND SETTERS
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }

    public function getDescription() { return $this->description; }
    public function setDescription($description) { $this->description = $description; }

    public function getHomeworld() { return $this->homeworld; }
    public function setHomeworld($homeworld) { $this->homeworld = $homeworld; }

    public function getTraits() { return $this->traits; }
    public function setTraits($traits) { $this->traits = $traits; }

    public function getImageFile() { return $this->image_file; }
    public function setImageFile($image_file) { $this->image_file = $image_file; }

    // ✅ 4️⃣ INSERT METHOD
    public function insert()
    {
        $query = "INSERT INTO species (name, description, homeworld, traits, image_file) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->dbc->prepare($query);
        $stmt->bind_param('ssssi', $this->name, $this->description, $this->homeworld, $this->traits, $this->image_file);
        return $stmt->execute();
    }

    // ✅ 5️⃣ QUERY METHOD (return result set)
    public function queryAll()
    {
        $query = "SELECT * FROM species";
        return $this->dbc->query($query);
    }

    // ✅ 6️⃣ DISPLAY METHOD (format result set into HTML table rows)
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
