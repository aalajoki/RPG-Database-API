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
}