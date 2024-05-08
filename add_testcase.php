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

session_start();
$engineer = $_SESSION['username'];

// Formdan gelen bilgileri al
$test_name = $_POST['test_name'];
$importance = $_POST['importance'];
$description = $_POST['description'];
$creation_date = date("Y-m-d");

// Dosyayı yükleme işlemi
$target_directory = "testcases/"; // Dosyaların yükleneceği klasör
$target_file = $target_directory . "OMIX_" . basename($_FILES["file"]["name"]); // Dosyanın yolunu oluştur
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Geçerli dosya türlerini belirle
$allowedTypes = array("application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/pdf", "application/zip", "application/x-rar-compressed", "application/x-7z-compressed", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

// Dosya türünü kontrol et
if (!in_array($_FILES["file"]["type"], $allowedTypes)) {
    echo "Hata: Geçersiz dosya türü.";

    $uploadOk = 0;
}

if ($uploadOk == 0) {
    header('Location: testcases.php');
}

// Dosyayı yükleme işlemini dene
if ($uploadOk == 1 && move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
    // Dosya yükleme başarılı olduğunda, veritabanına ekleme işlemini gerçekleştir
    $sql = "INSERT INTO test_cases (test_name, importance, engineer, creation_date, download_link, despcription) 
            VALUES ('$test_name', '$importance', '$engineer', '$creation_date', '$target_file', '$description')";

    if ($conn->query($sql) === TRUE) {
        echo "Yeni test case başarıyla eklendi.";
        header("Location: testcases.php");
    } else {
        echo "Hata: " . $sql . "<br>" . $conn->error;
    }
} elseif ($uploadOk == 0) {
    echo "Dosya yüklenirken bir hata oluştu.";
}

// Bağlantıyı kapatma
$conn->close();
?>