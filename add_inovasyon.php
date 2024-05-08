<?php
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

// Kullanıcı oturumunu kontrol etme ve mühendis adını alın
session_start();
$engineer = $_SESSION['username'];

// Formdan gelen bilgileri al
$oneri = $_POST['oneri'];
$despcription = $_POST['despcription'];

// Veritabanına ekleme sorgusu
$sql = "INSERT INTO inovasyon (engineer, oneri, despcription) 
        VALUES ('$engineer', '$oneri', '$despcription')";

if ($conn->query($sql) === TRUE) {
    echo "Yeni test case başarıyla eklendi.";
    header("Location: inovasyon.php");
} else {
    echo "Hata: " . $sql . "<br>" . $conn->error;
}

// Bağlantıyı kapatma
$conn->close();
?>
