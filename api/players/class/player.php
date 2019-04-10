<?php
class Player 
{
    // Database connection
    private $conn;
    
    private $table_name = "player";
 
    public $id;
    public $username;
    public $email;
    public $password;
 
    // Constructor with setting the database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }
 
    function Create()
    {
        $statement = $this->conn->prepare(
            "INSERT INTO " . $this->table_name . "
             SET
             username = :username,
             email = :email,
             password = :password"
        );

        // Remove any HTML
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));

        // Bind parameters into the prepared statement
        $statement->bindParam(':username', $this->username, PDO::PARAM_STR);
        $statement->bindParam(':email', $this->email, PDO::PARAM_STR);

        // Hash password
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $statement->bindParam(':password', $password_hash);

        // Check if the email and username are already in use
        $this->UniqueExists("email");
        $this->UniqueExists("username");
        
        if ($statement->execute()){
            $player_id = $this->conn->lastInsertId();
            return $player_id;
        }
    }
    
    // Check if given unique property already exists or not
    function UniqueExists($uniqueProperty) {
        
        $statement = $this->conn->prepare(
           "SELECT id
            FROM " . $this->table_name . "
            WHERE $uniqueProperty = ?"
        );
        
        // Remove HTML
        $this->$uniqueProperty = htmlspecialchars(strip_tags($this->$uniqueProperty));
        
        // Bind parameters into the prepared statement
        $statement->bindParam(1, $this->$uniqueProperty, PDO::PARAM_STR);
        
        $statement->execute();
        
        // If results are found, the unique property is already in use
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
    
    
    // Check if given email exist in the database
    function EmailExists()
    {
        $query = "SELECT id, username, password
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";

        
        $statement = $this->conn->prepare($query);

        // Remove any HTML
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        // Bind parameters
        $statement->bindParam(1, $this->email, PDO::PARAM_STR);
        
        $statement->execute();

        // Email exists if there are results to the query
        $num = $statement->rowCount();
        if ($num > 0){
            // If email exists, get the rest of the account data
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->password = $row['password'];
            return true;
        }
        return false;
    }
    
    
    // Update player information
    public function Update()
    {

        // Optional password update
        $password_set=!empty($this->password) ? ", password = :password" : "";

        $query = "UPDATE " . $this->table_name . "
                SET
                    username = :username,
                    email = :email
                    {$password_set}
                WHERE id = :id";

        // prepare the query
        $statement = $this->conn->prepare($query);

        // Remove any HTML
        $this->username=htmlspecialchars(strip_tags($this->username));
        $this->email=htmlspecialchars(strip_tags($this->email));

        $statement->bindParam(':username', $this->username, PDO::PARAM_STR);
        $statement->bindParam(':email', $this->email, PDO::PARAM_STR);

        // Hash password
        if (!empty($this->password)){
            $this->password=htmlspecialchars(strip_tags($this->password));
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $statement->bindParam(':password', $password_hash, PDO::PARAM_STR);
        }

        // unique ID of record to be edited
        $statement->bindParam(':id', $this->id, PDO::PARAM_INT);

        if ($statement->execute()){
            return true;
        }

        return false;
    }
}