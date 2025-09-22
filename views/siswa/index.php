<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\View;




/** @var yii\web\View $this */
$semester = Yii::$app->request->get('semester', 1); // default semester 1


$jumlahHari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
$labelsTanggal = range(1, $jumlahHari);

// encode data ke JSON untuk dipakai di JS
$labelsTanggalJson = json_encode($labelsTanggal);
$dataPerMapelJson = json_encode($dataPerMapel);
$dataSemesterJson = json_encode($dataSemester);

// Ambil daftar siswa unik
$kelas = Yii::$app->request->get('kelas', 'Kelas 5'); // misal default "Kelas 5"

$siswaList = \app\models\SiswaKelas::find()
    ->joinWith('kelas')   // relasi ke tabel kelas
    ->with('user')        // relasi ke tabel user
    ->where(['kelas.nama_kelas' => $kelas]) 
    ->all();

// Ambil daftar mapel unik
$mapelList = \app\models\Nilai::find()
    ->select('mapel')
    ->distinct()
    ->column();

$rankingData = [];
foreach ($siswaList as $siswa) {
    $totalNilai = 0;
    foreach ($mapelList as $mapelName) {
        $nilai = \app\models\Nilai::find()
            ->where([
                'user_id' => $siswa->user_id,
                'mapel' => $mapelName,
                'jenis_nilai' => 'akhir',
                'semester' => $semester,
            ])
            ->one();
        if ($nilai && $nilai->nilai !== null) {
            $totalNilai += $nilai->nilai;
        }
    }
    $rankingData[$siswa->user_id] = [
        'siswa' => $siswa,
        'total' => $totalNilai
    ];
}

// 🔥 Urutkan berdasarkan total nilai DESC
usort($rankingData, function($a, $b) {
    return $b['total'] <=> $a['total'];
});

// 🔥 Assign peringkat (hanya 10 besar)
$rank = 1;
foreach ($rankingData as &$data) {
    if ($rank <= 10 && $data['total'] > 0) {
        $data['rank'] = $rank++;
    } else {
        $data['rank'] = '-';
    }
}
unset($data);

// 🔥 Convert ke array indexed ulang
$rankingData = array_values($rankingData);

?>
<?php
$js = '';
if (Yii::$app->session->hasFlash('loginSuccess')) {
    $msg = Yii::$app->session->getFlash('loginSuccess');
    $js = "Swal.fire('Berhasil', '$msg', 'success');";
}
$this->registerJs($js, View::POS_END);
?>

<div class="container" style="padding-top: 20px;">
    <div class="page-inner">
        <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
            <div>
                <h3 class="fw-bold mb-3">Dashboard</h3>
                <h6 class="op-7 mb-2">Hallo, <?= Html::encode(Yii::$app->user->identity->username ?? '_') ?> selamat
                    datang</h6>
            </div>
        </div>


        <div class="row">
            <div class="col-md-8">
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-head-row">
                            <div class="card-title">Perkembangan Nilai Harian
                                (<?= date('F Y', strtotime("$tahun-$bulan-01")) ?>)</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container" style="min-height: 375px">
                            <canvas id="statisticsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4" style="height: 300px;">
                <div class="card card-light card-round">
                    <div class="card-header">
                        <div class="card-head-row">
                            <div class="card-title">Jumlah Siswa</div>
                        </div>
                    </div>
                    <div class="card-body pb-0">
                        <div id="barChart">
                            <canvas id="barChartCanvas"></canvas>
                        </div>
                    </div>
                </div>
                <div class="card card-round">
                    <div class="card-body pb-0">
                        <div class="card-header">
                            <div class="card-head-row">
                                <div class="card-title">Kejadian Khusus</div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover text-center ">
                                <thead>
                                    <tr>
                                        <th>Keterangan</th>
                                        <th>Total</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($kejadianKhusus)): ?>
                                    <?php foreach ($kejadianKhusus as $row): ?>
                                    <tr>
                                        <td><?= ucfirst($row['tipe']) ?></td>
                                        <td><?= $row['total'] ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="2"><i>Tidak ada data kejadian khusus diterima</i></td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class=" row">
            <div class="  col-mb-12">
                <div class="row">
                    <div class="  col-mb-12">
                        <div class="card card-round">
                            <div class="card-header">
                                <div
                                    class="card-head-row card-tools-still-right d-flex justify-content-between align-items-center">
                                    <div class="card-title">
                                        Daftar Peringkat Siswa <?= Html::encode($model->nama_kelas) ?>
                                    </div>

                                    <div>
                                        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => Url::to(['index']),
        ]); ?>

                                        <?= Html::dropDownList('semester', $semester, [
            1 => 'Semester 1',
            2 => 'Semester 2',
        ], [
            'class' => 'form-control',
            'prompt' => '-- Pilih Semester --',
            'onchange' => 'this.form.submit()'
        ]) ?>

                                        <?php ActiveForm::end(); ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">

                                        <div class="table-responsive" style="overflow-x:auto; white-space:nowrap;">
                                            <table id="multi-filter-select"
                                                class="table table-bordered table-striped table-hover text-center ">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Siswa</th>
                                                        <th>Total</th>
                                                        <th>Peringkat</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($rankingData as $index => $data): ?>
                                                    <?php $siswa = $data['siswa']; ?>
                                                    <tr>
                                                        <td><?= $index+1 ?></td>
                                                        <td><?= $siswa->nama ?? '-' ?></td>
                                                        <td><b><?= $data['total'] > 0 ? $data['total'] : '-' ?></b>
                                                        </td>
                                                        <td style="color:blue; font-weight:bold;">
                                                            <?= $data['rank'] ?>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php
$this->registerJs("
    const labelsTanggal = $labelsTanggalJson;
    const dataPerMapel = $dataPerMapelJson;
    const dataSemester = $dataSemesterJson;

    // === LINE CHART ===
    const datasets = [];
    const colors = ['#ff6384','#36a2eb','#ffce56','#4bc0c0','#9966ff','#f54291','#42f554','#f5a442'];

    let colorIndex = 0;
    for (const mapel in dataPerMapel) {
        const nilaiHarian = labelsTanggal.map(tgl => dataPerMapel[mapel][tgl] ?? NaN);

        datasets.push({
            label: mapel,
            data: nilaiHarian,
            borderColor: colors[colorIndex % colors.length],
            backgroundColor:colors[colorIndex % colors.length],
            pointBackgroundColor: colors[colorIndex % colors.length], // warna titik
            pointBorderColor: colors[colorIndex % colors.length],     // warna garis titik
            fill: false,
            tension: 0.3,
            pointRadius: 5,
            pointHoverRadius: 7,
            pointStyle: 'circle',
            showLine: true,
            spanGaps: true // ✅ nyambung walau ada gap data
        });
        colorIndex++;
    }

    new Chart(document.getElementById('statisticsChart'), {
        type: 'line',
        data: {
            labels: labelsTanggal,
            datasets: datasets
        },
        options: {
            responsive: true,
            plugins: {
                title: { display: true, text: 'Nilai Harian per Mapel' },
                legend: { display: true, position: 'bottom' }
            },
            scales: {
                y: { min: 0, max: 100 }
            }
        }
    });

    // === BAR CHART ===
    const semesterLabels = Object.keys(dataSemester).map(s => 'Semester ' + s);
    const semesterValues = Object.values(dataSemester);

    new Chart(document.getElementById('barChartCanvas'), {
        type: 'bar',
        data: {
            labels: semesterLabels,
            datasets: [{
                label: 'Nilai Akhir',
                data: semesterValues,
                backgroundColor: '#36a2eb'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { min: 0, max: 100 }
            }
        }
    });

    $(document).ready(function() {
        $('#basic-datatables').DataTable({});

        $('#multi-filter-select').DataTable({
            pageLength: 5,
            initComplete: function() {
                this.api()
                    .columns()
                    .every(function() {
                        var column = this;
                        var select = $('<select class=\"form-select\"><option value=\"\"></option></select>')
                            .appendTo($(column.footer()).empty())
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                column.search(val ? '^'+val+'$' : '', true, false).draw();
                            });

                        column.data().unique().sort().each(function(d, j) {
                            select.append('<option value=\"'+d+'\">'+d+'</option>');
                        });
                    });
            }
        });

        $('#add-row').DataTable({ pageLength: 5 });

        var action = '<td> <div class=\"form-button-action\"> <button type=\"button\" class=\"btn btn-link btn-primary btn-lg\"> <i class=\"fa fa-edit\"></i> </button> <button type=\"button\" class=\"btn btn-link btn-danger\"> <i class=\"fa fa-times\"></i> </button> </div> </td>';

        $('#addRowButton').click(function() {
            $('#add-row').dataTable().fnAddData([
                $('#addName').val(),
                $('#addPosition').val(),
                $('#addOffice').val(),
                action,
            ]);
            $('#addRowModal').modal('hide');
        });
    });
");
?>