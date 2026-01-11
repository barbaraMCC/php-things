<?php

 session_start();

 $servername = "localhost";
 $username = "root";
 $password = "";
 $dbname = "lab_equipment";

 // Create connection
 $conn = new mysqli($servername, $username, $password, $dbname);

 // Check connection
 if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
 } else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
    }

    // Prepare SQL statement
    $stmt = $conn->prepare("SELECT user_id,password FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

     if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if ($row['password'] === $password) {
            // Login success
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['logged']  = true;
            header("Location: index.html");
            exit;
        } else {
            echo "❌ Incorrect password";
        } 
        
    } else {
        echo "❌ User not found";
    }

        $stmt->close();

    }
 

 $conn->close();

?>