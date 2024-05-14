<?php
session_start();

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

class ScheduleGA {
    private $population;
    private $populationSize = 50;
    private $generations = 100;
    private $fitnessScores = [];
    private $mutationRate = 0.1;
    private $crossoverRate = 0.9;
    private $eliteSize = 5; 
    private $randomSeed; 

    public function __construct($data, $seed = null) {
        $this->populationSize = max(10, count($data));
        $this->randomSeed = $seed;
        if ($this->randomSeed !== null) {
            mt_srand($this->randomSeed);  // Seed the random number generator
        }
        $this->initializePopulation($data);
    }

    private function initializePopulation($data) {
        $this->population = [];
        for ($i = 0; $i < $this->populationSize; $i++) {
            $shuffledData = $data;
            foreach ($shuffledData as &$meeting) {
                $meeting['waktu'] = $this->generateRandomTime($meeting['tanggalinput'], $meeting['deadline']);
            }
            $this->population[] = $shuffledData;
        }
    }

    private function generateRandomTime($start, $end) {
        $startDate = strtotime($start . ' 08:00:00');
        $endDate = strtotime($end . ' 17:00:00');
        $randomTimestamp = mt_rand($startDate, $endDate);
        return $this->roundToNearestHalfHour($randomTimestamp);
    }

    public function run() {
        for ($gen = 0; $gen < $this->generations; $gen++) {
            $this->calculateFitness();
            $this->selection();
            $this->crossover();
            $this->mutation();
        }
        return $this->bestSchedule();
    }

    private function selection() {
        array_multisort($this->fitnessScores, SORT_DESC, $this->population);
        $this->population = array_slice($this->population, 0, $this->eliteSize);
    }

    private function calculateFitness() {
        $this->fitnessScores = array_map(function($chromosome) {
            $score = 0;
            foreach ($chromosome as $meeting) {
                $time = strtotime($meeting['waktu']);
                $start = strtotime($meeting['tanggalinput']);
                $end = strtotime($meeting['deadline']);
                if ($time >= $start && $time <= $end) {
                    $score += 50; 
                } else {
                    $score -= 100;
                }
            }
            return $score;
        }, $this->population);
    }

    private function crossover() {
        $newPopulation = [];
        while (count($newPopulation) < $this->populationSize) {
            $parent1 = $this->population[array_rand($this->population)];
            $parent2 = $this->population[array_rand($this->population)];
            $child1 = $parent1;
            $child2 = $parent2;
            $cutPoint = mt_rand(1, count($parent1) - 1);
            for ($i = $cutPoint; $i < count($parent1); $i++) {
                $temp = $child1[$i]['waktu'];
                $child1[$i]['waktu'] = $child2[$i]['waktu'];
                $child2[$i]['waktu'] = $temp;
            }
            $newPopulation[] = $child1;
            $newPopulation[] = $child2;
        }
        $this->population = $newPopulation;
    }

    private function mutation() {
        foreach ($this->population as &$individual) {
            if (mt_rand() / mt_getrandmax() < $this->mutationRate) {
                $mutateIndex = mt_rand(0, count($individual) - 1);
                $individual[$mutateIndex]['waktu'] = $this->generateRandomTime($individual[$mutateIndex]['tanggalinput'], $individual[$mutateIndex]['deadline']);
            }
        }
    }
    private function roundToNearestHalfHour($timestamp) {
        $minute = date('i', $timestamp);
        if ($minute < 30) {
            $minute = '00';
        } else {
            $minute = '30';
        }

        $time = date('Y-m-d H:' . $minute . ':00', $timestamp);
        $hour = date('H', strtotime($time));
        if ($hour < 8) {
            return date('Y-m-d 08:' . $minute . ':00', $timestamp);
        } elseif ($hour >= 17) {
            return date('Y-m-d 17:' . $minute . ':00', $timestamp - 3600); // Adjust to last valid half-hour
        }
        return $time;
    }

    private function bestSchedule() {
        $bestIndex = array_keys($this->fitnessScores, max($this->fitnessScores));
        return $this->population[$bestIndex[0]];
    }
}


function fetchDataFromDatabase($conn) {
    $sql = "SELECT nama, kabkota, tempat, waktu, deadline, tanggalinput FROM bapedda";
    $result = $conn->query($sql);
    $meetingData = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $meetingData[] = $row;
        }
    }
    return $meetingData;
}

$meetingData = fetchDataFromDatabase($conn);
$data_hash = md5(json_encode($meetingData));

if (!isset($_SESSION['data_hash']) || $_SESSION['data_hash'] !== $data_hash || !isset($_SESSION['schedule'])) {
    $ga = new ScheduleGA($meetingData);
    $schedule = $ga->run();
    $_SESSION['schedule'] = $schedule;
    $_SESSION['data_hash'] = $data_hash;
} else {
    $fixedSeed = 123456;  // Example fixed seed
    $ga = new ScheduleGA($meetingData, $fixedSeed);
    $schedule = $_SESSION['schedule'];
}

$ga = new ScheduleGA($meetingData);
$schedule = $ga->run();

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
            <li class="nav-item ">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-chart-column"></i>
                    <span>Dashboard</span></a>
            </li>


            <!-- Divider -->
            <hr class="sidebar-divider" />

            <!-- Heading -->
            <div class="sidebar-heading">Bappeda</div>

            <!-- Nav Item - Input Collapse Menu -->
            <li class="nav-item">
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
            <li class="nav-item active">
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
                    <h1 class="h3 mb-2 text-gray-800">Jadwal Pertemuan</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                Jadwal Pertemuan
                            </h6>
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
                                            <th>Tanggal penginputan:</th>
                                            <th>Waktu:</th>
                                            <th>Deadline:</th>
                                            <!-- <th>File:</th> -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($schedule as $index => $meeting): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td><?= htmlspecialchars($meeting['nama']) ?></td>
                                            <td><?= htmlspecialchars($meeting['kabkota']) ?></td>
                                            <td><?= htmlspecialchars($meeting['tempat']) ?></td>
                                            <td><?= htmlspecialchars($meeting['tanggalinput']) ?></td>
                                            <td><?= htmlspecialchars($meeting['waktu']) ?></td>
                                            <td><?= htmlspecialchars($meeting['deadline']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
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