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

// Formdan gelen bilgileri al
$engineer = $_POST['engineer'];
$device_name = $_POST['device_name'];
$startdate = $_POST['startdate'];
$totalissue = 0;

// Veritabanına ekleme sorgusu
$sql = "INSERT INTO devices (engineer, device_name, startdate, total_issue) 
        VALUES ('$engineer', '$device_name', '$startdate', '$totalissue')";

if ($conn->query($sql) === TRUE) {
    echo "Yeni Cihaz başarıyla eklendi.";
    header("Location: ./");
} else {
    echo "Hata: " . $sql . "<br>" . $conn->error;
}

// Bağlantıyı kapatma
$conn->close();
?>
