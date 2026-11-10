
<?php
require_once "../includes/auth.php";
require_once "../config/database.php";

$query = mysqli_query($conn, "
    SELECT 
        p.*,
        COUNT(pi.id) AS total_gambar
    FROM projects p
    LEFT JOIN project_images pi ON pi.project_id = p.id
    GROUP BY p.id
    ORDER BY p.id DESC
");

$total = mysqli_num_rows($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #fff5f8;
            font-family: Arial, sans-serif;
        }

        .sidebar {
            background: white;
            min-height: 100vh;
            padding: 25px;
            box-shadow: 3px 0 15px rgba(0,0,0,.04);
        }

        .pink {
            color: #d77fa5;
        }

        .btn-pink {
            background: #e99abb;
            color: white;
            border: none;
        }

        .btn-pink:hover {
            background: #d77fa5;
            color: white;
        }

        .content-card {
            background: white;
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(215,127,165,.08);
        }

        .table td {
            vertical-align: middle;
        }
    </style>
</head>

<body>
<div class="container-fluid">
    <div class="row">

        <div class="col-md-3 col-lg-2 sidebar">
            <h4 class="pink fw-bold mb-4">My Portfolio</h4>

            <p class="text-muted">MENU ADMIN</p>

            <a href="dashboard.php" class="d-block text-decoration-none pink mb-3">
                Dashboard
            </a>

            <a href="../index.php" class="d-block text-decoration-none text-dark mb-3">
                Lihat Portfolio
            </a>

            <a href="logout.php" class="d-block text-decoration-none text-danger">
                Logout
            </a>
        </div>

        <div class="col-md-9 col-lg-10 p-4 p-md-5">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold">Dashboard Admin</h3>
                    <p class="text-muted">
                        Selamat datang,
                        <?= htmlspecialchars($_SESSION['admin_username']) ?>!
                    </p>
                </div>

                <a href="logout.php" class="btn btn-outline-danger">
                    Logout
                </a>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] === 'tambah_berhasil'): ?>
                <div class="alert alert-success">Proyek berhasil ditambahkan.</div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] === 'edit_berhasil'): ?>
                <div class="alert alert-success">Proyek berhasil diperbarui.</div>
            <?php elseif (isset($_GET['status']) && $_GET['status'] === 'gambar_berhasil'): ?>
                <div class="alert alert-success">Screenshot berhasil dikelola.</div>
            <?php endif; ?>

            <div class="content-card mb-4">
                <p class="text-muted mb-1">Total Proyek</p>
                <h2 class="pink fw-bold"><?= $total ?></h2>
            </div>

            <div class="content-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold">Daftar Proyek</h5>

                    <a href="tambah.php" class="btn btn-pink">
                        + Tambah Proyek
                    </a>
                </div>

                <?php if ($total > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Proyek</th>
                                    <th>Teknologi</th>
                                    <th>Screenshot</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php $no = 1; ?>
                                <?php while ($project = mysqli_fetch_assoc($query)): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>

                                        <td>
                                            <strong>
                                                <?= htmlspecialchars($project['title']) ?>
                                            </strong>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($project['technology']) ?>
                                        </td>

                                        <td>
                                            <span class="badge bg-secondary">
                                                <?= (int)$project['total_gambar'] ?> gambar
                                            </span>
                                        </td>

                                        <td>
                                            <a
                                                href="edit_proyek.php?id=<?= (int)$project['id'] ?>"
                                                class="btn btn-sm btn-pink"
                                            >
                                                Edit Proyek
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted mb-0">
                        Belum ada proyek. Klik tombol Tambah Proyek untuk memulai.
                    </p>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>
</body>
</html>
