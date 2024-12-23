<?php
ob_start();
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    // Jika tidak login atau bukan admin, arahkan ke login
    echo "<script>alert('Unauthorized access! Please log in as admin.');</script>";
    header('Location: ../login.php'); // Arahkan ke halaman login utama
    exit();
}

include './inc/db_connect.php'; // Koneksi ke database
include './inc/header.php'; // Include Header
include './inc/sidebar.php'; // Include Sidebar

// Fungsi untuk mengubah role pengguna
if (isset($_POST['change_role'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['new_role'];
    $update_query = "UPDATE users SET role = '$new_role' WHERE user_id = $user_id";
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Role pengguna berhasil diperbarui!');</script>";
    } else {
        echo "<script>alert('Gagal memperbarui role pengguna!');</script>";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Ambil data pengguna
$user_query = "SELECT * FROM users ORDER BY user_id ASC";
$result = mysqli_query($conn, $user_query);
?>

<!-- Content -->
<div id="content-wrapper">
    <div class="container-fluid">
        <!-- Breadcrumbs-->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb arr-right bg-light">
                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Users</li>
            </ol>
        </nav>

        <!-- Page Content -->
        <section>
            <h3 class="text-orange text-center py-1">All Users</h3>

            <div class="order px-3 my-3 custom-card">
                <div class="row my-2 mx-5">
                    <table class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">User ID</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Email</th>
                                <th scope="col">Role</th>
                                <th scope="col">Aksi</th>
                                <th scope="col">View user details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?php echo $row['user_id']; ?></td>
                                    <td><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo ucfirst($row['role']); ?></td>
                                    <td>
                                        <form action="" method="POST" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                            <?php if ($row['role'] == 'customer') { ?>
                                                <input type="hidden" name="new_role" value="employee">
                                                <button type="submit" name="change_role" class="btn btn-primary btn-sm">Promote</button>
                                            <?php } elseif ($row['role'] == 'employee') { ?>
                                                <input type="hidden" name="new_role" value="customer">
                                                <button type="submit" name="change_role" class="btn btn-warning btn-sm">Demote</button>
                                            <?php } ?>
                                        </form>
                                    </td>
                                    <td>
                                        <button class="btn custom-btn" data-toggle="modal" data-target="#userInfoModal<?php echo $row['user_id']; ?>"> <i class="fa fa-eye"></i> View</button>
                                    </td>
                                </tr>

                                <!-- Modal -->
                                <div class="modal fade" id="userInfoModal<?php echo $row['user_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Info <?php echo $row['firstname']; ?></h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p>User ID: <?php echo $row['user_id']; ?></p>
                                                <p>Firstname: <?php echo $row['firstname']; ?></p>
                                                <p>Lastname: <?php echo $row['lastname']; ?></p>
                                                <p>Email: <?php echo $row['email']; ?></p>
                                                <p>Contact No.: <?php echo $row['contact_no']; ?></p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal"> <i class="fa fa-close"></i> Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<?php
include './inc/footer.php'; // Include Footer
?>
