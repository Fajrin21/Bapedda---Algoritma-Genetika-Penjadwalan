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
      
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Ambil nilai dari formulir
    $nama = $conn->real_escape_string($_POST['nama']);
    $kabkota = $conn->real_escape_string($_POST['kabkota']);
    $tempat = $conn->real_escape_string($_POST['tempat']);
    $waktu = $conn->real_escape_string($_POST['waktu']);
    $deadline = $conn->real_escape_string($_POST['deadline']);
    $tanggalinput = $conn->real_escape_string($_POST['tanggalinput']);

    // Lokasi dan penanganan file yang diupload
    $target_dir = "../assets/data/file/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Periksa apakah file sudah ada
    if (file_exists($target_file)) {
        echo "Maaf, file sudah ada.";
        $uploadOk = 0;
    }

    // Batasi jenis file
    $allowed_types = array("pdf", "doc", "docx", "jpg", "png", "jpeg", "gif");
    if (!in_array($fileType, $allowed_types)) {
        echo "Maaf, hanya file PDF, DOC, DOCX, JPG, JPEG, PNG & GIF yang diperbolehkan.";
        $uploadOk = 0;
    }

    // Coba upload file jika semua pengecekan lolos
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        // SQL untuk memasukkan data ke dalam database
        $sql = "INSERT INTO bapedda (nama, kabkota, tempat, waktu, deadline, tanggalinput, file)
                VALUES ('$nama', '$kabkota', '$tempat', '$waktu', '$deadline', '$tanggalinput', '$target_file')";

        if ($conn->query($sql) === TRUE) {
            header("Location: data.php");
        } else {  
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Maaf, terjadi kesalahan saat mengupload file Anda.";
    }
}

if (isset($_GET['delete']) && isset($_GET['id'])) {
$id = $_GET['id'];

$deleteQuery = "DELETE FROM bapedda WHERE id = $id";

if ($conn->query($deleteQuery) === TRUE) {
header("Location: " . $_SERVER['PHP_SELF']);
exit();
} else {
echo "Error deleting data: " . $conn->error;
}
}

if (isset($_GET['info']) && isset($_GET['id'])) {
$id = $_GET['id'];
$sql = "SELECT * FROM bapedda WHERE id = $id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
$row = $result->fetch_assoc();
echo json_encode($row);
exit();
} else {
echo json_encode(array("error" => "No data found"));
exit();
}
}

$sql = "SELECT * FROM bapedda";
$result = $conn->query($sql);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>SB Admin 2 - Tables</title>

    <!-- Custom fonts for this template -->
    <link href="<?= VENDOR_PATH?>fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css" />
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet" />

    <!-- Custom styles for this template -->
    <link href="<?= CSS_PATH?>sb-admin-2.min.css" rel="stylesheet" />

    <!-- Custom styles for this page -->
    <link href="<?= VENDOR_PATH?>datatables/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1050;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-img {
        width: 200%;
        /* Makes the image responsive */
        height: 200px;
        /* Fixed height */
        object-fit: cover;
        /* Ensures the image covers the area without distorting aspect ratio */
    }

    figure {
        margin: 10px;
        /* Provides some spacing around the image */
    }

    figcaption {
        text-align: center;
        /* Centers the caption text */
        margin-top: 5px;
        /* Spacing between image and caption */
    }

    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        /* Responsive width */
        box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
        animation-name: modalopen;
        animation-duration: 0.3s;
    }

    @keyframes modalopen {
        from {
            opacity: 0
        }

        to {
            opacity: 1
        }
    }

    .close {
        color: #aaaaaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-text mx-3">BAPEDDA</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0" />

            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-chart-column"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider" />

            <!-- Heading -->
            <div class="sidebar-heading">Bappeda</div>

            <!-- Nav Item - Input Collapse Menu -->
            <li class="nav-item active">
                <a class="nav-link" href="data.php">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Data</span>
                </a>
            </li>

            <!-- Nav Item - Data Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link" href="progress.php">
                    <i class="fas fa-database"></i>
                    <span>Progress</span>
                </a>
            </li>

            <!-- Nav Item - Akun Collapse Menu -->
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

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block" />

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2" />
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Data</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                Data
                            </h6>
                            <button type="button" class="btn btn-success float-right" data-toggle="modal"
                                data-target="#inputDataModal">Add New Data</button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>No:</th>
                                            <th>Nama:</th>
                                            <th>Kabupaten/kota:</th>
                                            <th>Tempat:</th>
                                            <th>Waktu:</th>
                                            <th>Deadline:</th>
                                            <th>Tanggal Input:</th>
                                            <th>File:</th>
                                            <th style="padding-right: 50px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                            if ($result->num_rows > 0) {
                            $nomor_urut = 1;

                                while ($row = $result->fetch_assoc()) {
                                $fileDirectory = '../assets/data/file/';
                                $fullFilePath =  $row['file']; 
                                
                                    echo "<td>" . $nomor_urut . "</td>"; 
                                    echo "<td>" . $row["nama"] . "</td>";
                                    echo "<td>" . $row["kabkota"] . "</td>";
                                    echo "<td>" . $row["tempat"] . "</td>";
                                    echo "<td>" . $row["waktu"] . "</td>";
                                    echo "<td>" . $row["deadline"] . "</td>";
                                    echo "<td>" . $row["tanggalinput"] . "</td>";
                                    echo "<td><a href='" . htmlspecialchars($fullFilePath) . "' target='_blank'>" . htmlspecialchars(basename($row["file"])) . "</a></td>";

                                    echo "<td><a href='?delete&id=" . $row["id"] . "' class='btn btn-danger btn-circle btn-sm'><i class='fas fa-trash'></i></a></td>";
                                    echo "</tr>";
                                    $nomor_urut++; 

                                }
                            } else {
                                echo "<tr><td colspan='5'>Tidak ada data yang tersedia</td></tr>";
                            }
                            ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div id="inputDataModal" class="modal fade" tabindex="-1" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Input New Data</h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <form id="inputDataForm" method="post" enctype="multipart/form-data">
                                        <!-- Add your input fields here -->
                                        <div class="form-group">
                                            <label for="nama">Nama:</label>
                                            <input type="text" class="form-control" id="nama" name="nama" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="kabkota">Kabupaten/kota:</label>
                                            <input type="text" class="form-control" id="kabkota" name="kabkota"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="tempat">Tempat:</label>
                                            <input type="text" class="form-control" id="tempat" name="tempat" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="waktu">Waktu:</label>
                                            <input type="time" class="form-control" id="waktu" name="waktu" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="deadline">Deadline:</label>
                                            <input type="date" class="form-control" id="deadline" name="deadline"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="tanggalinput">Tanggal Input:</label>
                                            <input type="date" class="form-control" id="tanggalinput"
                                                name="tanggalinput" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="filetoupload">File:</label>
                                            <input type="file" class="form-control" id="fileToUpload"
                                                name="fileToUpload" required>
                                        </div>
                                        <!-- Add more fields as needed -->
                                        <button type="submit" value="submit" class="btn btn-primary"> Submit </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <a class="scroll-to-top rounded" href="#page-top">
                <i class="fas fa-angle-up"></i>
            </a>

            <!-- <script src="../assets/js/info.js"></script> -->

            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

            <script src="<?= VENDOR_PATH;?>bootstrap/js/bootstrap.bundle.min.js"></script>
            <script src="<?= VENDOR_PATH;?>jquery-easing/jquery.easing.min.js"></script>
            <script src="<?= JS_PATH?>sb-admin-2.min.js"></script>
            <script src="<?= VENDOR_PATH;?>datatables/jquery.dataTables.min.js"></script>
            <script src="<?= VENDOR_PATH;?>datatables/dataTables.bootstrap4.min.js"></script>
            <script src="<?= JS_PATH?>demo/datatables-demo.js"></script>
</body>

</html>