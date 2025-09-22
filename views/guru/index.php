<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $dataPerSiswa */
/** @var int $bulan */
/** @var int $tahun */

$this->title = "Dashboard Kelas";
$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js');
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
                                    <h4 class="card-title"><?= $jumlahSiswa ?></h4>
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
                                    <i class="bi bi-messenger"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Pesan Masuk</p>
                                    <h4 class="card-title"><?= $unreadCount ?></h4>
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
                                    <i class="bi bi-repeat"></i>
                                </div>
                            </div>
                            <div class="col col-stats ms-3 ms-sm-0">
                                <div class="numbers">
                                    <p class="card-category">Tanggapi</p>
                                    <h4 class="card-title"><?= $pendingCount ?></h4>
                                </div>
                            </div>
                        </div>
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
                            <label for="selectSiswa">Pilih Siswa:</label>
                            <select id="selectSiswa" class="form-select">
                                <?php foreach($dataPerSiswa as $namaSiswa => $data): ?>
                                <option value="<?= Html::encode($namaSiswa) ?>"><?= Html::encode($namaSiswa) ?>
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
            const allDataPerSiswa = <?= json_encode($dataPerSiswa) ?>;
            const labelsTanggal =
                <?= json_encode(range(1, cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun))) ?>;
            const colors = ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff', '#f54291', '#42f554',
                '#f5a442'
            ];

            let chart = null;

            function renderChart(namaSiswa) {
                const dataPerMapel = allDataPerSiswa[namaSiswa]['dataPerMapel'] || {};
                const datasets = [];

                let colorIndex = 0;
                for (const mapel in dataPerMapel) {
                    // pastikan nilai untuk semua tanggal, pakai null jika kosong supaya garis tetap nyambung
                    const nilaiHarian = labelsTanggal.map(tgl => dataPerMapel[mapel][tgl] ?? null);
                    datasets.push({
                        label: mapel,
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

                if (chart) chart.destroy(); // hapus chart lama sebelum render baru

                chart = new Chart(document.getElementById('lineChart'), {
                    type: 'line',
                    data: {
                        labels: labelsTanggal,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        spanGaps: true, // garis tetap nyambung meskipun ada nilai kosong
                        plugins: {
                            title: {
                                display: true,
                                text: 'Nilai Harian: ' + namaSiswa
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

            // render chart awal pakai siswa pertama
            const firstSiswa = Object.keys(allDataPerSiswa)[0];
            renderChart(firstSiswa);

            // update chart ketika dropdown berubah
            document.getElementById('selectSiswa').addEventListener('change', function() {
                renderChart(this.value);
            });
        });
        </script>
    </div>
</div>
</div>
</div>
</div>
</div>