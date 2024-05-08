<?php
// Veritabanı bağlantısı
$servername = "localhost"; // Sunucu adı
$username = "root"; // Veritabanı kullanıcı adı
$password = ""; // Veritabanı parolası
$dbname = "mixtest"; // Veritabanı adı

// Bağlantı oluşturma
$conn = new mysqli($servername, $username, $password, $dbname);// Bağlantıyı kontrol etme
if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}// Kullanıcı oturumunu kontrol etme
session_start();// Eğer kullanıcı giriş yapmamışsa login sayfasına yönlendirme
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
if (isset($_POST['delete_id'])) {
    // Silinecek cihazın ID'sini al
    $delete_id = $_POST['delete_id'];    // Kullanıcının bilgilerini al
    $user_info = getUserInfo($delete_id);    // Sadece Takım Lideri ise silme işlemini gerçekleştir
    if ($_SESSION['role'] == 'Takım Lideri' || $_SESSION['role'] == 'CTO' || $_SESSION['$admin'] == true || $_SESSION['admin'] == 1) {
        // Silme işlemini onaylama
        echo '<script>';
        echo 'if(confirm("Dikkat!! Şu anda ' . $user_info['username'] . ', isimli ve ' . $user_info['role'] . ' rütbesindeki kullanıcıyı siliyorsunuz. Bunu yapmak istediğinize emin misiniz?")) {';
        echo 'console.log("Silme işlemi onaylandı.");';
        echo '} else {';
        echo 'console.log("Silme işlemi iptal edildi.");';
        echo '}';
        echo '</script>';        // Silme işlemini gerçekleştirme
        $sql_delete = "DELETE FROM users WHERE id = $delete_id";        // Sorguyu çalıştır ve sonucu kontrol et
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
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;// Bağlantıyı kapatma
$conn->close();
?>

<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Admin, Dashboard, Bootstrap">
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
                        <h5 class="page-title hidden-menubar-top hidden-float">
                            Ana Sayfa
                            <?php if ($_SESSION['admin'] == 1 || $_SESSION['admin'] == true) {
                                echo '- Admin Paneli';
                            } else {
                                echo '';
                            } ?>
                        </h5>
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
                            <a href="myprofile.php">
                                <i class="menu-icon fa fa-user"></i>
                                <span class="menu-text">Profilim</span>
                            </a>
                            <ul class="submenu" style=""> </ul>
                        </li>
                        <li class="has-submenu">
                            <a href="mydocs2.php">
                                <i class="menu-icon zmdi zmdi-search zmdi-hc-lg"></i>
                                <span class="menu-text">Dosyalaraım</span>
                            </a>
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
                        <div class="widget row no-gutter p-lg">
                            <div class="col-md-5 col-sm-5">
                                <div>
                                    <h3 class="widget-title fz-lg text-primary m-b-lg">Sales in 2014</h3>
                                    <p class="m-b-lg">Collaboratively administrate empowered markets via plug-and-play
                                        networks. Dynamically procrastinate B2C users after installed base benefits</p>
                                    <p class="fs-italic">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Hic
                                        eum alias est vitae, obcaecati?</p>
                                </div>
                            </div>
                            <div class="col-md-7 col-sm-7">
                                <div>
                                    <div id="lineChart" data-plugin="plot" data-options="
                                [
                                    {
                                        data: [[1,3.6],[2,3.5],[3,6],[4,4],[5,4.3],[6,3.5],[7,3.6]],
                                        color: '#ffa000',
                                        lines: { show: true, lineWidth: 6 },
                                        curvedLines: { apply: true }
                                    },
                                    {
                                        data: [[1,3.6],[2,3.5],[3,6],[4,4],[5,4.3],[6,3.5],[7,3.6]],
                                        color: '#ffa000',
                                        points: {show: true}
                                    }
                                ],
                                {
                                    series: {
                                        curvedLines: { active: true }
                                    },
                                    xaxis: {
                                        show: true,
                                        font: { size: 12, lineHeight: 10, style: 'normal', weight: '100',family: 'lato', variant: 'small-caps', color: '#a2a0a0' }
                                    },
                                    yaxis: {
                                        show: true,
                                        font: { size: 12, lineHeight: 10, style: 'normal', weight: '100',family: 'lato', variant: 'small-caps', color: '#a2a0a0' }
                                    },
                                    grid: { color: '#a2a0a0', hoverable: true, margin: 8, labelMargin: 8, borderWidth: 0, backgroundColor: '#fff' },
                                    tooltip: true,
                                    tooltipOpts: { content: 'X: %x.0, Y: %y.2',  defaultTheme: false, shifts: { x: 0, y: -40 } },
                                    legend: { show: false }
                                }" style="width: 100%; height: 230px;">
                                    </div>
                                </div>
                            </div>
                        </div><!-- .widget -->
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
                                            if ($result1->num_rows > 0) {
                                                while ($row = $result1->fetch_assoc()) {
                                                    ?>
                                                    <tr>
                                                        <td><a class="text-success"
                                                                href="mailto:<?php echo $row['email']; ?>"><?php echo $row['email']; ?></a>
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
                                                                    echo '<button type="button" class="btn mw-md btn-danger" style="margin: 3px;" onclick="confirmDelete(' . $row['id'] . ')">Sil</button>';
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
                        </div><!-- .widget -->
                    </div><!-- END column -->
                </div><!-- .row --> <!-- .row --> <!-- .row -->
            </section><!-- #dash-content -->
        </div><!-- .wrap -->
        <!-- APP FOOTER -->
        <?php if ($_SESSION['admin'] == true || $_SESSION['admin'] == 1): ?>

            <div class="col no-gutter p-lg">
                <div class="widget">
                    <div class="widget-header">
                        <h2 style="color: #74767d;">Omix Çalışan Kayıt Alanı</h2>
                        <p>Omix Test Platformuna ODM Friması Kaydı için aşağıdaki bilgileri doldurun.</p>
                    </div>
                    <div class="widget-body">

                        <form method="POST" action="register.php">
                            <div class="input-group mb-3" style="width: -webkit-fill-available; margin: 5px; margin: 5px;">
                                <input type="mail" name="email" class="form-control form-control-lg bg-light fs-6 w-100 p-3"
                                    placeholder="Email adresi" required="">
                            </div>
                            <div class="input-group mb-3" style="width: -webkit-fill-available; margin: 5px;">
                                <input type="text" name="username" class="form-control form-control-lg bg-light fs-6"
                                    placeholder="Kullanıcı adı" required="">
                            </div>
                            <div class="input-group mb-3" style="width: -webkit-fill-available; margin: 5px;">
                                <input type="text" name="role" class="form-control form-control-lg bg-light fs-6"
                                    placeholder="Mevki" required="">
                            </div>
                            <div class="input-group mb-1" style="width: -webkit-fill-available; margin: 5px;">
                                <input type="password" name="password" class="form-control form-control-lg bg-light fs-6"
                                    placeholder="Şifre" required="">
                            </div>
                            <div class="input-group mb-5 d-flex justify-content-between"
                                style="width: -webkit-fill-available; margin: 5px;">
                                <div class="forgot">
                                </div>
                            </div>
                            <div class="input-group mb-3" style="width: -webkit-fill-available; margin: 5px;">
                                <button type="submit" class="btn btn-lg btn-primary w-100 fs-6">Kayıt Ol</button>
                            </div>
                        </form><br>
                    </div>
                </div>
            </div>
            <div class="col no-gutter p-lg">
                <div class="widget">
                    <div class="widget-header">
                        <h2 style="color: #74767d;">Omix Çalışan Kayıt Alanı</h2>
                        <p>Omix Test Platformuna ODM Friması Kaydı için aşağıdaki bilgileri doldurun.</p>
                    </div>
                    <div class="widget-body">
                        <form method="POST" action="odmregister.php">
                            <div class="input-group mb-3" style="width: -webkit-fill-available; margin: 5px; margin: 5px;">
                                <input type="mail" name="email" class="form-control form-control-lg bg-light fs-6"
                                    placeholder="Email adresi" required>
                            </div>
                            <div class="input-group mb-3" style="width: -webkit-fill-available; margin: 5px; margin: 5px;">
                                <input type="text" name="company" class="form-control form-control-lg bg-light fs-6"
                                    placeholder="ODM Friması " required>
                            </div>
                            <div class="input-group mb-3" style="width: -webkit-fill-available; margin: 5px; margin: 5px;">
                                <input type="text" name="username" class="form-control form-control-lg bg-light fs-6"
                                    placeholder="Kullanıcı adı" required>
                            </div>
                            <div class="input-group mb-3" style="width: -webkit-fill-available; margin: 5px; margin: 5px;">
                                <input type="text" name="role" class="form-control form-control-lg bg-light fs-6"
                                    placeholder="Mevki" required>
                            </div>
                            <div class="input-group mb-1" style="width: -webkit-fill-available; margin: 5px; margin: 5px;">
                                <input type="password" name="password" class="form-control form-control-lg bg-light fs-6"
                                    placeholder="Şifre" required>
                            </div>
                            <div class="input-group mb-5 d-flex justify-content-between"
                                style="width: -webkit-fill-available; margin: 5px; margin: 5px;">
                                <div class="forgot">
                                </div>
                            </div>
                            <div class="input-group mb-3" style="width: -webkit-fill-available; margin: 5px; margin: 5px;">
                                <button type="submit" class="btn btn-lg btn-primary w-100 fs-6">Kayıt Ol</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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