<?php
// Character.php
class Character
{
    // ✅ 1️⃣ PROPERTIES
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
    private $species_id;  // selected species from form

    private $dbc; // database connection

    // ✅ 2️⃣ CONSTRUCTOR
    public function __construct($dbc)
    {
        $this->dbc = $dbc;
    }

    // ✅ 3️⃣ GETTERS/SETTERS
    public function getId() { return $this->id; }
    public function setId($id) { $this->id = $id; }

    public function getName() { return $this->name; }
    public function setName($name) { $this->name = $name; }

    public function getAge() { return $this->age; }
    public function setAge($age) { $this->age = $age; }

    public function getRole() { return $this->role; }
    public function setRole($role) { $this->role = $role; }

    public function getPersonality() { return $this->personality; }
    public function setPersonality($personality) { $this->personality = $personality; }

    public function getEvoPowers() { return $this->evo_powers; }
    public function setEvoPowers($evo_powers) { $this->evo_powers = $evo_powers; }

    public function getHistory() { return $this->history; }
    public function setHistory($history) { $this->history = $history; }

    public function getNotes() { return $this->notes; }
    public function setNotes($notes) { $this->notes = $notes; }

    public function getTraits() { return $this->traits; }
    public function setTraits($traits) { $this->traits = $traits; }

    public function getSkills() { return $this->skills; }
    public function setSkills($skills) { $this->skills = $skills; }

    public function getImageFile() { return $this->image_file; }
    public function setImageFile($image_file) { $this->image_file = $image_file; }

    public function getSpeciesId() { return $this->species_id; }
    public function setSpeciesId($species_id) { $this->species_id = $species_id; }

    // ✅ 4️⃣ INSERT METHOD
    public function insert()
    {
        // insert character
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
            $this->id = $stmt->insert_id;  // get newly inserted character ID

            // insert into junction table
            $junction_query = "INSERT INTO character_species (character_id, species_id) VALUES (?, ?)";
            $junction_stmt = $this->dbc->prepare($junction_query);
            $junction_stmt->bind_param('ii', $this->id, $this->species_id);
            return $junction_stmt->execute();
        }
        return false;
    }

    // ✅ 5️⃣ QUERY METHOD (join with species)
    public function queryAll()
    {
        $query = "SELECT characters.*, species.name AS species_name
                  FROM characters
                  LEFT JOIN character_species ON characters.character_id = character_species.character_id
                  LEFT JOIN species ON character_species.species_id = species.species_id";
        return $this->dbc->query($query);
    }

    // ✅ 6️⃣ DISPLAY METHOD (result set → HTML)
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
            $output .= "<td class='text-right align-middle'><a href='/cosmic-db/removecharacter.php?id={$row['character_id']}'><i class='fas fa-trash'></i></a></td>";
            $output .= "</tr>";
        }
        return $output;
    }
}
?>
