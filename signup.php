<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];    
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure version

    $sql = "INSERT INTO User (username, name, email, contact, address, password)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $username, $name, $email, $contact, $address, $password);


    if ($stmt->execute()) {
        echo "<script>
                alert('Signup successful!'); 
                window.location.href='login.html';
              </script>";
    } else {
        // Enhanced error handling
        if ($stmt->errno == 1062) {
            echo "<script>
                    alert('Username or email already exists');
                    window.history.back();
                  </script>";
        } else {
            echo "<script>
                    alert('Registration error: ".addslashes($stmt->error)."');
                    window.history.back();
                  </script>";
        }
    }
    
    $stmt->close();
}
?>
