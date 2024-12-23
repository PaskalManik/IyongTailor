<?php
session_start();

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    echo "<script>alert('Unauthorized access! Please log in as admin.');</script>";
    header('Location: ../login.php');
    exit();
}

include './inc/header.php';
include './inc/sidebar.php';
include './inc/db_connect.php';
?>

<div id="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumbs -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb arr-right bg-light">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Reports</li>
            </ol>
        </nav>

        <!-- Page Content -->
        <section>
            <h3 class="text-orange text-center py-1">Reports</h3>
            <div class="order px-3 my-4 custom-card">
                <div class="row my-5 mx-5">
                    <table class="table">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Report ID</th>
                                <th scope="col">Order</th>
                                <th scope="col">User ID</th>
                                <th scope="col">Subject</th>
                                <th scope="col">Message</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT * FROM Reports";
                            $result = mysqli_query($conn, $query);

                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $row['report_id']; ?></td>
                                        <td>
                                            <?php if (!empty($row['order_id'])): ?>
                                                <a href="order-details.php?id=<?php echo $row['order_id']; ?>">
                                                    <i class="fa text-dark fa-eye"></i> View
                                                </a>
                                            <?php else: ?>
                                                <span class="text-danger">Order ID missing</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $row['user_id']; ?></td>
                                        <td><?php echo $row['subject']; ?></td>
                                        <td><?php echo $row['message']; ?></td>
                                        <td><?php echo $row['status']; ?></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No reports found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<?php include './inc/footer.php'; ?>
