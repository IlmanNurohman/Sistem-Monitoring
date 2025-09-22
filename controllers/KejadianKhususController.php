<?php

namespace app\controllers;

use Yii;
use app\models\KejadianKhusus;

class KejadianKhususController extends \yii\web\Controller
{
    public function init()
    {
        parent::init();

        // Cek role user
        if (!Yii::$app->user->isGuest) {
            if (Yii::$app->user->identity->role === 'siswa') {
                $this->layout = 'sidebar-siswa';
            } elseif (Yii::$app->user->identity->role === 'guru') {
                $this->layout = 'sidebar-guru';
            }
        }
    }

   public function actionCreate()
{
    $model = new KejadianKhusus();
    $model->siswa_id = Yii::$app->user->id;

    if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if ($model->save()) {
            return ['status' => 'success', 'redirect' => \yii\helpers\Url::to(['view', 'id' => $model->id])];
        } else {
            return ['status' => 'error', 'errors' => $model->errors];
        }
    }

    return $this->render('create', ['model' => $model]);
}


    // Menampilkan semua pengajuan
   public function actionIndex()
{
    $query = KejadianKhusus::find()
        ->orderBy([
            new \yii\db\Expression("CASE WHEN status='pending' THEN 0 ELSE 1 END"), // pending duluan
            'created_at' => SORT_DESC, // terbaru
        ]);

    $dataProvider = new \yii\data\ActiveDataProvider([
        'query' => $query,
        'pagination' => [
            'pageSize' => 10, // 10 data per halaman
        ],
    ]);

    return $this->render('index', [
        'dataProvider' => $dataProvider,
    ]);
}


    // Tanggapan guru
    public function actionTanggapi($id)
    {
        $model = KejadianKhusus::findOne($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            Yii::$app->session->setFlash('success', 'Tanggapan berhasil disimpan.');
            return $this->redirect(['index']);
        }

        return $this->render('tanggapi', ['model' => $model]);
    }

    // Daftar pengajuan siswa
    public function actionMyPengajuan()
    {
        $siswaId = Yii::$app->user->id;
        $model = KejadianKhusus::find()
            ->where(['siswa_id' => $siswaId])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        return $this->render('my-pengajuan', ['model' => $model]);
    }

    // Detail pengajuan siswa
    public function actionView($id)
    {
        $model = KejadianKhusus::findOne($id);
        if ($model->siswa_id != Yii::$app->user->id) {
            throw new \yii\web\ForbiddenHttpException('Anda tidak memiliki akses.');
        }
        return $this->render('view', ['model' => $model]);
    }
}