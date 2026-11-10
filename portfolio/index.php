
<?php
require_once __DIR__ . "/config/database.php";

$page_title = "Selvina Dwi Melinda | Portfolio";

// Ambil semua proyek
$queryProjects = mysqli_query($conn, "
    SELECT * FROM projects
    ORDER BY id DESC
");

$projects = [];

if ($queryProjects) {
    while ($row = mysqli_fetch_assoc($queryProjects)) {
        $projects[] = $row;
    }
}

// Ambil semua gambar proyek
$queryImages = mysqli_query($conn, "
    SELECT project_id, image
    FROM project_images
    ORDER BY id ASC
");

$projectImages = [];

if ($queryImages) {
    while ($image = mysqli_fetch_assoc($queryImages)) {
        $projectImages[$image['project_id']][] = $image['image'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top navbar-portfolio">
    <div class="container">
        <a class="navbar-brand" href="#home">
            <span class="brand-icon">S.</span> Portfolio<span class="pink-dot">.</span>
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="#home">Beranda</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">Tentang Saya</a></li>
                <li class="nav-item"><a class="nav-link" href="#skills">Keahlian</a></li>
                <li class="nav-item"><a class="nav-link" href="#projects">Proyek</a></li>
                <li class="nav-item"><a class="nav-link" href="#experience">Pengalaman</a></li>
                <li class="nav-item">
                    <a class="nav-link contact-nav" href="#contact">Hubungi Saya</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- HERO SECTION -->
<section id="home" class="hero-section">
    <div class="container">
        <div class="row align-items-center gy-5">

            <div class="col-lg-6">
                <div class="hero-content">

                    <div class="availability">
                        <span class="availability-dot"></span>
                        Selamat datang di website portofolio saya
                    </div>

                    <p class="hero-greeting">Halo, perkenalkan saya</p>

                    <h1>
                        Selvina Dwi
                        <span>Melinda.</span>
                    </h1>

                    <h3>
                        Saya seorang <span id="typing-text">Web Developer</span>
                    </h3>

                    <p class="hero-description">
                        Mahasiswa Teknik Komputer yang memiliki ketertarikan
                        pada pengembangan website, pemrograman, dan teknologi
                        Internet of Things (IoT). Senang mempelajari hal baru
                        dan mengembangkan solusi digital yang bermanfaat.
                    </p>

                    <div class="hero-buttons">
                        <a href="#projects" class="btn btn-pink">
                            Lihat Proyek <i class="bi bi-arrow-right"></i>
                        </a>

                        <a href="#contact" class="btn btn-outline-pink">
                            Hubungi Saya
                        </a>
                    </div>

                    <div class="social-links">
                        <a href="#" aria-label="GitHub"><i class="bi bi-github"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="bi bi-linkedin"></i></a>
                        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    </div>

                </div>
            </div>

            <!-- FOTO PROFIL DAN ILUSTRASI -->
            <div class="col-lg-6">
                <div class="hero-visual">

                    <div class="pink-circle circle-one"></div>
                    <div class="pink-circle circle-two"></div>

                    <!-- Foto profil -->
                    <div class="profile-frame">
                        <img
                            src="assets/img/profile.jpg.jpeg"
                            alt="Foto Profil Selvina"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                        >
                        <div class="profile-fallback">S</div>
                    </div>

                    <!-- Ilustrasi avatar -->
                    <div class="avatar-card">
                        <svg viewBox="0 0 180 180" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="90" cy="90" r="85" fill="#FFE4EF"/>
                            <path d="M35 158 Q42 112 90 112 Q138 112 145 158" fill="#D77FA5"/>
                            <path d="M53 77 Q48 23 91 25 Q139 26 130 88 L120 113 L57 111 Z" fill="#513A49"/>
                            <ellipse cx="90" cy="79" rx="34" ry="42" fill="#F5C6A5"/>
                            <path d="M56 70 Q60 30 91 32 Q124 31 126 68 Q108 52 91 55 Q73 52 56 70" fill="#513A49"/>
                            <circle cx="77" cy="80" r="3" fill="#513A49"/>
                            <circle cx="103" cy="80" r="3" fill="#513A49"/>
                            <path d="M82 96 Q90 103 98 96" stroke="#B86E79" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                            <path d="M63 120 Q90 139 117 120 L129 158 H51 Z" fill="#FFFFFF"/>
                            <path d="M78 121 L90 139 L102 121" fill="#F2A7C5"/>
                            <circle cx="50" cy="88" r="7" fill="#EFA8C5"/>
                            <circle cx="130" cy="88" r="7" fill="#EFA8C5"/>
                        </svg>
                        <span>Keep Learning ✨</span>
                    </div>

                    <!-- Floating badge -->
                    <div class="floating-badge">
                        <i class="bi bi-code-slash"></i>
                        <div>
                            <strong>Creative Developer</strong>
                            <small>Always Learning</small>
                        </div>
                    </div>

                    <div class="decoration-star star-one">✦</div>
                    <div class="decoration-star star-two">✧</div>

                </div>
            </div>

        </div>
    </div>
</section>

<!-- ABOUT SECTION -->
<section id="about" class="section-padding">
    <div class="container">
        <div class="section-heading">
            <span class="section-label">KENALI SAYA</span>
            <h2>Tentang <span>Saya</span></h2>
            <p>Sedikit cerita tentang diri dan perjalanan saya.</p>
        </div>

        <div class="about-card">
            <p>
                Saya merupakan mahasiswa Teknik Komputer yang tertarik
                pada bidang teknologi informasi, khususnya pengembangan
                website dan Internet of Things. Saya memiliki pengalaman
                membuat website menggunakan HTML, CSS, JavaScript,
                PHP, dan MySQL serta mengembangkan proyek IoT
                menggunakan ESP8266, ESP32, dan RFID.
            </p>
            <p>
                Saya senang mengeksplorasi teknologi baru, belajar
                secara mandiri, dan mengembangkan kemampuan untuk
                menghasilkan karya digital yang bermanfaat.
            </p>
        </div>
    </div>
</section>

<!-- SKILLS SECTION -->
<section id="skills" class="section-padding skills-section">
    <div class="container">
        <div class="section-heading">
            <span class="section-label">KEMAMPUAN</span>
            <h2>Keahlian <span>Saya</span></h2>
            <p>Teknologi dan kemampuan yang saya pelajari.</p>
        </div>

        <div class="row g-4">
            <?php
            $skills = [
                ["HTML5", "bi-filetype-html"],
                ["CSS3", "bi-filetype-css"],
                ["JavaScript", "bi-filetype-js"],
                ["Bootstrap", "bi-bootstrap"],
                ["PHP", "bi-filetype-php"],
                ["MySQL", "bi-database"],
                ["ESP8266 & ESP32", "bi-cpu"],
                ["RFID & IoT", "bi-wifi"]
            ];

            foreach ($skills as $skill):
            ?>
            <div class="col-6 col-md-3">
                <div class="skill-card">
                    <i class="bi <?= $skill[1] ?>"></i>
                    <h5><?= htmlspecialchars($skill[0]) ?></h5>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>



<!-- PROJECTS SECTION -->
<section id="projects" class="section-padding">
    <div class="container">

        <div class="section-heading">
            <span class="section-label">PORTOFOLIO</span>
            <h2>Proyek <span>Saya</span></h2>
            <p>Beberapa karya dan proyek yang pernah saya kerjakan.</p>
        </div>

        <div class="row g-4">

            <?php if (!empty($projects)): ?>

                <?php foreach ($projects as $project): ?>

                    <?php
                    $id = (int)$project['id'];
                    $images = $projectImages[$id] ?? [];
                    $modalId = "modalProject" . $id;
                    $carouselId = "galleryProject" . $id;
                    ?>

                    <!-- CARD PROYEK -->
                    <div class="col-md-6 col-lg-4">

                        <div class="project-card project-clickable"
                             data-bs-toggle="modal"
                             data-bs-target="#<?= $modalId ?>"
                             role="button"
                             tabindex="0"
                             aria-label="Lihat proyek <?= htmlspecialchars($project['title']) ?>">

                            <?php if (!empty($images)): ?>

                                <div class="project-thumbnail">
                                    <img
                                        src="assets/img/projects/<?= htmlspecialchars($images[0]) ?>"
                                        alt="<?= htmlspecialchars($project['title']) ?>"
                                        onerror="this.style.display='none';"
                                    >
                                    <div class="view-project">
                                        <i class="bi bi-zoom-in"></i>
                                        Lihat Screenshot
                                    </div>
                                </div>

                            <?php else: ?>

                                <div class="project-placeholder">
                                    <i class="bi bi-image"></i>
                                </div>

                            <?php endif; ?>

                            <div class="project-body">

                                <span class="project-category">
                                    Web & Application Development
                                </span>

                                <h5>
                                    <?= htmlspecialchars($project['title']) ?>
                                </h5>

                                <p>
                                    <?= htmlspecialchars($project['description']) ?>
                                </p>

                                <?php
                                $technologies = explode(',', $project['technology']);

                                foreach ($technologies as $tech):
                                    $tech = trim($tech);
                                    if ($tech !== ''):
                                ?>
                                    <span class="tech-tag">
                                        <?= htmlspecialchars($tech) ?>
                                    </span>
                                <?php
                                    endif;
                                endforeach;
                                ?>

                                <div class="mt-3 small text-muted">
                                    <i class="bi bi-images"></i>
                                    <?= count($images) ?> Screenshot
                                    <span class="float-end">
                                        Lihat Detail <i class="bi bi-arrow-right"></i>
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>


                    <!-- MODAL GALERI PROYEK -->
                    <?php if (!empty($images)): ?>

                    <div class="modal fade"
                         id="<?= $modalId ?>"
                         tabindex="-1"
                         aria-hidden="true">

                        <div class="modal-dialog modal-xl modal-dialog-centered">

                            <div class="modal-content project-modal">

                                <div class="modal-header">
                                    <div>
                                        <h5 class="modal-title fw-bold">
                                            <?= htmlspecialchars($project['title']) ?>
                                        </h5>
                                        <small class="text-muted">
                                            <?= count($images) ?> Screenshot Proyek
                                        </small>
                                    </div>

                                    <button type="button"
                                            class="btn-close"
                                            data-bs-dismiss="modal">
                                    </button>
                                </div>

                                <div class="modal-body">

                                    <div id="<?= $carouselId ?>"
                                         class="carousel slide"
                                         data-bs-touch="true">

                                        <div class="carousel-inner">

                                            <?php foreach ($images as $index => $image): ?>

                                            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">

                                                <div class="gallery-image-wrapper">

                                                    <img
                                                        src="assets/img/projects/<?= htmlspecialchars($image) ?>"
                                                        class="gallery-large-image"
                                                        alt="Screenshot <?= $index + 1 ?>"
                                                    >

                                                </div>

                                            </div>

                                            <?php endforeach; ?>

                                        </div>

                                        <?php if (count($images) > 1): ?>

                                        <button class="carousel-control-prev"
                                                type="button"
                                                data-bs-target="#<?= $carouselId ?>"
                                                data-bs-slide="prev">

                                            <span class="carousel-control-prev-icon"></span>
                                        </button>

                                        <button class="carousel-control-next"
                                                type="button"
                                                data-bs-target="#<?= $carouselId ?>"
                                                data-bs-slide="next">

                                            <span class="carousel-control-next-icon"></span>
                                        </button>

                                        <?php endif; ?>

                                    </div>

                                    <div class="text-center mt-3 text-muted small">
                                        <i class="bi bi-hand-index-thumb"></i>
                                        Geser gambar ke kanan atau kiri untuk melihat screenshot lainnya.
                                    </div>

                                </div>

                                <div class="modal-footer">
                                    <button type="button"
                                            class="btn btn-pink"
                                            data-bs-dismiss="modal">
                                        Tutup Galeri
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                    <?php endif; ?>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="col-12 text-center">
                    <p class="text-muted">
                        Belum ada proyek yang ditambahkan.
                    </p>
                </div>

            <?php endif; ?>

        </div>

        <div class="text-center mt-5">
            <a href="projects.php" class="btn btn-pink">
                Lihat Semua Proyek
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

    </div>
</section>
<!-- EXPERIENCE SECTION -->
<section id="experience" class="section-padding skills-section">
    <div class="container">
        <div class="section-heading">
            <span class="section-label">PERJALANAN</span>
            <h2>Pengalaman <span>Saya</span></h2>
            <p>Pengalaman belajar dan pengembangan kemampuan.</p>
        </div>

        <div class="about-card">
            <div class="experience-item">
                <div class="experience-icon">
                    <i class="bi bi-mortarboard"></i>
                </div>
                <div>
                    <h5>Mahasiswa Teknik Komputer</h5>
                    <p>Universitas Ma'arif Hasyim Latief Sidoarjo</p>
                    <span>Bidang pendidikan</span>
                </div>
            </div>

            <hr>

            <div class="experience-item">
                <div class="experience-icon">
                    <i class="bi bi-laptop"></i>
                </div>
                <div>
                    <h5>Pengembangan Website</h5>
                    <p>Proyek mandiri dan pengembangan aplikasi web.</p>
                    <span>Pengalaman proyek</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CONTACT SECTION -->
<section id="contact" class="section-padding">
    <div class="container">
        <div class="contact-box">
            <span class="section-label">MARI TERHUBUNG</span>
            <h2>Mari Berkolaborasi <span> Bersama!</span></h2>
            <p>
                Terbuka untuk kesempatan belajar, kolaborasi,
                magang, dan pengembangan proyek.
            </p>
            <a href="mailto:emailkamu@example.com" class="btn btn-pink">
                <i class="bi bi-envelope"></i> Hubungi Saya
            </a>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="container text-center">
        <p>© <?= date("Y") ?> Selvina Dwi Melinda. Dibuat dengan
            <span>♥</span> dan semangat belajar.
        </p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>

</body>
</html>
