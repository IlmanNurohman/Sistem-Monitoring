<?php
use yii\helpers\Html;
?>

<div class="container" style="padding-top: 20px;">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Pembelajaran</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="#">
                        <i class="bi bi-exclamation-triangle"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="bi bi-chevron-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Kejadian Khusus</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="  col-mb-12">
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-head-row card-tools-still-right">
                            <div class="card-title">Detail Pengajuan Izin</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="  col-mb-12">

                                <?php
echo "<p><strong>Tipe:</strong> {$model->tipe}</p>";
echo "<p><strong>Keterangan:</strong> {$model->keterangan}</p>";
echo "<p><strong>Status:</strong> {$model->status}</p>";
echo "<p><strong>Tanggapan Guru:</strong> " . ($model->tanggapan_guru ?: '-') . "</p>";
echo Html::a('Kembali', ['my-pengajuan'], ['class' => 'btn btn-secondary mt-2']);?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>