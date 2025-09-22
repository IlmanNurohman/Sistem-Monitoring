<?php

namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use app\models\Kelas;
use app\models\SiswaKelas;
use app\models\User;
use PhpOffice\PhpSpreadsheet\IOFactory;


class KelasController extends Controller
{
    public function actionIndex()
    {
         if (!Yii::$app->user->isGuest) {
        if (Yii::$app->user->identity->role === 'siswa') {
            $this->layout = 'sidebar-siswa';
        } elseif (Yii::$app->user->identity->role === 'guru') {
            $this->layout = 'sidebar-guru';
        } elseif (Yii::$app->user->identity->role === 'admin') {
            $this->layout = 'sidebar-admin';
        }
    }
        $kelas = Kelas::find()->all();
        return $this->render('index', ['kelas' => $kelas]);
    }
    public function actionView($id)
{
 if (!Yii::$app->user->isGuest) {
        if (Yii::$app->user->identity->role === 'admin') {
            $this->layout = 'sidebar-admin';
        }
    }
    $model = Kelas::findOne($id);
    if (!$model) {
        throw new NotFoundHttpException("Kelas tidak ditemukan");
    }

    return $this->render('view', [
        'model' => $model,
    ]);
}

    public function actionCreate()
    { 
         if (!Yii::$app->user->isGuest) {
        if (Yii::$app->user->identity->role === 'siswa') {
            $this->layout = 'sidebar-siswa';
        } elseif (Yii::$app->user->identity->role === 'guru') {
            $this->layout = 'sidebar-guru';
        } elseif (Yii::$app->user->identity->role === 'admin') {
            $this->layout = 'sidebar-admin';
        }
    }
        $model = new Kelas();
        $uploadModel = new \yii\base\DynamicModel(['file_excel']);
        $uploadModel->addRule('file_excel', 'file', ['extensions' => 'xls, xlsx']);
if ($model->load(Yii::$app->request->post())) {
    $uploadModel->file_excel = UploadedFile::getInstance($uploadModel, 'file_excel');
    if ($model->save()) {
        $inserted = 0;
        $skipped  = [];

        if ($uploadModel->validate() && $uploadModel->file_excel) {
            $spreadsheet = IOFactory::load($uploadModel->file_excel->tempName);
            $rows = $spreadsheet->getActiveSheet()->toArray();

            foreach (array_slice($rows, 1) as $row) {
    $nama          = $row[1]; // kolom Nama
    $jk            = $row[2]; // kolom JK
    $nisn_excel    = $row[3]; // kolom NISN (abaikan)
    $tanggal_lahir = !empty($row[4]) ? date('Y-m-d', strtotime($row[4])) : null;
    $alamat        = $row[5];

                $user = User::findOne(['username' => $nama]);
if ($user) {
        $siswa = new SiswaKelas();
        $siswa->kelas_id      = $model->id;
        $siswa->user_id       = $user->id;   // foreign key ke user
        $siswa->nama          = $nama;
        $siswa->jk            = $jk;
        $siswa->tanggal_lahir = $tanggal_lahir;
        $siswa->alamat        = $alamat;
        $siswa->nisn          = $user->nisn; // ambil dari user, bukan dari excel
        $siswa->save(false);
    } else {
        Yii::warning("NISN $nisn_excel tidak ditemukan di tabel user");
    }
}
        }

        // kasih feedback ke guru
        Yii::$app->session->setFlash('success', "Kelas berhasil disimpan. $inserted siswa ditambahkan.");
        if (!empty($skipped)) {
            Yii::$app->session->setFlash('warning', "Ada siswa yang dilewati karena NISN tidak ditemukan: " . implode(", ", $skipped));
        }

        return $this->redirect(['index']);
    }
}
        return $this->render('create', [
            'model' => $model,
            'uploadModel' => $uploadModel,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = Kelas::findOne($id);
        if (!$model) throw new NotFoundHttpException("Kelas tidak ditemukan");

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $model = Kelas::findOne($id);
        if ($model) $model->delete();
        return $this->redirect(['index']);
    }
}