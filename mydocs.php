<?php

$filename = "";

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

// Kullanıcı oturumunu kontrol etme
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;

if (isset($_POST['logout'])) {
    // Tüm oturum değişkenlerini temizleme
    session_unset();

    // Oturumu sonlandırma
    session_destroy();

    // Kullanıcıyı login sayfasına yönlendirme
    header("Location: login.php");
    exit;
}

if ($_SESSION['company'] == 'Omix') {

} else {
    header('Location: anasayfa');
}

$sql2 = "SELECT * FROM docs"; // Tablo adınızı buraya yazın
$result2 = $conn->query($sql2);

$mixSign = 'OMIX_';
$folder = "docs/"; // Dosyaların kaydedileceği klasör
$filename = $mixSign . ""; // Dosya adı için boş bir başlangıç değeri

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Form gönderildiğinde, ilgili verileri al
    if (isset($_POST['docname']) && isset($_POST['docdesp']) && isset($_FILES['fileToUpload'])) {
        // Form verilerini kullan
        $docname = $_POST['docname'];
        $docdesp = $_POST['docdesp'];
        $filename = $mixSign . $_FILES["fileToUpload"]["name"];
        $tmpname = $_FILES["fileToUpload"]["tmp_name"];

        // Dosyanın yükleneceği yol
        $uploadPath = $folder . $filename;

        // Aynı ada sahip dosyanın var olup olmadığını kontrol et
        $i = 1;
        while (file_exists($uploadPath)) {
            // Dosya adına parantez içinde numaralandırma ekle
            $file_info = pathinfo($filename);
            $new_filename = $mixSign . $file_info['filename'] . "($i)." . $file_info['extension'];
            $uploadPath = $folder . $new_filename;
            $i++;
        }

        $allowedTypes = array("application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/pdf", "application/zip", "application/x-rar-compressed", "application/x-7z-compressed", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

        // Dosya türü kabul edilenler arasındaysa işlemlere devam edin

        // Dosyayı yükle
        if (move_uploaded_file($tmpname, $uploadPath)) {
            if (in_array($_FILES["fileToUpload"]["type"], $allowedTypes)) {

                // Dosya başarıyla yüklendiğinde
                $sql3 = "INSERT INTO docs (doc_name, despcription, link, owner) VALUES ('$docname', '$docdesp', '$uploadPath', '{$_SESSION['username']}')";
                if ($conn->query($sql3) === TRUE) {
                    echo "Dosya başarıyla yüklendi.";
                    header("Location: dosyalarım");
                    exit;
                } else {
                    echo "Hata: " . $sql3 . "<br>" . $conn->error;
                }
            }
        } else {
            // Dosya yükleme işlemi başarısız olduğunda
            echo "Dosya yüklenirken bir hata oluştu.";
        }
    } else {
        // Gerekli alanlar doldurulmamışsa hata mesajı göster
        echo "Lütfen tüm alanları doldurun.";
    }
}

if (isset($_POST['delete']) && isset($_POST['doc_id'])) {
    $doc_id = $_POST['doc_id'];

    // Dosyayı silmek için SQL sorgusu
    $sql_delete = "DELETE FROM docs WHERE id=$doc_id";

    // Sadece admin veya dosyayı yükleyen kişi silebilir
    if ($isAdmin) {
        if ($conn->query($sql_delete) === TRUE) {
            header("Location: dosyalarım");
        } else {
            echo "<script>alert('Dosya silinirken bir hata oluştu: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Sadece yönetici veya dosyayı yükleyen kişi dosyayı silebilir.');</script>";
    }
}

if (isset($_POST['update']) && isset($_POST['doc_id']) && isset($_POST['new_doc_name']) && isset($_POST['new_doc_desp'])) {
    $doc_id = $_POST['doc_id'];
    $new_doc_name = $_POST['new_doc_name'];
    $new_doc_desp = $_POST['new_doc_desp'];

    // Dosya adı ve açıklamasını güncellemek için SQL sorgusu
    $sql_update = "UPDATE docs SET doc_name='$new_doc_name', despcription='$new_doc_desp' WHERE id=$doc_id";

    // Sadece admin veya dosyayı yükleyen kişi düzenleyebilir
    if ($isAdmin) {
        if ($conn->query($sql_update) === TRUE) {
            header("Location: dosyalarım");
        } else {
            echo "<script>alert('Dosya güncellenirken bir hata oluştu: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Sadece yönetici veya dosyayı yükleyen kişi dosyayı düzenleyebilir.');</script>";
    }
}



if (move_uploaded_file($tmpname, $folder . $filename)) {
    // SQL sorgusu ile dosya bilgilerini veritabanına ekle
    $sql3 = "INSERT INTO docs (doc_name, despcription, link, owner) VALUES ('$docname', '$docdesp', '$folder$filename', '{$_SESSION['username']}')";

    if ($conn->query($sql3) === TRUE) {
        echo "Dosya başarıyla yüklendi.";
        header("Location: dosyalarım");
    } else {
        echo "Hata: " . $sql3 . "<br>" . $conn->error;
    }
} else {
    echo "Dosya yüklenirken bir hata oluştu.";
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="TR" style="background: #f1f4f9;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
    <title>Omix Tester</title>
    <style>
        --bs-bg-opacity: 1;

            {
            background-color: rgba(var(--bs-light-rgb), var(--bs-bg-opacity)) !important;
        }

        .fs-6 {
            font-size: 1rem !important;
        }

        .form-control-lg {
            min-height: calc(1.5em + 1rem + 2px);
            padding: .5rem 1rem;
            font-size: 1.25rem;
            border-radius: .5rem;
        }

        .form-control {
            display: block;
            width: 100%;
            padding: .375rem .75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border-radius: .375rem;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .dropdown-content {
            display: none;
        }

        .textboxedit {
            /* background: var(--main-color); */
            color: #000;
            height: 25px;
            margin: 5px;
            border-radius: 4px;
            padding: 0rem 1rem;
            border: none;
            font-weight: 600;
            width: -moz-available;
            width: -webkit-fill-available;
        }
    </style>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
        href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
</head>

<body>
    <input type="checkbox" id="menu-toggle">
    <div class="sidebar">
        <div class="side-header">
            <h3>O<span>mix Test</span></h3>
        </div>
        <div class="side-content">
            <div class="profile">
                <div class="profile-img bg-img" style="background-image: url(img/mixlogo.png)"></div>
                <h4><?php echo $_SESSION['username']; ?></h4>
                <small><?php echo $_SESSION['role']; ?></small><br>
                <small><?php echo $_SESSION['company']; ?></small>
            </div>
            <div class="side-menu">
                <ul>
                    <li>
                        <a href="./anasayfa">
                            <span class="las la-home"></span>
                            <small>Ana Sayfa</small>
                        </a>
                    </li>
                    <?php if ($_SESSION['company'] == 'Omix'): ?>
                        <li>
                            <a href="./dosyalarım" class="active">
                                <span class="las la-user-alt"></span>
                                <small>Dosyalar</small>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="./arge-cihazları">
                            <span class="las la-clipboard-list"></span>
                            <small>Cihazlar</small>
                        </a>
                    </li>
                    <?php if ($_SESSION['company'] == 'Omix'): ?>
                        <li>
                            <a href="./inovasyon">
                                <span class="material-symbols-outlined" style="color: #899DC1;">
                                    library_add
                                </span>
                                <small>Özellik Önerileri</small>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="./test-case-dosyaları">
                            <span class="las la-tasks"></span>
                            <small>Test Case Dosyaları</small>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="main-content">
        <header>
            <div class="header-content">
                <label for="menu-toggle">
                    <span class="las la-bars"></span>
                </label>
                <div class="header-menu">
                    <div class="notify-icon">
                        <span class="las la-envelope"></span>
                        <span class="notify">4</span>
                    </div>
                    <div class="notify-icon">
                        <span class="las la-bell"></span>
                        <span class="notify">3</span>
                    </div>
                    <form id="logoutForm" method="POST">
                        <div class="user">
                            <div class="bg-img" style="background-image: url(img/mixlogo.png)"></div>
                            <div class="user" style="cursor: pointer; margin-right:25px;" onclick="logout()">
                                <span class="las la-power-off"></span>
                                <span>Logout</span>
                                <input type="hidden" name="logout" value="1">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </header>
        <main>
            <div class="page-header">
                <h1>Omix Test</h1>
                <small>Dosyalarım</small>
            </div>

            <div class="record-header">
                <!-- Sadece Takım Lideri Görüntüleyebilir -->
                <div class="add">
                    <button onclick="toggleForm()">Yeni Dosya</button>
                </div>
            </div>

            <div class="records table-responsive">
                <div class="record-header">
                </div>
                <div id="addTestCaseForm" style="display:none; padding:10px">
                    <form method="POST" action="dosyalarım" enctype="multipart/form-data">
                        <input type="text" name="docname" class="form-control form-control-lg bg-light fs-6"
                            placeholder="Dosya Adı" required><br>
                        <input type="text" name="docdesp" class="form-control form-control-lg bg-light fs-6"
                            placeholder="Dosya Açıklaması" required><br>
                        <input type="file" class="form-control form-control-lg bg-light fs-6" name="fileToUpload"
                            id="fileToUpload" accept=".docx,.pdf,.zip,.rar,.7z,.xlsx" required><br>
                        <button
                            style="background: var(--main-color);color: #fff;height: 37px;border-radius: 4px;padding: 0rem 1rem;border: none;font-weight: 600;width: -moz-available;width: -webkit-fill-available;"
                            type="submit">Test Dosya Ekle</button>
                    </form>
                </div>
                <div>

                    <table width="100%">
                        <thead>
                            <tr>
                                <th>Dosya Adı</th>
                                <th>Dosya Açıklaması</th>
                                <th>Dosya Link'i</th>
                                <th>Dosya Sahibi</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $site_domain = "localhost/";
                            if ($result2->num_rows > 0) {
                                while ($row2 = $result2->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row2["doc_name"] . "</td>";
                                    echo "<td>" . $row2["despcription"] . "</td>";
                                    echo "<td><a style='background: var(--main-color);padding: 6px;color: #fff;height: 25px;margin: 5px;border-radius: 4px;border: none;font-weight: 600;width: -moz-available;width: -webkit-fill-available;' href='" . $row2["link"] . "'> Dosyayı İndir </a>";
                                    echo "<a 
    style='background: var(--main-color);padding: 6px;color: #fff;height: 25px;margin: 5px;border-radius: 4px;border: none;font-weight: 600;width: -moz-available;width: -webkit-fill-available;' 
    href='" . $row2["link"] . "' 
    onclick='copyLink(event, \"" . $site_domain . $row2["link"] . "\")'> Dosyayı Kopyala </a></td>";

                                    echo "<td>" . $row2["owner"] . "</td>";
                                    echo "<td>";
                                    // Sadece admin veya dosyayı yükleyen kişi için silme ve düzenleme butonları
                                    if ($isAdmin || $_SESSION['username'] == $row2["owner"]) {
                                        echo "<form method='POST' action='dosyalarım'>";
                                        echo "<input type='hidden' name='doc_id' value='" . $row2["id"] . "'>";
                                        echo "<input style='background: var(--main-color);color: #fff;height: 25px;margin: 5px;border-radius: 4px;padding: 0rem 1rem;border: none;font-weight: 600;width: -moz-available;width: -webkit-fill-available;' type='submit' name='delete' value='Sil' onclick='return confirm(\"Bu dosyayı silmek istediğinizden emin misiniz?\")'>";
                                        echo "</form>";
                                        // Düzenleme formu
                                        echo "<div class='dropdown'>";
                                        echo "<button onclick='toggleMenu(\"menu_" . $row2["id"] . "\")' class='dropdown-btn' style='background: var(--main-color);color: #fff;height: 25px;margin: 5px;border-radius: 4px;padding: 0rem 1rem;border: none;font-weight: 600;width: -moz-available;width: -webkit-fill-available;'>Düzenle</button>";
                                        echo "<div id='menu_" . $row2["id"] . "' class='dropdown-content'>";
                                        echo "<form method='POST' action='dosyalarım'>";
                                        echo "<input type='hidden' name='doc_id' value='" . $row2["id"] . "'>";
                                        echo "<input class='textboxedit' type='text' name='new_doc_name' placeholder='Yeni Dosya Adı'>";
                                        echo "<input class='textboxedit' type='text' name='new_doc_desp' placeholder='Yeni Dosya Açıklaması'>";
                                        echo "<input style='background: var(--main-color);color: #fff;height: 25px;margin: 5px;border-radius: 4px;padding: 0rem 1rem;border: none;font-weight: 600;width: -moz-available;width: -webkit-fill-available;' type='submit' name='update' value='Güncelle'>";
                                        echo "</form>";
                                        echo "</div>";
                                        echo "</div>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>Veri bulunamadı.</td></tr>";
                            }
                            echo "<script>
    function copyLink(event, link) {
        event.preventDefault();
        navigator.clipboard.writeText(link)
            .then(() => {
                alert('Bağlantı kopyalandı: ' + link);
            })
            .catch(err => {
                console.error('Bağlantı kopyalanırken bir hata oluştu: ', err);
            });
    }
</script>";
                            ?>
                        </tbody>
                    </table>


                </div>
            </div>
    </div>
    </main>
    </div>
    <script>
        function toggleForm() {
            var form = document.getElementById("addTestCaseForm");
            if (form.style.display === "none") {
                form.style.display = "block";
            } else {
                form.style.display = "none";
            }
        }

        function logout() {
            document.getElementById('logoutForm').submit();
        }
        function toggleMenu(menuId) {
            var menu = document.getElementById(menuId);
            if (menu.style.display === "block") {
                menu.style.display = "none";
            } else {
                menu.style.display = "block";
            }
        }

    </script>
</body>

</html>