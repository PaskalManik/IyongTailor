<?php

session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    // Jika tidak login atau bukan admin, arahkan ke login
    echo "<script>alert('Unauthorized access! Please log in as admin.');</script>";
    header('Location: ../login.php'); // Arahkan ke halaman login utama
    exit();
}

include './inc/header.php';
include './inc/sidebar.php';
include './inc/db_connect.php';

?>

<!-- Main Content -->
<div id="content-wrapper">
    <div class="container-fluid">

        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb arr-right bg-light">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">View Logs</li>
            </ol>
        </nav>

        <!-- Page Content -->
        <section>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 custom-card my-4 p-5">
                        <h3 class="text-orange text-center py-1">Activity Logs</h3>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Action Type</th>
                                        <th>Table Name</th>
                                        <th>Affected Data</th>
                                        <th>Nilai Lama</th>
                                        <th>Nilai Baru</th>
                                        <th>User ID</th>
                                        <th>Action_date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT * FROM logs ORDER BY action_date DESC";
                                    $result = mysqli_query($conn, $sql);

                                    if (mysqli_num_rows($result) > 0) {
                                        $counter = 1;
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo "<tr>";
                                            echo "<td>" . $counter++ . "</td>";
                                            echo "<td>" . htmlspecialchars($row['action_type']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['table_name']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['affected_data']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nilai_lama']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['nilai_baru']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['action_date']) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='8' class='text-center'>No logs available</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
    <!-- /.container-fluid -->
</div>
<!-- /.content-wrapper -->


<?php
include './inc/footer.php';
?>