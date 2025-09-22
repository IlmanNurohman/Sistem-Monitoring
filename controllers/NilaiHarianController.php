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

class NilaiHarianController extends Controller
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
        ->where(['jenis_nilai' => 'harian'])
        ->andWhere(['user_id' => $siswaIds]);

    // cek filter mapel
    $mapel = Yii::$app->request->get('mapel');
    if (!empty($mapel)) {
        $query->andWhere(['mapel' => $mapel]);
    }

    $dataProvider = new \yii\data\ActiveDataProvider([
        'query' => $query,
    ]);

    // ambil siswa sesuai kelas wali guru
    $siswaList = \app\models\SiswaKelas::find()
        ->with('user')
        ->where(['kelas_id' => $kelas->id])
        ->all();

    return $this->render('index', [
        'dataProvider' => $dataProvider,
        'mapel' => $mapel,
        'siswaList' => $siswaList, // kirim ke view
        'kelas' => $kelas,
    ]);
}


  public function actionCreate()
{
    $model = new Nilai();
    $model->jenis_nilai = 'harian';

    if (Yii::$app->request->isPost) {
        $model->excelFile = UploadedFile::getInstance($model, 'excelFile');
        $mapel = Yii::$app->request->post('Nilai')['mapel'] ?? 'Umum';
        $tanggal = Yii::$app->request->post('Nilai')['tanggal'] ?? date('Y-m-d');
        $semester = Yii::$app->request->post('Nilai')['semester'] ?? 1;

        $sukses = 0;
        $gagal = [];

        if ($model->excelFile) {
            $spreadsheet = IOFactory::load($model->excelFile->tempName);
            $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

            foreach ($sheetData as $i => $row) {
                if ($i == 1) continue; // skip header
                $nisn  = str_pad(trim((string)$row['C']), 10, "0", STR_PAD_LEFT);
                $nama  = trim((string)$row['B']);
                $nilai = trim((string)$row['D']);
                if (!$nisn || !$nilai) continue;

                $siswa = \app\models\User::findOne(['nisn' => $nisn]);
                if ($siswa) {
                    $nilaiModel = \app\models\Nilai::findOne([
                        'user_id' => $siswa->id,
                        'mapel' => $mapel,
                        'jenis_nilai' => 'harian',
                        'tanggal' => $tanggal,
                        'semester' => $semester,
                    ]);
                    if (!$nilaiModel) {
                        $nilaiModel = new Nilai();
                        $nilaiModel->user_id = $siswa->id;
                        $nilaiModel->mapel = $mapel;
                        $nilaiModel->jenis_nilai = 'harian';
                        $nilaiModel->tanggal = $tanggal;
                        $nilaiModel->semester = $semester;
                    }
                    $nilaiModel->nilai = (int)$nilai;
                    $nilaiModel->semester = $semester;

                    if ($nilaiModel->save()) {
                        $sukses++;
                    } else {
                        $gagal[] = "Baris {$i} → Error: " . json_encode($nilaiModel->getErrors());
                    }
                } else {
                    $gagal[] = "Baris {$i} → NISN: {$nisn}, Nama: {$nama}";
                }
            }

            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return [
                    'status' => empty($gagal) ? 'success' : 'error',
                    'message' => "Upload selesai. Berhasil: {$sukses}, Gagal: " . count($gagal),
                    'details' => $gagal
                ];
            }

            // Kalau request normal (fallback)
            Yii::$app->session->setFlash('success', "Upload selesai. Data berhasil: {$sukses}. Data gagal: " . count($gagal));
            if (!empty($gagal)) {
                Yii::$app->session->setFlash('error', "Baris gagal: <br>" . implode('<br>', $gagal));
            }
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