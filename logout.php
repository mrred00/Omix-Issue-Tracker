<?php
// Önceki oturumu başlat
session_start();

// Eğer kullanıcı giriş yapmışsa
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true){
    // Tüm oturum değişkenlerini temizle
    $_SESSION = array();
    
    // Oturumu sonlandır
    session_destroy();
    
    // Kullanıcıyı giriş sayfasına yönlendir
    header("location: login.php");
    exit;
} else {
    // Kullanıcı zaten oturum açmamışsa, istenmeyen bir durum olabilir
    // İsterseniz burada bir hata mesajı gösterebilir veya başka bir şey yapabilirsiniz
}
?>
