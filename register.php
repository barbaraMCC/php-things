<?php
 $servername = "localhost";
 $username = "root";
 $password = "";
 $dbname = "lab_equipment_res";
 // Create connection
 $conn = new mysqli($servername, $username, $password, $dbname);
 // Check connection
 if ($conn->connect_error) {
 die("Connection failed: " . $conn->connect_error);
 } else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
    }
    $insert_query = "INSERT INTO user (user_name,email,password) VALUES
    ('$username','$email','$password')";
    if ($conn->query($insert_query) === TRUE) {
        echo "New record created successfully";
    }
    else {
        echo "Error: " . $insert_query . "<br>" . $conn->error;
    }

    $conn->close();
 }
 header("Location: index.html");
 exit;
?>