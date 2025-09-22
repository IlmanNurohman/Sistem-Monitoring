<?php

namespace app\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\Kelas;
use app\models\KejadianKhusus;
use yii;


class AdminController extends Controller
{

    public function init()
    {
        parent::init();
        $this->layout = 'sidebar-admin'; // ✅ set layout
    }
    
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return \Yii::$app->user->identity->role === 'admin';
                        }
                    ],
                ],
                'denyCallback' => function () {
                    return $this->redirect(['/auth/login']);
                }
            ],
        ];
    }

   public function actionIndex($kelas_id = null, $semester = null)
{
    $bulan = Yii::$app->request->get('bulan', date('m'));
    $tahun = Yii::$app->request->get('tahun', date('Y'));
    $kelasId = Yii::$app->request->get('kelas_id');

    $kelasList = \app\models\Kelas::find()->all();

    $dataPerKelas = [];
    if ($kelasId) {
        $siswaList = \app\models\SiswaKelas::find()
            ->where(['kelas_id' => $kelasId])
            ->all();

        foreach ($siswaList as $siswaKelas) {
            $namaSiswa = $siswaKelas->nama;
            $userIdSiswa = $siswaKelas->user_id;

            $nilaiHarian = \app\models\Nilai::find()
                ->where([
                    'user_id' => $userIdSiswa,
                    'jenis_nilai' => 'harian'
                ])
                ->andWhere(['MONTH(tanggal)' => $bulan])
                ->andWhere(['YEAR(tanggal)' => $tahun])
                ->all();

            $nilaiPerTanggal = [];
            foreach ($nilaiHarian as $n) {
                $tgl = date('j', strtotime($n->tanggal));
                $nilaiPerTanggal[$tgl][] = $n->nilai;
            }

            $rataRataPerTanggal = [];
            for ($tgl = 1; $tgl <= cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun); $tgl++) {
                if (!empty($nilaiPerTanggal[$tgl])) {
                    $rataRataPerTanggal[$tgl] = round(array_sum($nilaiPerTanggal[$tgl]) / count($nilaiPerTanggal[$tgl]));
                } else {
                    $rataRataPerTanggal[$tgl] = null;
                }
            }

            $dataPerKelas[$namaSiswa] = $rataRataPerTanggal;
        }
    }
    // hitung jumlah siswa laki-laki
    $jumlahLaki = \app\models\SiswaKelas::find()
        ->where(['jk' => 'L']) // sesuaikan kode jk: L = Laki-laki
        ->count();

    // hitung jumlah siswa perempuan
    $jumlahPerempuan = \app\models\SiswaKelas::find()
        ->where(['jk' => 'P']) // P = Perempuan
        ->count();

         $kelasList = \app\models\Kelas::find()->all();

    $rankingData = [];
    if ($kelas_id && $semester) {
        // Ambil siswa berdasarkan kelas
        $siswaList = \app\models\SiswaKelas::find()
            ->where(['kelas_id' => $kelas_id])
            ->all();

        // Ambil daftar mapel (misal distinct mapel di tabel nilai)
        $mapelList = \app\models\Nilai::find()
            ->select('mapel')
            ->distinct()
            ->column();

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

        // Urutkan DESC
        usort($rankingData, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        // Assign Rank (top 10 aja)
        $rank = 1;
        foreach ($rankingData as &$data) {
            if ($rank <= 10 && $data['total'] > 0) {
                $data['rank'] = $rank++;
            } else {
                $data['rank'] = '-';
            }
        }
        unset($data);

        // Reset index array
        $rankingData = array_values($rankingData);
    }

    return $this->render('index', [
        'bulan' => $bulan,
        'tahun' => $tahun,
        'kelasList' => $kelasList,
        'kelasId' => $kelasId,
        'dataPerKelas' => $dataPerKelas,
        'jumlahLaki' => $jumlahLaki,
        'jumlahPerempuan' => $jumlahPerempuan,
         'kelasList' => $kelasList,
        'kelas_id' => $kelas_id,
        'semester' => $semester,
        'rankingData' => $rankingData
    ]);
}

}