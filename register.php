<?php
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
        $username = $_POST['username'];
        $email = $_POST['email'];
        $pass = $_POST['password'];
    }

    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

    $insert_query = "INSERT INTO user (user_name,email,password) VALUES
    ('$username','$email','$hashed_password')";
    if ($conn->query($insert_query) === TRUE) {
        echo "New record created successfully";

        $last_id = $conn->insert_id;
        session_start();
        $_SESSION['user_id'] = $last_id;
        
    }
    else {
        echo "Error: " . $insert_query . "<br>" . $conn->error;
    }

    $conn->close();
 }
 header("Location: index.php");
 exit;
?>