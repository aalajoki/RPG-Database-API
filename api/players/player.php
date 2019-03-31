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

        $statement->bindParam(':username', $this->username);
        $statement->bindParam(':email', $this->email);

        // Hash password
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $statement->bindParam(':password', $password_hash);

        // Check if the email and username are already in use
        $this->UniqueExists("email");
        $this->UniqueExists("username");
        
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

        $statement->bindParam(1, $this->email, PDO::PARAM_STR);

        $statement->execute();

        $num = $statement->rowCount();
        // if email exists, assign values to object properties for easy access and use for php sessions
        if ($num > 0){

            // get record details / values
            $row = $statement->fetch(PDO::FETCH_ASSOC);

            // assign values to object properties
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