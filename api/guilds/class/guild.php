<?php
class Guild 
{
 
    // Database connection
    private $conn;
    
    private $table_name = "guild";
 
    public $id;
    public $name;
    public $guild_type;
    public $description;
 
    public function __construct($db)
    {
        $this->conn = $db;
    }
 
    function Create($gm_id)
    {
        $statement = $this->conn->prepare(
            "INSERT INTO " . $this->table_name . "
             SET
             name = :name,
             guild_type = :guild_type,
             description = :description"
        );

        // Remove any HTML
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));

        $statement->bindParam(':name', $this->name, PDO::PARAM_STR);
        $statement->bindParam(':guild_type', $this->guild_type, PDO::PARAM_INT);
        $statement->bindParam(':description', $this->description, PDO::PARAM_STR);

        // Check if the name is already in use
        $this->UniqueExists("name");
        
        if($statement->execute()){
            // ID of the created guild
            $guild_id = $this->conn->lastInsertId();
            
            // Guild has been created: add GM as the first member
            $statement = $this->conn->prepare(
                "INSERT INTO guild_membership
                 SET
                 guild_id = :guild_id,
                 char_id = :char_id,
                 char_rank = 5"
            );
            $statement->bindParam(':guild_id', $guild_id, PDO::PARAM_INT);
            $statement->bindParam(':char_id', $gm_id, PDO::PARAM_INT);
            $statement->execute();
            
            return $guild_id;
        }
        return false;
    }
    
    function Edit($guild_id)
    {
        $statement = $this->conn->prepare(
            "UPDATE " . $this->table_name . "
             SET
             name = :name,
             guild_type = :guild_type,
             description = :description
             WHERE id = :guild_id"
        );

        // Remove any HTML
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));

        $statement->bindParam(':name', $this->name, PDO::PARAM_STR);
        $statement->bindParam(':guild_type', $this->guild_type, PDO::PARAM_INT);
        $statement->bindParam(':description', $this->description, PDO::PARAM_STR);
        $statement->bindParam(':guild_id', $guild_id, PDO::PARAM_INT);

        // Check if the name is already in use
        $this->UniqueExists("name");
        
        if($statement->execute()){
            return true;
        }
        return false;
    }
    
    // Check if given unique property already exists or not
    function UniqueExists($uniqueProperty) {
        
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
    
    // Check if the player with the chosen player_id owns the guild with the chosen guild_id
    function ValidateOwnership($guild_id, $player_id) {
        $statement = $this->conn->prepare(
            "SELECT a.guild_id
            FROM guild_membership a
            LEFT JOIN player_character b
            ON a.char_id = b.id
            WHERE a.guild_id = :guild_id
            AND a.char_rank = 5
            AND b.player_id = :player_id"
        );
        
        $statement->bindParam(':guild_id', $guild_id, PDO::PARAM_INT);
        $statement->bindParam(':player_id', $player_id, PDO::PARAM_INT);
        $statement->execute();
        
        $rowCount = $statement->rowCount();
        if ($rowCount > 0){
            return true;
        }
        return false;
    }
}