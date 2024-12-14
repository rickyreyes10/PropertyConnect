<?php

// this file is used to connect to the database

//this class is used to connect to the database
class Database {
    private $host = "localhost";
    private $user = "rreyespena1";
    private $pass = "rreyespena1";
    private $db = "rreyespena1";
    private $conn; //this is the connection to the database

    //this method is used to connect to the database
    public function connect() {
        //using try-catch to handle any exceptions that may occur
        try { //try to connect to the database
            //this->conn is the connection to the database
            $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db); //create a new mysqli object

            //check if the connection failed
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
            //return the connection
            return $this->conn;
        } catch (Exception $e) { //catch any exceptions
            error_log("Database Connection Error: " . $e->getMessage()); //log the error message
            throw $e; //throw the exception
        }
    }
}

?>