<?php
// DatabaseConnection.php
// Simple wrapper around mysqli, used by every Control script in the project.

class DatabaseConnection
{
    private $servername = "localhost";
    private $username = "root";
    private $password = "";
    private $dbname = "quizhub";

    // Opens and returns a mysqli connection
    public function OpenCon()
    {
        $conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        if ($conn->connect_error) {
            die("Connection Failed: " . $conn->connect_error);
        }
        return $conn;
    }

    // Closes a mysqli connection
    public function CloseCon($conn)
    {
        $conn->close();
    }
}
