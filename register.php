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
    $username = $_POST['username']; // Kullanıcı adını al
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Şifreyi hashleme
    
    // Veritabanında aynı e-posta adresine sahip bir kullanıcı var mı diye kontrol etme
    $email_check_sql = "SELECT * FROM users WHERE email='$email'";
    $email_check_result = $conn->query($email_check_sql);
    
    if ($email_check_result->num_rows > 0) {
        $error = "Bu e-posta adresi zaten kullanımda.";
        header("Location: index2.php");
    } else {
        // Kullanıcının rolüne göre admin olup olmadığını belirleme
        if ($role == 'Takım Lideri' || $role == 'CEO' || $role == 'CTO' || $role == 'Ürün Geliştirme Direktörü' || $role == 'Ürün Geliştirme Müdürü') {
            $admin = 1;
        } else {
            $admin = 0;
        }

        // Kullanıcıyı veritabanına ekleme
        $insert_sql = "INSERT INTO users (email, username, password, role, admin, company) VALUES ('$email', '$username', '$password', '$role', '$admin', 'Omix')";

        if ($conn->query($insert_sql) === TRUE) {
            header("Location: ./index2.php");
            exit;
        } else {
            $error = "Kayıt oluşturulurken bir hata oluştu: " . $conn->error;
        }
    }
}

$conn->close();
?>
