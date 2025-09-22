<?php

namespace app\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\Kelas;
use yii;
use app\models\KejadianKhusus;


class GuruController extends Controller
{
    public function init()
    {
        parent::init();
        $this->layout = 'sidebar-guru'; // ✅ set layout
    }
    
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'kelas-saya'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return \Yii::$app->user->identity->role === 'guru';
                        }
                    ],
                ],
                'denyCallback' => function () {
                    return $this->redirect(['/auth/login']);
                }
            ],
        ];
    }

   public function actionIndex()
{
    $userId = Yii::$app->user->id;

    // ambil kelas guru sebagai wali kelas
    $kelas = \app\models\Kelas::find()
        ->where(['wali_guru_id' => $userId])
        ->one();

    if (!$kelas) {
        throw new \yii\web\NotFoundHttpException('Anda tidak memiliki kelas.');
    }

    // ambil semua siswa di kelas ini
    $siswaList = \app\models\SiswaKelas::find()
        ->where(['kelas_id' => $kelas->id])
        ->all();

    $bulan = Yii::$app->request->get('bulan', date('m'));
    $tahun = Yii::$app->request->get('tahun', date('Y'));

    $dataPerSiswa = [];

    foreach ($siswaList as $siswaKelas) {
        $namaSiswa = $siswaKelas->nama;
        $userIdSiswa = $siswaKelas->user_id;

        // nilai harian
        $nilaiHarian = \app\models\Nilai::find()
            ->where([
                'user_id' => $userIdSiswa,
                'jenis_nilai' => 'harian'
            ])
            ->andWhere(['MONTH(tanggal)' => $bulan])
            ->andWhere(['YEAR(tanggal)' => $tahun])
            ->orderBy(['tanggal' => SORT_ASC])
            ->all();

        $dataPerMapel = [];
        foreach ($nilaiHarian as $n) {
            $tgl = date('j', strtotime($n->tanggal));
            $dataPerMapel[$n->mapel][$tgl] = $n->nilai;
        }

        // nilai akhir
        $nilaiAkhir = \app\models\Nilai::find()
            ->select(['semester', 'AVG(nilai) as rata2'])
            ->where([
                'user_id' => $userIdSiswa,
                'jenis_nilai' => 'akhir'
            ])
            ->groupBy('semester')
            ->asArray()
            ->all();

        $dataSemester = [];
        foreach ($nilaiAkhir as $n) {
            $dataSemester[$n['semester']] = round($n['rata2']);
        }

        $dataPerSiswa[$namaSiswa] = [
            'dataPerMapel' => $dataPerMapel,
            'dataSemester' => $dataSemester
        ];
    }
    $kelas = Kelas::find()->where(['wali_guru_id' => Yii::$app->user->id])->one();

// hitung jumlah siswa
$jumlahSiswa = $kelas ? $kelas->getSiswaKelas()->count() : 0;
$user = Yii::$app->user->identity;

// Hitung pesan masuk yang belum dibaca
$unreadCount = \app\models\ChatMessages::find()
    ->where(['receiver_id' => $user->id, 'is_read' => 0])
    ->count();

    

$user = Yii::$app->user->identity;

// Hanya untuk guru
if ($user->role === 'guru') {
    // Ambil semua kelas yang dia walikan
    $kelas = \app\models\Kelas::find()->where(['wali_guru_id' => $user->id])->all();
    $kelasIds = array_column($kelas, 'id');

    // Hitung jumlah pengajuan pending siswa di kelas yang dia walikan
    $pendingCount = KejadianKhusus::find()
        ->joinWith('siswaKelas') // pastikan relasi siswaKelas ada di model KejadianKhusus
        ->where(['status' => 'pending'])
        ->andWhere(['kelas_id' => $kelasIds])
        ->count();
} else {
    $pendingCount = 0;
}

    return $this->render('index', [
        'bulan' => $bulan,
        'tahun' => $tahun,
        'dataPerSiswa' => $dataPerSiswa,
        'kelas' => $kelas,
         'jumlahSiswa' => $jumlahSiswa,
         'unreadCount' => $unreadCount,
          'pendingCount' => $pendingCount,
    ]);
}


    public function actionKelasSaya()
    {
        $userId = \Yii::$app->user->id; // ✅ pakai \Yii

        $kelas = Kelas::find()->where(['wali_guru_id' => $userId])->one();

        if (!$kelas) {
            throw new \yii\web\NotFoundHttpException("Anda belum menjadi wali kelas manapun.");
        }

        return $this->render('kelas-saya', [
            'model' => $kelas,
        ]);
    }
}