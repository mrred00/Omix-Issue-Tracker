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

// Kullanıcı oturumunu kontrol etme
session_start();

// Eğer kullanıcı giriş yapmamışsa login sayfasına yönlendirme
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['delete'])) {
    // Sadece oturum açmış kullanıcılar ve kendi ekledikleri öğeleri silebilir veya takım lideri rolüne sahip olanlar silme işlemi yapabilir
    $id = $_POST['delete'];
    $engineer = $_SESSION['username'];
    $role = $_SESSION['role'];
    $sql_check = "SELECT * FROM inovasyon WHERE id = $id AND (engineer = '$engineer' OR '$role' = 'Takım Lideri')";
    $result_check = $conn->query($sql_check);
    if ($result_check->num_rows > 0) {
        $sql_delete = "DELETE FROM inovasyon WHERE id = $id";
        if ($conn->query($sql_delete) === TRUE) {
            // Silme işlemi başarılı olduysa
            echo "Kayıt başarıyla silindi.";
        } else {
            // Silme işlemi sırasında bir hata oluştuysa
            echo "Error deleting record: " . $conn->error;
        }
    } else {
        // Yetkisiz kullanıcılar için hata mesajı
        echo "Bu işlemi gerçekleştirmek için yetkiniz yok.";
    }
}

$sql1 = "SELECT * FROM inovasyon";
$result1 = $conn->query($sql1);

if (isset($_POST['logout'])) {
    // Tüm oturum değişkenlerini temizleme
    session_unset();

    // Oturumu sonlandırma
    session_destroy();

    // Kullanıcıyı login sayfasına yönlendirme
    header("Location: login.php");
    exit;
}


// Bağlantıyı kapatma
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
    </style>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
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
                    <?php if($_SESSION['company'] == 'Omix'): ?>
                    <li>
                        <a href="./dosyalarım">
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
                    <?php if($_SESSION['company'] == 'Omix'): ?>
                    <li>
                        <a href="./inovasyon" class="active">
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
                <small>Özellik Önerisi - İnovasyon / Kontrol Paneli</small>
            </div>
            <div class="records table-responsive">
                <div class="record-header">
                    <div class="add">
                        <button onclick="toggleForm()">Yeni Öneri Ekle</button>
                    </div>
                </div>
                <div id="addTestCaseForm" style="display:none; padding:10px">
                    <form method="POST" action="add_inovasyon.php">
                        <input type="text" name="oneri" class="form-control form-control-lg bg-light fs-6"
                            placeholder="İnovatif Öneriniz" required><br>
                        <input type="text" name="despcription" class="form-control form-control-lg bg-light fs-6"
                            placeholder="Öneri Açıklaması" required><br>
                        <button
                            style="background: var(--main-color);color: #fff;height: 37px;border-radius: 4px;padding: 0rem 1rem;border: none;font-weight: 600;width: -moz-available;width: -webkit-fill-available;"
                            type="submit">Test Case Ekle</button>
                    </form>
                </div>
                <div>
                    <table width="100%">
                        <thead>
                            <tr>
                                <th>Öneren Mühendis</th>
                                <th>Öneri</th>
                                <th>Açıklama</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result1->num_rows > 0) {
                                while ($row = $result1->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="client">
                                                <div class="client-info">
                                                    <h4><?php echo $row['engineer']; ?></h4>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $row['oneri']; ?></td>
                                        <td><?php echo $row['despcription']; ?></td>
                                        <td>
                                            <?php if ($_SESSION['username'] == $row['engineer'] || $_SESSION['role'] == 'Takım Lideri' || $_SESSION['admin'] == 1): ?>
                                                <form method="POST" onsubmit="return confirmDelete();">
                                                    <button
                                                        style="background: var(--main-color); color: #fff; height: 37px; border-radius: 4px; padding: 0rem 1rem; border: none; font-weight: 600; width: -moz-available; width: -webkit-fill-available; margin-right: 10px;"
                                                        type="submit" name="delete" value="<?php echo $row['id']; ?>">Sil</button>
                                                </form>
                                            <?php endif; ?>
                                        </td>

                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='4'>Kayıt bulunamadı.</td></tr>";
                            }
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

        function confirmDelete() {
            return confirm("Bu öneriyi silmek istediğinizden emin misiniz?");
        }

        function logout() {
            document.getElementById('logoutForm').submit();
        }
    </script>
</body>

</html>