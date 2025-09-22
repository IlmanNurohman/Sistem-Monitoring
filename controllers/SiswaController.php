<?php

namespace app\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;
use Yii;
use yii\web\NotFoundHttpException;
use app\models\SiswaKelas;



class SiswaController extends Controller
{
    public function init()
    {
        parent::init();
        $this->layout = 'sidebar-siswa'; // ✅ set layout
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
                        'roles' => ['@'], // hanya user login
                        'matchCallback' => function ($rule, $action) {
                            return \Yii::$app->user->identity->role === 'siswa';
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

    // cari data siswa_kelas berdasarkan user login
    $siswaKelas = \app\models\SiswaKelas::find()
        ->where(['user_id' => $userId])
        ->one();

    if (!$siswaKelas) {
        throw new NotFoundHttpException('Anda belum terdaftar di kelas manapun.');
    }

    // ambil kelas dari relasi siswa_kelas
    $model = \app\models\Kelas::findOne($siswaKelas->kelas_id);

    if (!$model) {
        throw new NotFoundHttpException('Data kelas tidak ditemukan.');
    }

    // 🔥 ambil bulan aktif dari GET atau default bulan sekarang
    $bulan = Yii::$app->request->get('bulan', date('m'));
    $tahun = Yii::$app->request->get('tahun', date('Y'));

    // --- DATA GRAFIK LINE (nilai harian per mapel) ---
    $nilaiHarian = \app\models\Nilai::find()
        ->where([
            'user_id' => $userId,
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

    // --- DATA GRAFIK BAR (nilai akhir per semester) ---
    $nilaiAkhir = \app\models\Nilai::find()
        ->select(['semester', 'AVG(nilai) as rata2'])
        ->where([
            'user_id' => $userId,
            'jenis_nilai' => 'akhir'
        ])
        ->groupBy('semester')
        ->asArray()
        ->all();

    $dataSemester = [];
    foreach ($nilaiAkhir as $n) {
        $dataSemester[$n['semester']] = round($n['rata2']);
    }

    $kejadianKhusus = \app\models\KejadianKhusus::find()
    ->select(['tipe', 'COUNT(*) as total'])
    ->where([
        'siswa_id' => $userId,
        'status' => 'diterima' // ✅ hanya yang sudah diterima
    ])
    ->groupBy('tipe')
    ->asArray()
    ->all();
    

    return $this->render('index', [
        'bulan' => $bulan,
        'tahun' => $tahun,
        'dataPerMapel' => $dataPerMapel,
        'dataSemester' => $dataSemester,
        'model' => $model, // ✅ supaya bisa dipakai di view
        'kejadianKhusus' => $kejadianKhusus,
    ]);
}


}