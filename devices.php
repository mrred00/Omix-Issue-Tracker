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

   // Sadece Takım Lideri ise silme işlemini gerçekleştir
   if ($_SESSION['role'] == 'Takım Lideri') {
      // Silme sorgusunu hazırla
      $sql_delete = "DELETE FROM devices WHERE id = $delete_id";

      // Sorguyu çalıştır ve sonucu kontrol et
      if ($conn->query($sql_delete) === TRUE) {
         echo "Cihaz başarıyla silindi.";
         header("Location: ./");
      } else {
         echo "Cihazı silerken bir hata oluştu: " . $conn->error;
      }
   } else {
      echo "Sadece Takım Lideri cihazları silebilir.";
   }
}

$sql1 = "SELECT * FROM devices";
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
                        <a href="./arge-cihazları" class="active">
                            <span class="las la-clipboard-list"></span>
                            <small>Cihazlar</small>
                        </a>
                    </li>
                    <?php if($_SESSION['company'] == 'Omix'): ?>
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
            <small>Cihazlar / Kontrol Paneli</small>
         </div>
         <div class="analytics">
            <div class="card">
               <div class="card-head">
                  <h2>107,200</h2>
                  <span class="las la-user-friends"></span>
               </div>
               <div class="card-progress">
                  <small>User activity this month</small>
                  <div class="card-indicator">
                     <div class="indicator one" style="width: 60%"></div>
                  </div>
               </div>
            </div>
            <div class="card">
               <div class="card-head">
                  <h2>340,230</h2>
                  <span class="las la-eye"></span>
               </div>
               <div class="card-progress">
                  <small>Page views</small>
                  <div class="card-indicator">
                     <div class="indicator two" style="width: 80%"></div>
                  </div>
               </div>
            </div>
            <div class="card">
               <div class="card-head">
                  <h2>$653,200</h2>
                  <span class="las la-shopping-cart"></span>
               </div>
               <div class="card-progress">
                  <small>Monthly revenue growth</small>
                  <div class="card-indicator">
                     <div class="indicator three" style="width: 65%"></div>
                  </div>
               </div>
            </div>
            <div class="card">
               <div class="card-head">
                  <h2>47,500</h2>
                  <span class="las la-envelope"></span>
               </div>
               <div class="card-progress">
                  <small>New E-mails received</small>
                  <div class="card-indicator">
                     <div class="indicator four" style="width: 90%"></div>
                  </div>
               </div>
            </div>
         </div>
         <div class="records table-responsive">
            <div class="record-header">
               <?php
               // Sadece Takım Lideri ise Yeni Cihaz Ekle butonunu göster
               if ($_SESSION['role'] == 'Takım Lideri') {
                  ?>
                  <div class="add">
                     <button onclick="toggleForm()">Yeni Cihaz Ekle</button>
                  </div>
                  <?php
               }
               ?>
            </div>
            <div id="addTestCaseForm" style="display:none; padding:10px">
               <form method="POST" action="add_device.php">
                  <input type="text" name="engineer" class="form-control form-control-lg bg-light fs-6"
                     placeholder="Test Mühendisi" required><br>
                  <input type="text" name="device_name" class="form-control form-control-lg bg-light fs-6"
                     placeholder="Cihaz Adı" required><br>
                  <input type="text" name="startdate" class="form-control form-control-lg bg-light fs-6"
                     placeholder="Proje Başlangıç Tarihi" required><br>
                  <button
                     style="background: var(--main-color);color: #fff;height: 37px;border-radius: 4px;padding: 0rem 1rem;border: none;font-weight: 600;width: -moz-available;width: -webkit-fill-available;"
                     type="submit">Test Case Ekle</button>
               </form>
            </div>
            <div>
               <table width="100%">
                  <thead>
                     <tr>
                        <th>Test Mühendisi</th>
                        <th>Cihaz Adı</th>
                        <th>Proje Başlangıç Tarihi</th>
                        <th>Aktif Hata Sayısı</th>
                        <th>İşlemler</th>
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
                                       <h4><?php echo $row['engineer']; ?></h4>
                                       <small><?php echo $row['despcription']; ?></small>
                                    </div>
                                 </div>
                              </td>
                              <td><?php echo $row['device_name']; ?></td>
                              <td><?php echo $row['startdate']; ?></td>
                              <td><?php echo $row['total_issue']; ?></td>
                              <td> <?php
                              // Sadece Takım Lideri ise silme butonunu göster
                              if ($_SESSION['role'] == 'Takım Lideri') {
                                 echo '<form method="POST">';
                                 echo '<input type="hidden" name="delete_id" value="' . $row['id'] . '">';
                                 echo '<button style="background: var(--main-color);color: #fff;height: 37px; margin-right: 10px;border-radius: 4px;padding: 0rem 1rem;border: none;font-weight: 600;width: -moz-available;width: -webkit-fill-available;" type="submit">Sil</button>';
                                 echo '</form>';
                              }
                              ?>
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
   </div>
   </main>
   </div>
   <script>
      function search() {
         var input, filter, table, tr, td, i, txtValue;
         input = document.getElementById("searchInput");
         filter = input.value.toUpperCase();
         table = document.querySelector(".records table");
         tr = table.getElementsByTagName("tr");

         // Her satırı kontrol et, eşleşenleri göster, eşleşmeyenleri gizle
         for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1]; // Cihaz adı sütunu
            if (td) {
               txtValue = td.textContent || td.innerText;
               if (txtValue.toUpperCase().indexOf(filter) > -1) {
                  tr[i].style.display = "";
               } else {
                  tr[i].style.display = "none";
               }
            }
         }
      }

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
   </script>
</body>

</html>