<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use app\models\Evaluasi;
use app\models\SiswaKelas;

class EvaluasiSiswaController extends Controller
{
    public function init()
    {
        parent::init();
        $this->layout = 'sidebar-siswa'; // ✅ Layout khusus untuk siswa (opsional)
    }

    /**
     * Menampilkan daftar hasil evaluasi untuk siswa yang sedang login.
     */
    public function actionIndex()
    {
        $userId = Yii::$app->user->id;

        // Ambil data siswa berdasarkan user login
        $siswa = SiswaKelas::find()->where(['user_id' => $userId])->one();

        // Jika siswa belum terdaftar di tabel siswa_kelas
        if (!$siswa) {
            throw new NotFoundHttpException('Data siswa tidak ditemukan.');
        }

        // Ambil semua evaluasi berdasarkan siswa_kelas_id
        $evaluasi = Evaluasi::find()
            ->where(['siswa_kelas_id' => $siswa->id])
            ->orderBy(['tanggal' => SORT_DESC])
            ->all();

        // Tampilkan ke view
        return $this->render('index', [
            'evaluasi' => $evaluasi,
            'siswa' => $siswa,
        ]);
    }
}