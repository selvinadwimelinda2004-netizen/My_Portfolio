
<?php
require_once "../includes/auth.php";
require_once "../config/database.php";

$error = "";
$uploadDir = "../assets/img/projects/";

// Validasi ID proyek
$projectId = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$projectId || $projectId < 1) {
    header("Location: dashboard.php");
    exit;
}

// Ambil data proyek
$stmt = mysqli_prepare($conn, "SELECT * FROM projects WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $projectId);
mysqli_stmt_execute($stmt);
$project = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$project) {
    header("Location: dashboard.php");
    exit;
}

// ===================================
// PROSES HAPUS SATU SCREENSHOT
// ===================================
if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    ($_POST["action"] ?? "") === "delete_image"
) {
    $imageId = filter_input(INPUT_POST, "image_id", FILTER_VALIDATE_INT);

    if ($imageId && $imageId > 0) {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, image FROM project_images
             WHERE id = ? AND project_id = ?"
        );

        mysqli_stmt_bind_param($stmt, "ii", $imageId, $projectId);
        mysqli_stmt_execute($stmt);

        $image = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if ($image) {

            // Hitung jumlah screenshot
            $stmt = mysqli_prepare(
                $conn,
                "SELECT COUNT(*) AS jumlah
                 FROM project_images
                 WHERE project_id = ?"
            );

            mysqli_stmt_bind_param($stmt, "i", $projectId);
            mysqli_stmt_execute($stmt);

            $count = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

            if ((int)$count["jumlah"] <= 1) {

                $error = "Minimal harus ada satu screenshot. Tambahkan gambar pengganti terlebih dahulu.";

            } else {

                $stmt = mysqli_prepare(
                    $conn,
                    "DELETE FROM project_images
                     WHERE id = ? AND project_id = ?"
                );

                mysqli_stmt_bind_param($stmt, "ii", $imageId, $projectId);

                if (mysqli_stmt_execute($stmt)) {

                    $filePath = $uploadDir . basename($image["image"]);

                    if (is_file($filePath)) {
                        unlink($filePath);
                    }

                    header(
                        "Location: edit_proyek.php?id=" .
                        $projectId . "&status=hapus_berhasil"
                    );
                    exit;

                } else {
                    $error = "Gagal menghapus screenshot.";
                }
            }

        } else {
            $error = "Screenshot tidak ditemukan.";
        }

    } else {
        $error = "ID screenshot tidak valid.";
    }
}

// ===================================
// PROSES SIMPAN PERUBAHAN PROYEK
// ===================================
if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    ($_POST["action"] ?? "") === "save_project"
) {

    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $technology = trim($_POST["technology"] ?? "");

    if ($title === "" || $description === "") {

        $error = "Nama proyek dan deskripsi wajib diisi.";

    } else {

        $uploadedFiles = [];
        $transactionStarted = false;

        try {

            $filesToInsert = [];

            // Periksa apakah ada gambar baru
            if (
                isset($_FILES["images"]) &&
                is_array($_FILES["images"]["name"])
            ) {

                $files = $_FILES["images"];

                $selectedCount = count(
                    array_filter($files["name"], function ($name) {
                        return $name !== "";
                    })
                );

                // Hitung screenshot yang sudah ada
                $stmt = mysqli_prepare(
                    $conn,
                    "SELECT COUNT(*) AS jumlah
                     FROM project_images
                     WHERE project_id = ?"
                );

                mysqli_stmt_bind_param($stmt, "i", $projectId);
                mysqli_stmt_execute($stmt);

                $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

                if ((int)$data["jumlah"] + $selectedCount > 10) {
                    throw new Exception(
                        "Maksimal 10 screenshot untuk setiap proyek."
                    );
                }

                $allowedTypes = [
                    "image/jpeg" => "jpg",
                    "image/png" => "png",
                    "image/webp" => "webp"
                ];

                foreach ($files["name"] as $i => $originalName) {

                    if ($originalName === "") {
                        continue;
                    }

                    if ($files["error"][$i] !== UPLOAD_ERR_OK) {
                        throw new Exception("Ada gambar yang gagal diunggah.");
                    }

                    if ($files["size"][$i] > 3 * 1024 * 1024) {
                        throw new Exception(
                            "Ukuran setiap gambar maksimal 3 MB."
                        );
                    }

                    $tmpName = $files["tmp_name"][$i];
                    $imageInfo = getimagesize($tmpName);

                    if ($imageInfo === false) {
                        throw new Exception("File bukan gambar yang valid.");
                    }

                    $mime = $imageInfo["mime"];

                    if (!isset($allowedTypes[$mime])) {
                        throw new Exception(
                            "Format gambar harus JPG, PNG, atau WEBP."
                        );
                    }

                    $filesToInsert[] = [
                        "tmp" => $tmpName,
                        "extension" => $allowedTypes[$mime]
                    ];
                }
            }

            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    throw new Exception("Folder gambar gagal dibuat.");
                }
            }

            // Simpan gambar baru ke folder
            foreach ($filesToInsert as $image) {

                $imageName = bin2hex(random_bytes(16))
                           . "." . $image["extension"];

                $destination = $uploadDir . $imageName;

                if (!move_uploaded_file($image["tmp"], $destination)) {
                    throw new Exception("Gagal menyimpan gambar.");
                }

                $uploadedFiles[] = $imageName;
            }

            // Mulai transaksi database
            mysqli_begin_transaction($conn);
            $transactionStarted = true;

            // Update informasi proyek
            $stmtUpdate = mysqli_prepare(
                $conn,
                "UPDATE projects
                 SET title = ?, description = ?, technology = ?
                 WHERE id = ?"
            );

            mysqli_stmt_bind_param(
                $stmtUpdate,
                "sssi",
                $title,
                $description,
                $technology,
                $projectId
            );

            mysqli_stmt_execute($stmtUpdate);

            // Simpan gambar baru
            if (!empty($uploadedFiles)) {

                $stmtImage = mysqli_prepare(
                    $conn,
                    "INSERT INTO project_images (project_id, image)
                     VALUES (?, ?)"
                );

                foreach ($uploadedFiles as $imageName) {

                    mysqli_stmt_bind_param(
                        $stmtImage,
                        "is",
                        $projectId,
                        $imageName
                    );

                    mysqli_stmt_execute($stmtImage);
                }
            }

            mysqli_commit($conn);
            $transactionStarted = false;

            header(
                "Location: edit_proyek.php?id=" .
                $projectId . "&status=simpan_berhasil"
            );
            exit;

        } catch (Throwable $e) {

            if ($transactionStarted) {
                mysqli_rollback($conn);
            }

            // Bersihkan file baru jika database gagal
            foreach ($uploadedFiles as $imageName) {

                $path = $uploadDir . $imageName;

                if (is_file($path)) {
                    unlink($path);
                }
            }

            $error = $e->getMessage();
        }
    }
}

// ===================================
// AMBIL ULANG DATA PROYEK
// ===================================
$stmt = mysqli_prepare($conn, "SELECT * FROM projects WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $projectId);
mysqli_stmt_execute($stmt);

$project = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Ambil screenshot
$stmtImages = mysqli_prepare(
    $conn,
    "SELECT * FROM project_images
     WHERE project_id = ?
     ORDER BY id ASC"
);

mysqli_stmt_bind_param($stmtImages, "i", $projectId);
mysqli_stmt_execute($stmtImages);

$imagesResult = mysqli_stmt_get_result($stmtImages);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Proyek - Portfolio</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            background: #fff5f8;
            font-family: Arial, sans-serif;
        }

        .form-card {
            background: white;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 5px 25px rgba(215,127,165,.10);
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

        .image-card {
            background: #fff5f8;
            border-radius: 12px;
            padding: 10px;
            height: 100%;
        }

        .image-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 8px;
        }
    </style>
</head>

<body>

<div class="container py-5">

    <div class="mb-4">
        <a href="dashboard.php" class="text-decoration-none pink">
            &larr; Kembali ke Dashboard
        </a>

        <h2 class="fw-bold mt-3">Edit Proyek</h2>

        <p class="text-muted">
            Ubah informasi proyek dan kelola screenshot.
        </p>
    </div>

    <div class="form-card">

        <?php if ($error !== ""): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (($_GET["status"] ?? "") === "simpan_berhasil"): ?>
            <div class="alert alert-success">
                Perubahan proyek berhasil disimpan.
            </div>
        <?php elseif (($_GET["status"] ?? "") === "hapus_berhasil"): ?>
            <div class="alert alert-success">
                Screenshot berhasil dihapus.
            </div>
        <?php endif; ?>

        <!-- FORM SIMPAN PROYEK -->
        <form method="POST" enctype="multipart/form-data">

            <input type="hidden" name="action" value="save_project">

            <div class="mb-3">
                <label class="form-label fw-semibold">Nama Proyek</label>

                <input type="text"
                       name="title"
                       class="form-control"
                       value="<?= htmlspecialchars($project["title"]) ?>"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi Proyek</label>

                <textarea name="description"
                          class="form-control"
                          rows="4"
                          required><?= htmlspecialchars($project["description"]) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Teknologi yang Digunakan
                </label>

                <input type="text"
                       name="technology"
                       class="form-control"
                       value="<?= htmlspecialchars($project["technology"]) ?>">
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Tambahkan Screenshot Baru
                </label>

                <input type="file"
                       name="images[]"
                       class="form-control"
                       accept=".jpg,.jpeg,.png,.webp"
                       multiple>

                <small class="text-muted">
                    Maksimal 10 screenshot per proyek,
                    masing-masing maksimal 3 MB.
                </small>
            </div>

            <button type="submit" class="btn btn-pink px-4">
                Simpan Perubahan
            </button>

            <a href="dashboard.php" class="btn btn-light px-4">
                Kembali
            </a>

        </form>

        <hr class="my-4">

        <!-- GALERI SCREENSHOT -->
        <h5 class="fw-bold mb-3">Screenshot Proyek Saat Ini</h5>

        <div class="row g-3">

            <?php if (mysqli_num_rows($imagesResult) > 0): ?>

                <?php while ($image = mysqli_fetch_assoc($imagesResult)): ?>

                    <div class="col-6 col-md-4 col-lg-3">

                        <div class="image-card">

                            <img
                                src="../assets/img/projects/<?= rawurlencode(basename($image["image"])) ?>"
                                alt="Screenshot proyek">

                            <!-- FORM HAPUS TERPISAH -->
                            <form method="POST"
                                  class="mt-2"
                                  onsubmit="return confirm('Yakin ingin menghapus screenshot ini saja?')">

                                <input type="hidden"
                                       name="action"
                                       value="delete_image">

                                <input type="hidden"
                                       name="image_id"
                                       value="<?= (int)$image["id"] ?>">

                                <button type="submit"
                                        class="btn btn-sm btn-outline-danger w-100">
                                    <i class="bi bi-trash"></i>
                                    Hapus Screenshot
                                </button>

                            </form>

                        </div>

                    </div>

                <?php endwhile; ?>

            <?php else: ?>

                <div class="col-12">
                    <p class="text-muted">
                        Belum ada screenshot.
                    </p>
                </div>

            <?php endif; ?>

        </div>

        <small class="text-muted d-block mt-3">
            Minimal satu screenshot harus tetap ada pada setiap proyek.
            Menghapus screenshot tidak akan menghapus proyek.
        </small>

    </div>

</div>

</body>
</html>
