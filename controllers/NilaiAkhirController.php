<?php

namespace app\controllers;
use Yii;
use app\models\Nilai;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use yii\web\UploadedFile;

class NilaiAkhirController extends \yii\web\Controller
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
    $guruId = Yii::$app->user->id;
    
    $kelas = \app\models\Kelas::findOne(['wali_guru_id' => $guruId]);

    if (!$kelas) {
        throw new NotFoundHttpException('Anda tidak terdaftar sebagai wali kelas.');
    }

    // ambil user_id siswa di kelas tersebut
    $siswaIds = \app\models\SiswaKelas::find()
        ->select('user_id')
        ->where(['kelas_id' => $kelas->id])
        ->column();

    // ambil nilai hanya siswa tersebut
    $query = \app\models\Nilai::find()
        ->joinWith(['user'])
        ->where(['jenis_nilai' => 'akhir'])
        ->andWhere(['user_id' => $siswaIds]);

    // cek filter mapel
    $mapel = Yii::$app->request->get('mapel');
    if (!empty($mapel)) {
        $query->andWhere(['mapel' => $mapel]);
    }

    $dataProvider = new \yii\data\ActiveDataProvider([
        'query' => $query,
    ]);

    return $this->render('index', [
        'dataProvider' => $dataProvider,
        'mapel' => $mapel,
         'model' => $kelas,
         'siswaIds' => $siswaIds,
    ]);
}

    public function actionCreate()
{
    $model = new Nilai();
    $model->jenis_nilai = 'akhir';

    if (Yii::$app->request->isPost) {
        $model->excelFile = UploadedFile::getInstance($model, 'excelFile');
        if ($model->excelFile) {
            $spreadsheet = IOFactory::load($model->excelFile->tempName);
$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            $mapel = Yii::$app->request->post('Nilai')['mapel'] ?? 'Umum';
$tanggal = Yii::$app->request->post('Nilai')['tanggal'] ?? date('Y-m-d');
$semester = Yii::$app->request->post('Nilai')['semester'] ?? 1; // 🔥 ambil semester


            $sukses = 0; 
            $gagal = [];

    foreach ($sheetData as $i => $row) {
    if ($i == 1) continue; // skip header

    $nisn  = trim((string)$row['C']); // kolom C = NISN
    $nama  = trim((string)$row['B']); // kolom B = Nama
    $nilai = trim((string)$row['D']); // kolom D = Nilai

    if (!$nisn || !$nilai) {
        continue;
    }

    // pastikan NISN 10 digit
    $nisn = str_pad($nisn, 10, "0", STR_PAD_LEFT);

    // cari user hanya berdasarkan NISN
    $siswa = \app\models\User::findOne(['nisn' => $nisn]);

    if ($siswa) {
        // cek apakah nilai sudah pernah ada (untuk user+mapel+jenis_nilai+tanggal)
       $nilaiModel = \app\models\Nilai::findOne([
    'user_id' => $siswa->id,
    'mapel' => $mapel,
    'jenis_nilai' => 'akhir',
    'tanggal' => $tanggal,
    'semester' => $semester, // 🔥 tambahkan ini
]);


       if (!$nilaiModel) {
    $nilaiModel = new Nilai();
    $nilaiModel->user_id = $siswa->id;
    $nilaiModel->mapel = $mapel;
    $nilaiModel->jenis_nilai = 'akhir';
    $nilaiModel->tanggal = $tanggal;
    $nilaiModel->semester = $semester; // 🔥 tambahkan ini
}

// update nilai
$nilaiModel->nilai = (int) $nilai;
$nilaiModel->semester = $semester; // 🔥 pastikan diupdate juga kalau sudah ada


        if ($nilaiModel->save()) {
            $sukses++;
        }
    } else {
        $gagal[] = "Baris {$i} → NISN: {$nisn}, Nama: {$nama}";
    }
}

            Yii::$app->session->setFlash('success', "Upload selesai. Data berhasil: {$sukses}. Data gagal: " . count($gagal));
            if (!empty($gagal)) {
                Yii::$app->session->setFlash('error', "Baris gagal: <br>" . implode('<br>', $gagal));
            }

            return $this->redirect(['index']);
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }
    }

    return $this->render('create', [
        'model' => $model,
    ]);
}

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Nilai::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('Data tidak ditemukan.');
    }

}