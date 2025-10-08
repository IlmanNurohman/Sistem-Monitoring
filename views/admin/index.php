<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
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
            <div class="col-sm-6 col-md-4">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-primary bubble-shadow-small">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                            </div>
                            <div class=" col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Jumlah Siswa</p>
                                    <h4 class="card-title"><?= $totalSiswa ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-info bubble-shadow-small">
                                    <i class="bi bi-layers"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Kelas</p>
                                    <h4 class="card-title"><?= $jumlahKelas ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-md-4">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-icon">
                                <div class="icon-big text-center icon-success bubble-shadow-small">
                                    <i class="bi bi-person-video3"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Guru</p>
                                    <h4 class="card-title"><?= $jumlahWaliGuru ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="  col-md-8">
                <div class="card card-round">
                    <div class="card-header">
                        <div
                            class="card-head-row card-tools-still-right d-flex justify-content-between align-items-center">
                            <div class="card-title">
                                Daftar Peringkat Siswa
                            </div>
                            <?php $form = ActiveForm::begin([
    'method' => 'get',
    'action' => Url::to(['index']),
]); ?>

                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <?= Html::dropDownList('kelas_id', $kelas_id, 
            \yii\helpers\ArrayHelper::map($kelasList, 'id', 'nama_kelas'), 
            [
                'class' => 'form-control',
                'prompt' => '-- Pilih Kelas --',
                'onchange' => 'this.form.submit()'
            ]) 
        ?>
                                </div>
                                <div class="col-md-6">
                                    <?= Html::dropDownList('semester', $semester, [
            1 => 'Semester 1',
            2 => 'Semester 2',
        ], [
            'class' => 'form-control',
            'prompt' => '-- Pilih Semester --',
            'onchange' => 'this.form.submit()'
        ]) ?>
                                </div>
                            </div>

                            <?php ActiveForm::end(); ?>

                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="table-responsive" style="overflow-x:auto; white-space:nowrap;">

                                <?php if ($rankingData): ?>
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
                                            <td><?= Html::encode($siswa->nama ?? '-') ?></td>
                                            <td><b><?= $data['total'] > 0 ? $data['total'] : '-' ?></b></td>
                                            <td style="color:blue; font-weight:bold;">
                                                <?= $data['rank'] ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php else: ?>
                                <div class="alert alert-info">Silakan pilih kelas dan semester terlebih dahulu.</div>
                                <?php endif; ?>

                            </div>
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
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-round">
                        <div
                            class="card-header d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                            <div>
                                <div class="card-title">Perkembangan Nilai Harian
                                    (<?= date('F Y', strtotime("$tahun-$bulan-01")) ?>)</div>
                            </div>
                            <div>
                                <label for="selectKelas">Pilih Kelas:</label>
                                <select id="selectKelas" class="form-select">
                                    <option value="">-- Pilih Kelas --</option>
                                    <?php foreach($kelasList as $kelas): ?>
                                    <option value="<?= $kelas->id ?>" <?= ($kelasId == $kelas->id ? 'selected' : '') ?>>
                                        <?= Html::encode($kelas->nama_kelas) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="card-body">
                            <canvas id="lineChart" style="min-height: 400px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const dataPerKelas = <?= json_encode($dataPerKelas) ?>;
                const labelsTanggal =
                    <?= json_encode(range(1, cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun))) ?>;
                const colors = ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff',
                    '#f54291', '#42f554', '#f5a442'
                ];

                let chart = null;

                function renderChart() {
                    const datasets = [];
                    let colorIndex = 0;

                    for (const siswa in dataPerKelas) {
                        const nilaiHarian = labelsTanggal.map(tgl => dataPerKelas[siswa][tgl] ?? null);
                        datasets.push({
                            label: siswa,
                            data: nilaiHarian,
                            borderColor: colors[colorIndex % colors.length],
                            backgroundColor: colors[colorIndex % colors.length],
                            fill: false,
                            tension: 0.3,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        });
                        colorIndex++;
                    }

                    if (chart) chart.destroy();

                    chart = new Chart(document.getElementById('lineChart'), {
                        type: 'line',
                        data: {
                            labels: labelsTanggal,
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            spanGaps: true,
                            plugins: {
                                title: {
                                    display: true,
                                    text: ''
                                },
                                legend: {
                                    position: 'bottom'
                                }
                            },
                            scales: {
                                y: {
                                    min: 0,
                                    max: 100
                                }
                            }
                        }
                    });
                }

                renderChart();

                // Ganti kelas → reload halaman dengan query kelas_id
                document.getElementById('selectKelas').addEventListener('change', function() {
                    const kelasId = this.value;
                    if (kelasId) {
                        window.location.href = "?kelas_id=" + kelasId +
                            "&bulan=<?= $bulan ?>&tahun=<?= $tahun ?>";
                    }
                });
            });
            </script>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('barChartCanvas').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Laki-laki', 'Perempuan'],
            datasets: [{
                label: 'Jumlah Siswa',
                data: [<?= $jumlahLaki ?>, <?= $jumlahPerempuan ?>],
                backgroundColor: ['#36a2eb',
                    '#ff6384'
                ] // biru = Laki, merah = Perempuan
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Jumlah Siswa Laki-laki & Perempuan'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            }
        }
    });
});
</script>
<?php
$this->registerJs("
$(document).ready(function() {
    $('#basic-datatables').DataTable({});

    $('#multi-filter-select').DataTable({
        pageLength: 5,
        initComplete: function() {
            this.api()
                .columns()
                .every(function() {
                    var column = this;
                    var select = $(
                            '<select class=\"form-select\"><option value=\"\"></option></select>'
                        )
                        .appendTo($(column.footer()).empty())
                        .on('change', function() {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });

                    column.data().unique().sort().each(function(d, j) {
                        select.append('<option value=\"' + d + '\">' + d + '</option>');
                    });
                });
        }
    });

    $('#add-row').DataTable({
        pageLength: 5
    });

    var action =
        '<td> <div class=\"form-button-action\"> <button type=\"button\" class=\"btn btn-link btn-primary btn-lg\"> <i class=\"fa fa-edit\"></i> </button> <button type=\"button\" class=\"btn btn-link btn-danger\"> <i class=\"fa fa-times\"></i> </button> </div> </td>';

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