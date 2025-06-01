<?php

namespace App;

use  mysqli;


class Database {
    private $host = 'localhost';
    private $database = 'social_network';
    private $username = 'root';
    private $password = '';
    public $conn;

    // Get the database connection

    public function __construct()
    {
        // hostname username password database
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
       
    }

    public function checkConnection()
    {
        if ($this->conn->connect_error == null)
            echo "Connected To Database";
        else
            echo "Not Connected To Database";
    }
}