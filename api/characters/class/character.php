<?php
class Character 
{
    // Database connection
    private $conn;
    
    private $table_name = "player_character";
 
    public $id;
    public $player_id;
    public $name;
    public $char_race;
    public $char_class;
    public $level;
 
    public function __construct($db)
    {
        $this->conn = $db;
    }
 
    function Create()
    {
        $statement = $this->conn->prepare(
            "INSERT INTO " . $this->table_name . "
             SET
             player_id = :player_id,
             name = :name,
             char_race = :char_race,
             char_class = :char_class"
        );

        // Remove any HTML
        $this->name = htmlspecialchars(strip_tags($this->name));

        $statement->bindParam(':player_id', $this->player_id, PDO::PARAM_INT);
        $statement->bindParam(':name', $this->name, PDO::PARAM_STR);
        $statement->bindParam(':char_race', $this->char_race, PDO::PARAM_INT);
        $statement->bindParam(':char_class', $this->char_class, PDO::PARAM_INT);

        // Check if the name is already in use
        $this->UniqueExists("name");
        
        if($statement->execute()){
            return true;
        }
        return false;
    }
    
    // Check if given unique property already exists or not
    function UniqueExists($uniqueProperty) 
    {
        $statement = $this->conn->prepare(
           "SELECT id
            FROM " . $this->table_name . "
            WHERE $uniqueProperty = ?"
        );
        
        $this->$uniqueProperty = htmlspecialchars(strip_tags($this->$uniqueProperty));
        
        $statement->bindParam(1, $this->$uniqueProperty, PDO::PARAM_STR);
        $statement->execute();
        
        $num = $statement->rowCount();
        if ($num > 0){
            $error = array(
                "status" => 409, 
                "body" => ucfirst($uniqueProperty) . " is already in use."
            );
            http_response_code(409);
            $json = json_encode($error);
            echo $json;
            die();
        }
    }
    
    
    // Get all characters that are guildless (for inviting new members)
    function GetAllGuildless() 
    {
        $statement = $this->conn->prepare(
            "SELECT id, name
            FROM " . $this->table_name . "
            WHERE id NOT IN
                (SELECT char_id 
                FROM guild_membership
                WHERE char_id = id)"
        );

        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        return $results;
    }
    
    // Get characters owned by player with the chosen ID that are guildless
    function GetGuildlessOwned($player_id) 
    {
        $statement = $this->conn->prepare(
            "SELECT id, name
            FROM " . $this->table_name . "
            WHERE player_id = ? AND id NOT IN
                (SELECT char_id 
                FROM guild_membership
                WHERE char_id = id)"
        );
        
        $statement->bindParam(1, $player_id, PDO::PARAM_INT);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        return $results;
    }
    
    // Check if the player with the chosen player_id owns the character with the chosen char_id
    function ValidateOwnership($char_id, $player_id) {
        $statement = $this->conn->prepare(
            "SELECT id
            FROM " . $this->table_name . "
            WHERE id = :char_id 
            AND player_id = :player_id"
        );
        
        $statement->bindParam(':char_id', $char_id, PDO::PARAM_INT);
        $statement->bindParam(':player_id', $player_id, PDO::PARAM_INT);
        $statement->execute();
        
        $rowCount = $statement->rowCount();
        if ($rowCount > 0){
            return true;
        }
        return false;
    }
}