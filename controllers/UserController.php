<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class UserController extends Controller
{
    // Tampilkan semua user
    public function actionIndex()
    {

        // Atur layout sesuai role
    if (!Yii::$app->user->isGuest) {
        if (Yii::$app->user->identity->role === 'siswa') {
            $this->layout = 'sidebar-siswa';
        } elseif (Yii::$app->user->identity->role === 'guru') {
            $this->layout = 'sidebar-guru';
        } elseif (Yii::$app->user->identity->role === 'admin') {
            $this->layout = 'sidebar-admin';
        }
    }
        $users = User::find()->all();
        return $this->render('index', [
            'users' => $users
        ]);
    }

    // Tambah user
    // Tambah user
public function actionCreate()
{
     // Atur layout sesuai role
    if (!Yii::$app->user->isGuest) {
        if (Yii::$app->user->identity->role === 'siswa') {
            $this->layout = 'sidebar-siswa';
        } elseif (Yii::$app->user->identity->role === 'guru') {
            $this->layout = 'sidebar-guru';
        } elseif (Yii::$app->user->identity->role === 'admin') {
            $this->layout = 'sidebar-admin';
        }
    }
    $model = new User();

    if ($model->load(Yii::$app->request->post())) {
        $model->file_foto = UploadedFile::getInstance($model, 'file_foto');

        if ($model->validate()) {
            if ($model->file_foto) {
                $namaFile = uniqid() . '.' . $model->file_foto->extension;
                $path = Yii::getAlias('@webroot/uploads/') . $namaFile;

                if ($model->file_foto->saveAs($path)) {
                    $model->foto = $namaFile;
                }
            }

            // hash password
            $model->setPassword($model->password_hash);
            $model->generateAuthKey();
            $model->created_at = time();
            $model->updated_at = time();

            if ($model->save(false)) {
                return $this->redirect(['index']);
            }
        }
    }

    return $this->render('create', [
        'model' => $model
    ]);
}


// Edit user
public function actionUpdate($id)
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
    $model = $this->findModel($id);

    if ($model->load(Yii::$app->request->post())) {
        $model->file_foto = UploadedFile::getInstance($model, 'file_foto');

        if ($model->validate()) {
            if ($model->file_foto) {
                $namaFile = uniqid() . '.' . $model->file_foto->extension;
                $path = Yii::getAlias('@webroot/uploads/') . $namaFile;

                if ($model->file_foto->saveAs($path)) {
                    $model->foto = $namaFile;
                }
            }

            // kalau password diganti
            if (!empty($model->password_hash)) {
                $model->setPassword($model->password_hash);
            } else {
                $model->password_hash = $model->getOldAttribute('password_hash');
            }

            $model->updated_at = time();

            if ($model->save(false)) {
                return $this->redirect(['index']);
            }
        }
    }

    return $this->render('update', [
        'model' => $model
    ]);
}



    // Hapus user
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    // Cari model
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('User tidak ditemukan.');
    }
public function actionProfile()
{
    // Atur layout sesuai role
    if (!Yii::$app->user->isGuest) {
        if (Yii::$app->user->identity->role === 'siswa') {
            $this->layout = 'sidebar-siswa';
        } elseif (Yii::$app->user->identity->role === 'guru') {
            $this->layout = 'sidebar-guru';
        } elseif (Yii::$app->user->identity->role === 'admin') {
            $this->layout = 'sidebar-admin';
        }
    }

    $model = $this->findModel(Yii::$app->user->id);

    // Jalankan hanya saat form disubmit
    if ($model->load(Yii::$app->request->post())) {
        $model->file_foto = \yii\web\UploadedFile::getInstance($model, 'file_foto');

        // Update foto
        if ($model->file_foto && $model->uploadFoto()) {
            $model->save(false);
            Yii::$app->session->setFlash('success', 'Profil berhasil diperbarui.');
        } else {
            // Update password jika ada perubahan
            if (!empty($model->new_password)) {
                $model->setPassword($model->new_password);
            }

            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Profil berhasil diperbarui.');
            } else {
                Yii::$app->session->setFlash('error', 'Gagal memperbarui profil!');
            }
        }

        return $this->refresh(); // hanya refresh setelah ada submit
    }

    // Saat hanya membuka halaman (tanpa submit), tidak set flash apa pun
    return $this->render('profile', [
        'model' => $model,
    ]);
}



}