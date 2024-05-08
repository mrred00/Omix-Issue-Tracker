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

if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

if (isset($_POST['logout'])) {
    // Tüm oturum değişkenlerini temizleme
    session_unset();

    // Oturumu sonlandırma
    session_destroy();

    // Kullanıcıyı login sayfasına yönlendirme
    header("Location: login.php");
    exit;

}


if (isset($_POST['delete'])) {
    $delete_id = $_POST['delete_id'];
    $engineer = $_SESSION['username'];
    $role = $_SESSION['role'];

    // Sadece oturum açmış kullanıcılar ve kendi ekledikleri öğeleri silebilir veya takım lideri rolüne sahip olanlar silme işlemi yapabilir
    $sql_check = "SELECT * FROM test_cases WHERE id = $delete_id AND (engineer = '$engineer' OR '$role' = 'Takım Lideri')";
    $result_check = $conn->query($sql_check);
    if ($result_check->num_rows > 0 || $_SESSION['admin'] == 1 || $_SESSION['admin'] == true) {
        $sql_delete = "DELETE FROM test_cases WHERE id = $delete_id";
        if ($conn->query($sql_delete) === TRUE) {
            // Silme işlemi başarılı olduysa
            echo "Kayıt başarıyla silindi.";
            header("Location: testcases2.php");
        } else {
            // Silme işlemi sırasında bir hata oluştuysa
            echo "Error deleting record: " . $conn->error;
        }
    } else {
        // Yetkisiz kullanıcılar için hata mesajı
        echo "Bu işlemi gerçekleştirmek için yetkiniz yok.";
    }
}

$sql1 = "SELECT * FROM test_cases";
$result1 = $conn->query($sql1);

// Bağlantıyı kapatma
$conn->close();
?>


<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Omix Test System">
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
                        <h5 class="page-title hidden-menubar-top hidden-float">Güncel Test Case Dosyaları</h5>
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
                        <?php if ($_SESSION['company'] == 'Omix'): ?>
                            <li class="has-submenu">
                                <a href="mydocs2.php">
                                    <i class="menu-icon glyphicon glyphicon-file zmdi-hc-lg"></i>
                                    <span class="menu-text">Dosyalarım</span>
                                </a>
                                <ul class="submenu" style=""> </ul>
                            </li>
                        <?php elseif($_SESSION['company'] == 'Omix' || $_SESSION['company'] == 'Mobiltel' || $_SESSION['company'] == 'Blink' || $_SESSION['company'] == 'Mobiltel'): ?>
                            <li class="has-submenu">
                                <a href="inovasyon2.php">
                                    <i class="menu-icon zmdi zmdi-search zmdi-hc-lg"></i>
                                    <span class="menu-text">İnovasyon</span>
                                </a>
                                <ul class="submenu" style=""> </ul>
                            </li>
                        <?php endif; ?>
                        <li class="has-submenu">
                                <a href="inovasyon2.php">
                                    <i class="menu-icon zmdi zmdi-search zmdi-hc-lg"></i>
                                    <span class="menu-text">Ana Sayfa</span>
                                </a>
                                <ul class="submenu" style=""> </ul>
                            </li>
                        <li class="menu-separator">
                            <hr>
                        </li>
                        <li class="has-submenu">
                            <a href="testcases2.php">
                                <i class="menu-icon zmdi zmdi-file-text zmdi-hc-lg"></i>
                                <span class="menu-text">Test Case</span>
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

                    </div>
                </div><!-- .row -->
                <div class="row">
                    <div class="col no-gutter p-lg">
                        <div class="widget">
                            <div class="widget-header">
                                <h2 style="color: #74767d;">Test Case Ekle</h2>
                                <p>Omix Test Süreçlerinde kullanılmasını istediğiniz test case dosyalarını sisteme
                                    yükleyiniz.</p>
                            </div>
                            <div class="widget-body">
                                <div id="addTestCaseForm" style="padding:10px">
                                    <!-- Dosya yükleme işlemi için form etiketinde enctype="multipart/form-data" özelliğini ekleyin -->
                                    <form method="POST" action="add_testcase.php" enctype="multipart/form-data">
                                        <input ty pe="text" name="test_name"
                                            class="form-control form-control-lg bg-light fs-6 w-100 p-3"
                                            placeholder="Test Adı" required><br>
                                        <input type="text" name="despcription"
                                            class="form-control form-control-lg bg-light fs-6 w-100 p-3"
                                            placeholder="Test Açıklaması" required><br>
                                        <input type="text" name="importance"
                                            class="form-control form-control-lg bg-light fs-6 w-100 p-3"
                                            placeholder="Test Önem Düzeyi" required><br>
                                        <!-- Dosya yükleme alanını düzenle, name özelliğini uploaded_file olarak belirt -->
                                        <input type="file" name="file"
                                            class="form-control form-control-lg bg-light fs-6 w-100 p-3"
                                            placeholder="Dosya" accept=".docx,.pdf,.xlsx" required><br>
                                        <button class="btn mw-md btn-success"
                                            style="margin: 3px; width: -webkit-fill-available;" type="submit">Test
                                            Case Ekle</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                                <th> Test Adı</th>
                                                <th> Test Önem Düzeyi</th>
                                                <th> Test Case'i oluşturan Mühendis</th>
                                                <th> Test Case Oluşturulma Tarihi</th>
                                                <th> İndirme Bağlantısı</th>
                                                <th> İşlemler</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Test case'lerini tabloya ekleme
                                            if ($result1->num_rows > 0) {
                                                while ($row = $result1->fetch_assoc()) {
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <div class="client">
                                                                <div class="client-info">
                                                                    <h3><?php echo $row['test_name']; ?></h3>
                                                                    <small><?php echo $row['despcription']; ?></small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><?php echo $row['importance']; ?></td>
                                                        <td><?php echo $row['engineer']; ?></td>
                                                        <td><?php echo $row['creation_date']; ?></td>
                                                        <td>
                                                            <a style="text-success"
                                                                href="<?php echo $row['download_link']; ?>">indir</a>
                                                        </td>
                                                        <td>
                                                            <?php if ($_SESSION['username'] == $row['engineer'] || $_SESSION['role'] == 'Takım Lideri' || $_SESSION['admin'] == true): ?>
                                                                <form method="POST">
                                                                    <input type="hidden" name="delete_id"
                                                                        value="<?php echo $row['id']; ?>">
                                                                    <button class="btn mw-md btn-danger" style="margin: 3px;"
                                                                        type="submit" name="delete">Sil</button>
                                                                </form>
                                                            <?php endif; ?>
                                                        </td>

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
                        </div><!-- .widget -->
                    </div><!-- END column -->
                </div><!-- .row --> <!-- .row --> <!-- .row -->
            </section><!-- #dash-content -->
        </div><!-- .wrap -->
        <!-- APP FOOTER -->
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