<?php
session_start();

// Veritabanı bağlantısı
$servername = "localhost"; // Sunucu adı
$username = "root"; // Veritabanı kullanıcı adı
$password = ""; // Veritabanı parolası
$dbname = "mixtest"; // Veritabanı adı

// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

// Formdan gelen verileri kontrol etme
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin = 0;
    $email = $_POST['email'];
    $role = $_POST['role'];
    $company = $_POST['company'];
    $username = $_POST['username']; // Kullanıcı adını al
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Şifreyi hashleme
    

    // Kullanıcıyı veritabanına ekleme
    $sql = "INSERT INTO users (email, username, password, role, admin, company) VALUES ('$email', '$username', '$password', '$role', '$admin', '$company')";

    if ($conn->query($sql) === TRUE) {
        header("Location: index2.php");
        exit;
    } else {
        $error = "Kayıt oluşturulurken bir hata oluştu: " . $conn->error;
    }
}

$conn->close();
?>