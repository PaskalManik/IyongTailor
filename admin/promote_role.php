<?php
ob_start();
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
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Ambil data pengguna
$user_query = "SELECT * FROM users ORDER BY user_id ASC";
$result = mysqli_query($conn, $user_query);
?>

<!-- Content -->
<div class="container my-4">
    <h1 class="mb-4">Kelola Pengguna</h1>

    <!-- Tabel Pengguna -->
    <h2>Daftar Pengguna</h2>
    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID Pengguna</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Role</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['user_id']; ?></td>
                    <td><?php echo $row['firstname'].' '.$row['lastname']; ?></td>
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
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
include './inc/footer.php'; // Include Footer
?>
