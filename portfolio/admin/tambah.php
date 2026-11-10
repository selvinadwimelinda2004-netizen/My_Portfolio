<?php
require_once "../includes/auth.php";
require_once "../config/database.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $technology = trim($_POST["technology"] ?? "");

    if ($title === "" || $description === "") {
        $error = "Nama proyek dan deskripsi wajib diisi.";
    } elseif (!isset($_FILES["images"])) {
        $error = "Silakan pilih gambar proyek.";
    } else {

        $files = $_FILES["images"];
        $allowedTypes = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/webp" => "webp"
        ];

        $total = count($files["name"]);
        $validImages = [];

        if ($total < 1 || $total > 10) {
            $error = "Pilih minimal 1 dan maksimal 10 gambar.";
        } else {

            // Validasi semua gambar
            for ($i = 0; $i < $total; $i++) {

                if ($files["error"][$i] !== UPLOAD_ERR_OK) {
                    $error = "Ada gambar yang gagal diunggah.";
                    break;
                }

                if ($files["size"][$i] > 3 * 1024 * 1024) {
                    $error = "Setiap gambar maksimal 3 MB.";
                    break;
                }

                $tmpName = $files["tmp_name"][$i];
                $imageInfo = getimagesize($tmpName);

                if ($imageInfo === false) {
                    $error = "File yang dipilih bukan gambar.";
                    break;
                }

                $mime = $imageInfo["mime"];

                if (!isset($allowedTypes[$mime])) {
                    $error = "Format gambar harus JPG, PNG, atau WEBP.";
                    break;
                }

                $validImages[] = [
                    "tmp" => $tmpName,
                    "extension" => $allowedTypes[$mime]
                ];
            }
        }

        if ($error === "") {

            $uploadDir = "../assets/img/projects/";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $uploadedFiles = [];

            try {
                // Simpan file gambar
                foreach ($validImages as $image) {

                    $imageName = bin2hex(random_bytes(16))
                               . "." . $image["extension"];

                    $destination = $uploadDir . $imageName;

                    if (!move_uploaded_file(
                        $image["tmp"],
                        $destination
                    )) {
                        throw new Exception("Gagal menyimpan gambar.");
                    }

                    $uploadedFiles[] = $imageName;
                }

                // Simpan data proyek dan gambar ke database
                mysqli_begin_transaction($conn);

                $stmt = mysqli_prepare(
                    $conn,
                    "INSERT INTO projects
                    (title, description, technology)
                    VALUES (?, ?, ?)"
                );

                mysqli_stmt_bind_param(
                    $stmt,
                    "sss",
                    $title,
                    $description,
                    $technology
                );

                mysqli_stmt_execute($stmt);

                $projectId = mysqli_insert_id($conn);

                $stmtImage = mysqli_prepare(
                    $conn,
                    "INSERT INTO project_images
                    (project_id, image)
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

                mysqli_commit($conn);

                header("Location: dashboard.php?status=tambah_berhasil");
                exit;

            } catch (Throwable $e) {

                mysqli_rollback($conn);

                // Hapus file jika penyimpanan gagal
                foreach ($uploadedFiles as $imageName) {
                    $path = $uploadDir . $imageName;

                    if (file_exists($path)) {
                        unlink($path);
                    }
                }

                $error = "Gagal menyimpan proyek. Silakan coba lagi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah Proyek - Portfolio</title>

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
    </style>
</head>

<body>

<div class="container py-5">

    <div class="mb-4">
        <a href="dashboard.php" class="text-decoration-none pink">
            &larr; Kembali ke Dashboard
        </a>

        <h2 class="fw-bold mt-3">Tambah Proyek</h2>

        <p class="text-muted">
            Tambahkan informasi dan screenshot proyek kamu.
        </p>
    </div>

    <div class="form-card">

        <?php if ($error !== ""): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Nama Proyek
                </label>

                <input
                    type="text"
                    name="title"
                    class="form-control"
                    placeholder="Contoh: Website Absensi RFID"
                    value="<?= htmlspecialchars($_POST['title'] ?? '') ?>"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Deskripsi Proyek
                </label>

                <textarea
                    name="description"
                    class="form-control"
                    rows="4"
                    placeholder="Jelaskan proyek yang kamu buat..."
                    required
                ><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Teknologi yang Digunakan
                </label>

                <input
                    type="text"
                    name="technology"
                    class="form-control"
                    placeholder="PHP, MySQL, Bootstrap, ESP8266"
                    value="<?= htmlspecialchars($_POST['technology'] ?? '') ?>"
                >
            </div>

            <div class="mb-4">

                <label class="form-label fw-semibold">
                    Screenshot / Gambar Proyek
                </label>

                <input
                    type="file"
                    name="images[]"
                    class="form-control"
                    accept=".jpg,.jpeg,.png,.webp"
                    multiple
                    required
                >

                <small class="text-muted">
                    Pilih beberapa gambar sekaligus.
                    Maksimal 10 gambar, masing-masing 3 MB.
                </small>

            </div>

            <button type="submit" class="btn btn-pink px-4">
                Simpan Proyek
            </button>

            <a href="dashboard.php" class="btn btn-light px-4">
                Batal
            </a>

        </form>

    </div>

</div>

</body>
</html>