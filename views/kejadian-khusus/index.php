<?php
use yii\helpers\Html;
use yii\widgets\LinkPager;
?>
<div class="container" style="padding-top: 20px;">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Sekolah</h3>
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
                            <div class="card-title">Daftar Pengajuan</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="  col-mb-12">
                                <?php foreach ($dataProvider->getModels() as $item) {
    echo "<div class='card p-3 mb-2'>";
    echo "<strong>{$item->siswa->username}</strong><br>";
    echo "Tipe: {$item->tipe} <br>";
    echo "Keterangan: {$item->keterangan} <br>";
    echo "Status: {$item->status} <br>";

    if ($item->status === 'pending') {
        echo "<div class='text-end mt-2'>";
        echo Html::a('Tanggapi', ['tanggapi', 'id' => $item->id], [
            'class'=>'btn btn-primary mt-2'
        ]);
        echo "</div>";
    }

    echo "</div>";
}

// ✅ pagination links
echo LinkPager::widget([
    'pagination' => $dataProvider->pagination,
]);?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>