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

if (isset($_POST['delete_id'])) {
    // Silinecek cihazın ID'sini al
    $delete_id = $_POST['delete_id'];

    // Kullanıcının bilgilerini al
    $user_info = getUserInfo($delete_id);

    // Sadece Takım Lideri ise silme işlemini gerçekleştir
    if ($_SESSION['role'] == 'Takım Lideri' || $_SESSION['role'] == 'CTO' || $_SESSION['$admin'] == true || $_SESSION['admin'] == 1) {
        // Silme işlemini onaylama
        echo '<script>';
        echo 'if(confirm("Dikkat!! Şu anda ' . $user_info['username'] . ', isimli ve ' . $user_info['role'] . ' rütbesindeki kullanıcıyı siliyorsunuz. Bunu yapmak istediğinize emin misiniz?")) {';
        echo 'console.log("Silme işlemi onaylandı.");';
        echo '} else {';
        echo 'console.log("Silme işlemi iptal edildi.");';
        echo '}';
        echo '</script>';

        // Silme işlemini gerçekleştirme
        $sql_delete = "DELETE FROM users WHERE id = $delete_id";

        // Sorguyu çalıştır ve sonucu kontrol et
        if ($conn->query($sql_delete) === TRUE) {
            echo "Cihaz başarıyla silindi.";
            header("Location: ./");
        } else {
            echo "Kullanıcı silerken bir hata oluştu: " . $conn->error;
            header("Location: ./");
        }
    } else {
        echo "Sadece Takım Lideri Kullanıcı silebilir.";
    }
}

function getUserInfo($user_id)
{
    global $conn;
    $sql = "SELECT * FROM users WHERE id = $user_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return false;
    }
}


$userCompany = $_SESSION['company'];

$sql1 = "SELECT * FROM users";
// Eğer kullanıcı "Omix" firmasından değilse, sadece kendi firmasındaki kullanıcıları seçiyoruz
if ($userCompany !== 'Omix') {
    $sql1 .= " WHERE company = '$userCompany' OR company = 'Omix'";
}
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

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;

// Bağlantıyı kapatma
$conn->close();
?>
<!DOCTYPE html>
<html lang="TR" style="background: #f1f4f9;">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1">
    <title>Omix Tester</title>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
        href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
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
            margin: 5px;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .btn{
            margin: 5px;
            display: block;
            width: 100%;
            padding: .375rem .75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #fff;
            background-color: #577dff;
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

        th{
            background-color: #fff;
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
                        <a href="./index2.php" class="active">
                            <span class="las la-home"></span>
                            <small>Ana Sayfa</small>
                        </a>
                    </li>
                    <?php if ($_SESSION['company'] == 'Omix'): ?>
                        <li>
                            <a href="./dosyalarım">
                                <span class="las la-user-alt"></span>
                                <small>Dosyalar</small>
                            </a><
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
    </div>
    </div>
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <form method="post" action="">
                <div class="container">
                    <label for="psw"><b>Şifre</b></label>
                    <input type="password" placeholder="Şifrenizi Girin" name="psw" required>
                    <button type="submit" class="registerbtn">Sil</button>
                </div>
            </form>
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
                <small>Ana Sayfa / Kontrol Paneli</small>
                <div class="records table-responsive">
                    <div class="record-header">
                    </div>

                    <div>
                        <table width="100%">
                            <thead>
                                <tr>
                                    <th>Mail Adresi</th>
                                    <th>Frima</th>
                                    <th>Ad Soyad</th>
                                    <th>Mevki</th>
                                    <?php if ($isAdmin): ?>
                                        <th>Admin</th>
                                        <th>İşlemler</th>
                                    <?php endif ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Test case'lerini tabloya ekleme
                                if ($result1->num_rows > 0) {
                                    while ($row = $result1->fetch_assoc()) {
                                        ?>
                                        <tr>
                                            <td><a href="mailto:<?php echo $row['email']; ?>"><?php echo $row['email']; ?></a>
                                            </td>
                                            <td>
                                                <h4><?php echo $row['company']; ?></h4>
                                            </td>
                                            <td>
                                                <h4><?php echo $row['username']; ?></h4>
                                            </td>
                                            <td><?php echo $row['role']; ?></td>
                                            <?php if ($isAdmin): ?>
                                                <td><?php echo $row['admin']; ?></td>
                                                <td>
                                                    <?php
                                                    // Sadece Takım Lideri ise silme butonunu göster
                                                    if ($_SESSION['role'] == 'Takım Lideri' || $_SESSION['role'] == 'CTO' || $_SESSION['admin'] == true || $_SESSION['$admin'] == 1) {
                                                        echo '<form id="deleteForm' . $row['id'] . '" method="POST">';
                                                        echo '<input type="hidden" name="delete_id" value="' . $row['id'] . '">';
                                                        echo '<button onclick="confirmDelete(' . $row['id'] . ')" style="background: var(--main-color);color: #fff;height: 37px; margin-right: 10px;border-radius: 4px;padding: 0rem 1rem;border: none;font-weight: 600;width: -moz-available;width: -webkit-fill-available;" type="button">Sil</button>';
                                                        echo '</form>';
                                                    }
                                                    ?>
                                                </td>
                                            <?php endif ?>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>Kayıt bulunamadı.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div><br>
            <?php if($_SESSION['admin'] == true ||$_SESSION['admin'] == 1): ?>
            <div class="page-header">
                <h1>Kullanıcı Kayıt</h1>
                <small>Ana Sayfa / Kontrol Paneli</small><br>
                <br>
                <div class="header-text mb-4">
                     <h2 style="color: #74767d;">Omix Çalışan Kayıt Alanı</h2>
                     <p>Omix Test Platformuna ODM Friması Kaydı için aşağıdaki bilgileri doldurun.</p>
                </div>
                <form method="POST" action="register.php">
                    <div class="input-group mb-3">
                        <input type="mail" name="email" class="form-control form-control-lg bg-light fs-6"
                            placeholder="Email adresi" required="">
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="username" class="form-control form-control-lg bg-light fs-6"
                            placeholder="Kullanıcı adı" required="">
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="role" class="form-control form-control-lg bg-light fs-6"
                            placeholder="Mevki" required="">
                    </div>
                    <div class="input-group mb-1">
                        <input type="password" name="password" class="form-control form-control-lg bg-light fs-6"
                            placeholder="Şifre" required="">
                    </div>
                    <div class="input-group mb-5 d-flex justify-content-between">
                        <div class="forgot">
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <button type="submit" class="btn btn-lg btn-primary w-100 fs-6">Kayıt Ol</button>
                    </div>
                </form><br>
                <br>
                <div class="row align-items-center">
                <div class="header-text mb-4">
                     <h2 style="color: #74767d;">Odm Friması Kayıt Alanı</h2>
                     <p>Omix Test Platformuna ODM Friması Kaydı için aşağıdaki bilgileri doldurun.</p>
                </div>
                <form method="POST" action="odmregister.php">
                    <div class="input-group mb-3">
                        <input type="mail" name="email" class="form-control form-control-lg bg-light fs-6" placeholder="Email adresi" required>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="company" class="form-control form-control-lg bg-light fs-6" placeholder="ODM Friması " required>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="username" class="form-control form-control-lg bg-light fs-6" placeholder="Kullanıcı adı" required>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" name="role" class="form-control form-control-lg bg-light fs-6" placeholder="Mevki" required>
                    </div>
                    <div class="input-group mb-1">
                        <input type="password" name="password" class="form-control form-control-lg bg-light fs-6" placeholder="Şifre" required>
                    </div>
                    <div class="input-group mb-5 d-flex justify-content-between">
                        <div class="forgot">
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <button type="submit" class="btn btn-lg btn-primary w-100 fs-6">Kayıt Ol</button>
                    </div>
                </form>
                <div class="input-group mb-3">
                </div>
                <div class="row">
                </div>
          </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
    <script>
        function confirmDelete(userId) {
            if (confirm("Bu kullanıcıyı silmek istediğinize emin misiniz?")) {
                document.getElementById('deleteForm' + userId).submit();
            }
        }



        function logout() {
            document.getElementById('logoutForm').submit();
        }
    </script>
</body>

</html>