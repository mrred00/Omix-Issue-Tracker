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
    header('Location: index2.php');
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
                    header("Location: mydocs2.php");
                    exit;
                } else {
                    echo "Hata: " . $sql3 . "<br>" . $conn->error;
                }
            }
        } else {
            // Dosya yükleme işlemi başarısız olduğunda
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
            header("Location: mydocs2.php");
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
            header("Location: mydocs2.php");
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
        header("Location: mydocs2.php");
    } else {
        echo "Hata: " . $sql3 . "<br>" . $conn->error;
    }
} else {
}

$conn->close();
?>

<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Omix Test Dosyalar">
    <link rel="shortcut icon" sizes="196x196" href="./assets/images/logo.png">
    <title>Omix Test Admin</title>
    <link rel="stylesheet" href="./libs/bower/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="./libs/bower/material-design-iconic-font/dist/css/material-design-iconic-font.css">
    <!-- build:css ./assets/css/app.min.css -->
    <link rel="stylesheet" href="./libs/bower/animate.css/animate.min.css">
    <link rel="stylesheet" href="./libs/bower/fullcalendar/dist/fullcalendar.min.css">
    <link rel="stylesheet" href="./libs/bower/perfect-scrollbar/css/perfect-scrollbar.css">
    <link rel="stylesheet" href="./assets/css/bootstrap.css">
    <link rel="stylesheet" href="./assets/css/core.css">
    <link rel="stylesheet" href="./assets/css/app.css">
    <!-- endbuild -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:400,500,600,700,800,900,300">
    <script src="./libs/bower/breakpoints.js/dist/breakpoints.min.js"></script>
    <script>
        Breakpoints();
    </script>
</head>

<body class="menubar-left menubar-dark theme-dark    pace-done menubar-unfold">
    <div class="pace  pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99"
            style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div>
    <div class="pace  pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99"
            style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div>
    <div class="pace  pace-inactive">
        <div class="pace-progress" data-progress-text="100%" data-progress="99"
            style="transform: translate3d(100%, 0px, 0px);">
            <div class="pace-progress-inner"></div>
        </div>
        <div class="pace-activity"></div>
    </div>
    <!--============= start main area --> <!-- APP NAVBAR ==========-->
    <nav id="app-navbar" class="navbar navbar-inverse navbar-fixed-top in dark"> <!-- navbar header -->
        <div class="navbar-header">
            <button type="button" id="menubar-toggle-btn"
                class="navbar-toggle visible-xs-inline-block navbar-toggle-left hamburger hamburger--collapse js-hamburger">
                <span class="sr-only">Toggle navigation</span>
                <span class="hamburger-box"><span class="hamburger-inner"></span></span>
            </button> <button type="button" class="navbar-toggle navbar-toggle-right collapsed" data-toggle="collapse"
                data-target="#app-navbar-collapse" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="zmdi zmdi-hc-lg zmdi-more"></span>
            </button> <button type="button" class="navbar-toggle navbar-toggle-right collapsed" data-toggle="collapse"
                data-target="#navbar-search" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="zmdi zmdi-hc-lg zmdi-search"></span>
            </button> <a href="./index2.php" class="navbar-brand">
                <span class="brand-icon"><i class="fa fa-gg"></i></span>
                <span class="brand-name">OMIX</span>
            </a>
        </div><!-- .navbar-header -->
        <div class="navbar-container container-fluid">
            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <ul class="nav navbar-toolbar navbar-toolbar-left navbar-left">
                    <li class="hidden-float hidden-menubar-top">
                        <a href="javascript:void(0)" role="button" id="menubar-fold-btn"
                            class="hamburger hamburger--arrowalt js-hamburger is-active">
                            <span class="hamburger-box"><span class="hamburger-inner"></span></span>
                        </a>
                    </li>
                    <li>
                        <h5 class="page-title hidden-menubar-top hidden-float">Dosya Yönetim Sistemi - FTP</h5>
                    </li>
                </ul>
                <ul class="nav navbar-toolbar navbar-toolbar-right navbar-right">

                    <li class="dropdown">
                        <a href="logout.php" class="dropdown-toggle" data-toggle="dropdown" role="button"
                            aria-haspopup="true" aria-expanded="false"><i class="zmdi zmdi-hc-lg zmdi-settings"></i></a>
                        <ul class="dropdown-menu animated flipInY" href="logout.php">
                            <li><a href="logout.php"><i class="zmdi m-r-md zmdi-hc-lg zmdi-account-box"></i>Log Out</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <div class="app-user">
                    <div class="media">
                        <div class="media-left">
                            <div class="avatar avatar-md avatar-circle dropdown">
                                <a href="javascript:void(0)" data-toggle="dropdown">
                                    <img class="img-responsive" src="./assets/images/221.jpg" alt="avatar">
                                </a>

                            </div><!-- .avatar -->
                        </div>
                    </div><!-- .media -->
                </div>
            </div>
        </div><!-- navbar-container -->
    </nav>
    <!--========== END app navbar --> <!-- APP ASIDE ==========-->
    <aside id="menubar" class="menubar in dark">
        <div class="app-user">
            <div class="media">
                <div class="media-left">
                    <div class="avatar avatar-md avatar-circle dropdown">
                        <a href="javascript:void(0)" data-toggle="dropdown"><img class="img-responsive"
                                src="./img/mixlogo.png" alt="avatar"></a>
                    </div><!-- .avatar -->
                </div>
                <div class="media-body">
                    <div class="foldable">
                        <h5><a href="javascript:void(0)" class="username"><?php echo $_SESSION['username']; ?></a></h5>
                        <small><?php echo $_SESSION['mevki']; ?></small>
                        <small><?php echo $_SESSION['role']; ?></small><br>
                        <small><?php echo $_SESSION['company']; ?></small><br>
                        <small>
                            <?php if ($_SESSION['admin'] == 1) {
                                echo 'Admin Hesabı';
                            } else {
                                echo '';
                            } ?>
                        </small>
                        </small>
                    </div>
                </div><!-- .media-body -->
            </div><!-- .media -->
        </div><!-- .app-user -->
        <div class="menubar-scroll" style="height: 566px;">
            <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 566px;">
                <div class="menubar-scroll-inner" style="overflow: hidden; width: auto; height: 566px;">
                    <ul class="app-menu">
                        <li class="has-submenu">
                            <a href="mydocs2.php">
                                <i class="menu-icon zmdi zmdi-search zmdi-hc-lg"></i>
                                <span class="menu-text">Dosyalaraım</span>
                            </a>
                            <ul class="submenu" style=""> </ul>
                        </li>
                        <li class="menu-separator">
                            <hr>
                        </li>
                        <li class="has-submenu">
                            <a href="testcases2.php">
                                <i class="menu-icon zmdi zmdi-file-text zmdi-hc-lg"></i>
                                <span class="menu-text">Test Case Dosyaları</span>
                            </a>
                            <ul class="submenu" style=""> </ul>
                        </li>
                    </ul><!-- .app-menu -->
                </div>
                <div class="slimScrollBar"
                    style="background: rgb(152, 166, 173); width: 5px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 566px; visibility: visible;">
                </div>
                <div class="slimScrollRail"
                    style="width: 5px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;">
                </div>
            </div><!-- .menubar-scroll-inner -->
        </div><!-- .menubar-scroll -->
    </aside>
    <!--========== END app aside --> <!-- APP MAIN ==========-->
    <main id="app-main" class="app-main in">
        <div class="wrap">
            <section class="app-content">
                <div class="row">
                    <div class="col-md-12">
                            <div class="widget">
                                <div class="widget-header">
                                    <h3 class="widget-title fz-lg text-primary m-b-lg">Dosya Yükle</h3>
                                </div>
                                <div class="widget-body">
                                    <form method="POST" action="mydocs2.php" enctype="multipart/form-data">
                                        <input type="text" name="docname"
                                            class="form-control form-control-lg bg-light fs-6" placeholder="Dosya Adı"
                                            required><br>
                                        <input type="text" name="docdesp"
                                            class="form-control form-control-lg bg-light fs-6 w-100"
                                            placeholder="Dosya Açıklaması" required><br>
                                        <input type="file" class="form-control input" name="fileToUpload"
                                            id="fileToUpload"  accept=".docx,.pdf,.zip,.rar,.7z,.xlsx" required><br>
                                        <button class="btn btn-large btn-block btn-info" type="submit">Dosya Yükle</button>
                                    </form>
                                </div>
                            </div>
                    </div>
                </div><!-- .row -->
                <div class="row">
                    <div class="col no-gutter p-lg">
                        <div class="widget">
                            <header class="widget-header">
                                <h4 class="widget-title">Kullanıcılar</h4>
                            </header>
                            <hr class="widget-separator">
                            <div class="widget-body">
                                <div class="table-responsive">
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
                                                    echo "<td><a style='margin: 5px;' class='btn rounded mw-md btn-success'" . $row2["link"] . "'> Dosyayı İndir </a>";
                                                    echo "<a style='margin: 5px;' class='btn rounded mw-md btn-success' href='" . $row2["link"] . "' onclick='copyLink(event, \"" . $site_domain . $row2["link"] . "\")'> Dosyayı Kopyala </a></td>";

                                                    echo "<td>" . $row2["owner"] . "</td>";
                                                    echo "<td>";
                                                    // Sadece admin veya dosyayı yükleyen kişi için silme ve düzenleme butonları
                                                    if ($isAdmin || $_SESSION['username'] == $row2["owner"]) {
                                                        echo "<form method='POST' action='mydocs2.php'>";
                                                        echo "<input type='hidden' name='doc_id' value='" . $row2["id"] . "'>";
                                                        echo "<input class='btn rounded mw-md btn-success' type='submit' name='delete' value='Sil' onclick='return confirm(\"Bu dosyayı silmek istediğinizden emin misiniz?\")'>";
                                                        echo "</form>";
                                                        // Düzenleme formu
                                                        echo "<div class='dropdown'>";
                                                        echo "<button style='margin-bottom: 5px;' onclick='toggleMenu(\"menu_" . $row2["id"] . "\")' class='btn rounded mw-md btn-success'>Düzenle</button>";
                                                        echo "<div style='display: none;' id='menu_" . $row2["id"] . "' class='dropdown-content'>";
                                                        echo "<form method='POST' action='mydocs2.php'>";
                                                        echo "<input type='hidden' name='doc_id' value='" . $row2["id"] . "'>";
                                                        echo "<input class='form-control' type='text' name='new_doc_name' placeholder='Yeni Dosya Adı'>";
                                                        echo "<input class='form-control' type='text' name='new_doc_desp' placeholder='Yeni Dosya Açıklaması'>";
                                                        echo "<input class='btn rounded mw-md btn-success' type='submit' name='update' value='Güncelle'>";
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
                        </div><!-- .widget -->
                    </div><!-- END column -->
                </div><!-- .row --> <!-- .row --> <!-- .row -->
            </section><!-- #dash-content -->
        </div><!-- .wrap -->
        <!-- APP FOOTER -->
        <?php if ($_SESSION['admin'] == true || $_SESSION['admin'] == 1): ?>


        <?php endif; ?>
        <div class="wrap p-t-0">
            <footer class="app-footer">
                <div class="clearfix">
                    <div class="copyright pull-left">Copyright Omix 2021 ©</div>
                </div>
            </footer>
        </div>
        <!-- /#app-footer -->
    </main>
    <!--========== END app main --> <!-- APP CUSTOMIZER -->
    <!-- #app-customizer --> <!-- build:js ./assets/js/core.min.js -->
    <script src="./libs/bower/jquery/dist/jquery.js"></script>
    <script src="./libs/bower/jquery-ui/jquery-ui.min.js"></script>
    <script src="./libs/bower/jQuery-Storage-API/jquery.storageapi.min.js"></script>
    <script src="./libs/bower/bootstrap-sass/assets/javascripts/bootstrap.js"></script>
    <script src="./libs/bower/jquery-slimscroll/jquery.slimscroll.js"></script>
    <script src="./libs/bower/perfect-scrollbar/js/perfect-scrollbar.jquery.js"></script>
    <script src="./libs/bower/PACE/pace.min.js"></script>
    <!-- endbuild --> <!-- build:js ./assets/js/app.min.js -->
    <script src="./assets/js/library.js"></script>
    <script src="./assets/js/plugins.js"></script>
    <script src="./assets/js/app.js"></script>
    <script src="../libs/misc/flot/jquery.flot.min.js"></script>
    <script src="../libs/misc/flot/jquery.flot.min.js"></script>
    <script src="../libs/bower/waypoints/lib/jquery.waypoints.min.js"></script>
    <!-- endbuild -->
    <script src="./libs/bower/moment/moment.js"></script>
    <script src="./libs/bower/fullcalendar/dist/fullcalendar.min.js"></script>
    <script src="./assets/js/fullcalendar.js"></script>
    <script>
        function toggleForm() {
            var form = document.getElementById("addTestCaseForm");
            if (form.style.display === "block") {
                form.style.display = "none";
            } else {
                form.style.display = "block";
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