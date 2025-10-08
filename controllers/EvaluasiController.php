<?php

namespace app\controllers;

use Yii;
use app\models\Evaluasi;
use app\models\EvaluasiSearch;
use app\models\SiswaKelas;
use app\models\Kelas;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EvaluasiController implements the CRUD actions for Evaluasi model.
 */
class EvaluasiController extends Controller
{
     public function init()
    {
        parent::init();
        $this->layout = 'sidebar-guru'; // ✅ set layout
    }
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Evaluasi models.
     * Guru hanya bisa lihat evaluasi untuk siswa dari kelas yang dia wakili
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;
        $kelas = Kelas::find()->where(['wali_guru_id' => $userId])->one();

        if (!$kelas) {
            throw new NotFoundHttpException('Anda bukan wali kelas mana pun.');
        }

        $searchModel = new EvaluasiSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        // Filter hanya siswa dari kelas guru tersebut
        $dataProvider->query->joinWith('siswaKelas')
            ->andWhere(['siswa_kelas.kelas_id' => $kelas->id]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Evaluasi model.
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Evaluasi model.
     * Guru hanya bisa membuat evaluasi untuk siswa di kelasnya
     */
    public function actionCreate()
    {
        $model = new Evaluasi();
        $userId = Yii::$app->user->id;
        $kelas = Kelas::find()->where(['wali_guru_id' => $userId])->one();

        if (!$kelas) {
            throw new NotFoundHttpException('Anda tidak memiliki kelas yang diampu.');
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->wali_guru_id = $userId;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Data evaluasi berhasil disimpan.');
                return $this->redirect(['index']);
            }
        }

        // Ambil daftar siswa di kelas wali guru
        $siswaList = SiswaKelas::find()
            ->where(['kelas_id' => $kelas->id])
            ->select(['nama', 'id'])
            ->indexBy('id')
            ->column();

        return $this->render('create', [
            'model' => $model,
            'siswaList' => $siswaList,
        ]);
    }

    /**
     * Updates an existing Evaluasi model.
     */
   public function actionUpdate($id)
{
    $model = $this->findModel($id);
    $userId = Yii::$app->user->id;
    $kelas = Kelas::find()->where(['wali_guru_id' => $userId])->one();

    // Cegah guru lain update data yang bukan dari kelasnya
    if ($kelas && $model->siswaKelas->kelas_id !== $kelas->id) {
        throw new NotFoundHttpException('Anda tidak berhak mengubah evaluasi ini.');
    }

    // Ambil daftar siswa dari kelas wali guru
    $siswaList = SiswaKelas::find()
        ->where(['kelas_id' => $kelas->id])
        ->select(['nama', 'id'])
        ->indexBy('id')
        ->column();

    if ($model->load(Yii::$app->request->post()) && $model->save()) {
        Yii::$app->session->setFlash('success', 'Data evaluasi berhasil diperbarui.');
        return $this->redirect(['index']);
    }

    return $this->render('update', [
        'model' => $model,
        'siswaList' => $siswaList, // 🔥 tambahkan ini
    ]);
}


    /**
     * Deletes an existing Evaluasi model.
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $userId = Yii::$app->user->id;
        $kelas = Kelas::find()->where(['wali_guru_id' => $userId])->one();

        // Cegah guru hapus data siswa dari kelas lain
        if ($kelas && $model->siswaKelas->kelas_id !== $kelas->id) {
            throw new NotFoundHttpException('Anda tidak berhak menghapus evaluasi ini.');
        }

        $model->delete();
        Yii::$app->session->setFlash('success', 'Data evaluasi berhasil dihapus.');
        return $this->redirect(['index']);
    }

    /**
     * Finds the Evaluasi model based on its primary key value.
     * @param int $id
     * @return Evaluasi
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Evaluasi::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Data evaluasi tidak ditemukan.');
    }
}