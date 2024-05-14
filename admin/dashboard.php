<?php
    require_once '../includes/config.php';
require_once '../includes/db.php';

function logout()
  {
    // Hapus semua data sesi
    $_SESSION = array();

    // Hapus cookie sesi jika ada
    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
      );
    }

    // Hancurkan sesi
    session_destroy();

    // Redirect ke halaman login atau halaman lain yang sesuai
    header("Location:  index.php");
    exit();
  }

  // Periksa apakah tombol logout ditekan
  if (isset($_POST['logout'])) {
    logout();
  }
      
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Sistem Penjadwalan Bapedda" />
    <meta name="author" content="" />

    <title>Bapedda - Sistem Penjadwalan</title>

    <!-- Custom fonts for this template -->
    <link href="<?= FONTAWE_PATH; ?>css/all.min.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<?= CSS_PATH; ?>sb-admin-2.min.css" rel="stylesheet">

    <!-- Additional CSS for aesthetic enhancements -->
    <style>
    .welcome-message {
        background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url('../assets/images/bg.jpeg') no-repeat center center;
        background-size: cover;
        height: 70vh;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .welcome-message h1 {
        font-size: 2.5rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.8);
        font-weight: 800;
        /* Increased weight for more emphasis */
    }

    body {
        overflow-x: hidden;
        /* Prevents horizontal scroll */
    }

    .bg-gradient-primary {
        background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
    }
    </style>
</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
                <div class="sidebar-brand-text mx-3">Bapedda</div>
            </a>
            <!-- Navigation Items -->
            <hr class="sidebar-divider my-0" />

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-chart-column"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider" />

            <!-- Heading -->
            <div class="sidebar-heading">Bappeda</div>

            <!-- Nav Item - Data -->
            <li class="nav-item">
                <a class="nav-link" href="data.php">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Data</span>
                </a>
            </li>

            <!-- Nav Item - Progress -->
            <li class="nav-item">
                <a class="nav-link" href="progress.php">
                    <i class="fas fa-database"></i>
                    <span>Progress</span>
                </a>
            </li>

            <!-- Nav Item - Schedule -->
            <li class="nav-item">
                <a class="nav-link" href="jadwal.php">
                    <i class="fas fa-users"></i>
                    <span>Jadwal</span>
                </a>
            </li>

            <li class="sidebar-item">
                <form method="post" action="">
                    <input class='btn btn-danger' style="margin-left: 20px;" type="submit" name="logout" value="Logout">
                </form>
            </li>
        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                </nav>

                <!-- Page Content -->
                <div class="container-fluid">
                    <div class="welcome-message">
                        <h1>Welcome to Sistem Penjadwalan Bapedda</h1>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>


    <!-- Bootstrap core JavaScript-->
    <script src="<?= JQUERY_PATH; ?>jquery.min.js"></script>
    <script src="<?= BOOTSTRAP_PATH; ?>js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?= JQUERYE_PATH; ?>jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?= JS_PATH; ?>sb-admin-2.min.js"></script>
</body>

</html>