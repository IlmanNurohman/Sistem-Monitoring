<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);



$this->registerJs("
    $('#btnLogout').on('click', function(e) {
        e.preventDefault(); // stop form submit

        // Tutup dropdown
        $(this).closest('.dropdown-menu').removeClass('show').parent().removeClass('show');

        // Tampilkan konfirmasi SweetAlert
        Swal.fire({
            title: 'Yakin mau logout?',
            text: 'Kamu akan keluar dari akun ini.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Logout!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#logout-form').submit(); // kirim POST request
            }
        });
    });
");
?>


<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">


<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title><?= Html::encode($this->title) ?></title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />

    <script src="<?= Yii::$app->request->baseUrl ?>/assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
    WebFont.load({
        google: {
            families: ["Public Sans:300,400,500,600,700"]
        },
        custom: {
            families: [
                "Font Awesome 5 Solid",
                "Font Awesome 5 Regular",
                "Font Awesome 5 Brands",
                "simple-line-icons",
            ],
            urls: ["<?= Yii::$app->request->baseUrl ?>/assets/css/fonts.min.css"],
        },
        active: function() {
            sessionStorage.fonts = true;
        },
    });
    </script>

    <link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ?>/css/plugins.min.css" />
    <link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ?>/css/kaiadmin.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <?php $this->head() ?>

    <script src="https://cdn.onesignal.com/sdks/web/v16/OneSignalSDK.page.js" defer></script>
    <script>
    window.OneSignalDeferred = window.OneSignalDeferred || [];
    OneSignalDeferred.push(async function(OneSignal) {
        await OneSignal.init({
            appId: "827ae35c-f117-4c67-8fdb-f73618f29245",
        });

        // Minta izin notifikasi
        OneSignal.Notifications.requestPermission();

        // Ambil playerId setelah user login
        OneSignal.User.PushSubscription.addEventListener("change", async function(event) {
            if (event.current && event.current.id) {
                const playerId = event.current.id;

                // Simpan ke backend
                await fetch('/chat/save-player-id', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?= Yii::$app->request->csrfToken ?>'
                    },
                    body: JSON.stringify({
                        player_id: playerId
                    })
                });
            }
        });
    });
    </script>

</head>

<body>
    <?php $this->beginBody() ?>

    <div class="wrapper">

        <!-- Sidebar -->
        <div class="sidebar" data-background-color="dark">
            <div class="sidebar-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="" class=" logo">
                        <img src="<?= Yii::getAlias('@web') ?>/assets/img/logo3.png" alt="Logo"
                            style="height: 30px; margin-right: 10px;">
                    </a>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
                        <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
                    </div>

                </div>
            </div>
            <div class="sidebar-wrapper scrollbar scrollbar-inner">
                <div class="sidebar-content">
                    <ul class="nav nav-secondary">
                        <li class="nav-section">
                            <span class="sidebar-mini-icon"><i class="bi bi-three-dots"></i></span>
                            <h4 class="text-section">Menu</h4>
                        </li>
                        <li class="nav-item">
                            <a href="<?= Url::to(['guru/index']) ?>"><i class="bi bi-house"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-section">
                            <span class="sidebar-mini-icon"><i class="bi bi-three-dots"></i></span>
                            <h4 class="text-section">Sekolah</h4>
                        </li>
                        <li class="nav-item">
                            <a href="<?= Url::to(['guru/kelas-saya']) ?>"><i class="bi bi-layers"></i>
                                <p>Kelas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="collapse" href="#nilaiMenu" role="button"
                                aria-expanded="false" aria-controls="nilaiMenu">
                                <i class="bi bi-file-earmark-medical"></i>
                                <p>Nilai</p>
                                <i class="bi bi-chevron-down ms-auto"></i>
                            </a>
                            <div class="collapse" id="nilaiMenu">
                                <ul class="nav flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= Url::to(['nilai-harian/index']) ?>">Nilai
                                            Harian</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= Url::to(['nilai-uts/index']) ?>">UTS</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= Url::to(['nilai-uas/index']) ?>">UAS</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="<?= Url::to(['nilai-akhir/index']) ?>">Nilai Akhir</a>
                                    </li>
                                </ul>
                            </div>
                        </li>



                        <li class="nav-item">
                            <a href="<?= Url::to(['kejadian-khusus/index']) ?>"><i
                                    class="bi bi-exclamation-triangle"></i>
                                <p>Kejadian Khusus</p>
                            </a>
                        </li>
                        <li class="nav-section">
                            <span class="sidebar-mini-icon"><i class="bi bi-three-dots"></i></span>
                            <h4 class="text-section">Layanan</h4>
                        </li>

                        <li class="nav-item">
                            <a href="<?= Url::to(['chat/list']) ?>"><i class="bi bi-envelope"></i>
                                <p>Pesan</p>
                            </a>
                        </li>

                    </ul>

                </div>
            </div>
        </div>

        <!-- Main Panel -->
        <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                        <a href="" class="logo">
                            <img src="<?= Yii::$app->request->baseUrl ?>/web/assets/img/logo.png" alt="navbar brand"
                                class="navbar-brand" height="20" />
                            <ul>
                                <li>
                                    <div class="dropdown-divider"></div>
                                    <?= Html::a('Profil Saya', ['/user/profile'], ['class' => 'dropdown-item']) ?>
                                    <div class="dropdown-divider"></div>
                                    <?php
            echo Html::beginForm(['/site/logout'], 'post', ['id' => 'logout-form'])
                . Html::submitButton('Logout', [
                    'class' => 'dropdown-item',
                    'id' => 'btnLogout'
                ])
                . Html::endForm();
            ?>
                                </li>
                            </ul>
                        </a>
                        <div class="nav-toggle">
                            <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
                            <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
                        </div>
                    </div>
                </div>
                <nav class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
                    <div class="container-fluid">

                        <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                            <li class="nav-item topbar-user dropdown hidden-caret">
                                <a class="dropdown-toggle profile-pic" data-bs-toggle="dropdown" href="#"
                                    aria-expanded="false">
                                    <div class="avatar-sm">
                                        <img src="<?= Yii::$app->user->identity->foto 
        ? Yii::$app->request->baseUrl . '/uploads/' . Yii::$app->user->identity->foto 
        : Yii::$app->request->baseUrl . '/assets/img/default.png' 
?>" alt="..." class="avatar-img rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-user animated fadeIn">
                                    <li>
                                        <div class="user-box">
                                            <div class="avatar-lg">
                                                <img src="<?= Yii::$app->user->identity->foto 
                        ? Yii::$app->request->baseUrl . '/uploads/' . Yii::$app->user->identity->foto 
                        : Yii::$app->request->baseUrl . '/assets/img/default.png' 
                    ?>" class="avatar-img rounded" />
                                            </div>
                                            <div class="u-text">
                                                <h4><?= Html::encode(Yii::$app->user->identity->username ?? '_') ?></h4>
                                                <p class="text-muted">
                                                    <?= Html::encode(Yii::$app->user->identity->email ?? '-') ?></p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                        <?= Html::a('Profil Saya', ['/user/profile'], ['class' => 'dropdown-item']) ?>
                                        <div class="dropdown-divider"></div>
                                        <?php
            echo Html::beginForm(['/site/logout'], 'post', ['id' => 'logout-form'])
                . Html::submitButton('Logout', [
                    'class' => 'dropdown-item',
                    'id' => 'btnLogout'
                ])
                . Html::endForm();
            ?>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>

            <div class="container">
                <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs'] ?? []]) ?>
                <?= $content ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="<?= Yii::$app->request->baseUrl ?>/js/core/popper.min.js"></script>
    <script src="<?= Yii::$app->request->baseUrl ?>/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="<?= Yii::$app->request->baseUrl ?>/js/plugin/datatables/datatables.min.js"></script>
    <script src="<?= Yii::$app->request->baseUrl ?>js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="<?= Yii::$app->request->baseUrl ?>/js/kaiadmin.min.js"></script>


    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>