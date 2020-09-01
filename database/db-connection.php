<?php
    // require hidden database credentials
    require (__DIR__ . '/../secret/db-credentials.php');

    // setting connection variables
    $servername = $credentials['hostname'];
    $database = $credentials['database'];
    $username = $credentials['user'];
    $password = $credentials['password'];

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "Connected successfully";
        return $conn;
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        fwrite("Error: " . $e->getMessage());
        exit(1);
    }
?>