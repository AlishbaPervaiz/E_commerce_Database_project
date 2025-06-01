<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = uniqid("user_"); // Generates a unique user ID
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Secure hash

    $sql = "INSERT INTO User (user_id, name, email, contact, address, password)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $user_id, $name, $email, $contact, $address, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Signup successful!'); window.location.href='login.html';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
