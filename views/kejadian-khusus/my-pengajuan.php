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
        <?php 
                    // Tambah tombol di bawah h3
                    echo Html::a('<i class="bi bi-plus-circle"></i> Tambah Pengajuan', ['create'], ['class' => 'btn btn-primary mb-3']);
                    ?>
        <div class="row">
            <div class="  col-mb-12">
                <div class="card card-round">

                    <div class="card-header">
                        <div class="card-head-row card-tools-still-right">
                            <div class="card-title">Daftar Pengajuan</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="  col-mb-12">
                                <?php 
foreach ($model as $item) {
    echo "<div class='card p-3 mb-2'>";
    echo "Tanggal: " . date('d-m-Y H:i', strtotime($item->created_at)) . "<br>";
    echo "Tipe: {$item->tipe} <br>";
    echo "Status: {$item->status} <br>";
    echo "<div class='text-end mt-2'>";
    echo Html::a('Lihat Detail', ['view', 'id' => $item->id], ['class'=>'btn btn-success']);
    echo "</div>";
    echo "</div>";
} 
?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>