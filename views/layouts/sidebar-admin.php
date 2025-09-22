<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
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


    <link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ?>css/plugins.min.css" />
    <link rel="stylesheet" href="<?= Yii::$app->request->baseUrl ?>css/kaiadmin.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <?php $this->head() ?>
    <?= Html::csrfMetaTags() ?>
</head>

<body>
    <?php $this->beginBody() ?>

    <div class="wrapper">

        <!-- Sidebar -->
        <div class="sidebar" data-background-color="dark">
            <div class="sidebar-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="<?= Url::to(['dashboard/index']) ?>" class="logo">
                        <img src="<?= Yii::$app->request->baseUrl ?>/assets/img/logo3.png" alt="Logo"
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
                            <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                            <h4 class="text-section">Menu</h4>
                        </li>
                        <li class="nav-item">
                            <a href="<?= Url::to(['admin/index']) ?>"><i class="bi bi-house"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <li class="nav-section">
                            <span class="sidebar-mini-icon"><i class="fa fa-ellipsis-h"></i></span>
                            <h4 class="text-section">manajemen akun</h4>
                        </li>
                        <li class="nav-item">
                            <a href="<?= Url::to(['kelas/index']) ?>"><i class="bi bi-layers"></i>
                                <p>Manajemen Kelas</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= Url::to(['user/index']) ?>"><i class="bi bi-person-vcard"></i>
                                <p>Manajemen Akun</p>
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
                        <a href="<?= Url::to(['dashboard/index']) ?>" class="logo">
                            <img src="<?= Yii::$app->request->baseUrl ?>/assets/img/gbadmin.png" alt="navbar brand"
                                class="navbar-brand" height="20" />
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
                                                <img src="<?= 
    Yii::$app->user->identity->foto 
        ? Yii::$app->request->baseUrl . '/uploads/' . Yii::$app->user->identity->foto 
        : Yii::$app->request->baseUrl . '/assets/img/default.png' 
?>" class="avatar-img rounded" />

                                            </div>
                                            <div class="u-text">
                                                <h4><?= Html::encode(Yii::$app->user->identity->username ?? '_') ?>
                                                </h4>
                                                <p class="text-muted">
                                                    <?= Html::encode(Yii::$app->user->identity->email ?? '-') ?></p>
                                            </div>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                        <?= Html::beginForm(['/auth/logout'], 'post', ['id' => 'logout-form']) ?>
                                        <?= Html::submitButton('Logout', ['class' => 'dropdown-item', 'id' => 'btnLogout']) ?>
                                        <?= Html::endForm() ?>
                                    </li>

                                </ul>

                                <?php
$this->registerJs("
    $('.logout-button-admin').on('click', function(e) {
        e.preventDefault(); // hentikan submit form

        // Ambil form terdekat
        let form = $(this).closest('form');

        Swal.fire({
            title: 'Yakin mau logout?',
            text: 'Anda akan keluar dari akun admin.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Logout!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit(); // kirim POST
            }
        });
    });
");
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
    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= Yii::$app->request->baseUrl ?>js/core/popper.min.js"></script>
    <script src="<?= Yii::$app->request->baseUrl ?>js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <script src="<?= Yii::$app->request->baseUrl ?>js/plugin/datatables/datatables.min.js"></script>
    <script src="<?= Yii::$app->request->baseUrl ?>js/plugin/sweetalert/sweetalert.min.js"></script>
    <script src="<?= Yii::$app->request->baseUrl ?>js/kaiadmin.min.js"></script>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>