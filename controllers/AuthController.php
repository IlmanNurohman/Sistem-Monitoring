<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\LoginForm;
use app\models\RegisterForm;
use yii\filters\VerbFilter;

class AuthController extends Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'], // ✅ logout hanya lewat POST
                ],
            ],
        ];
    }
    
   public function actionLogin()
{
    $model = new LoginForm();

    if ($model->load(Yii::$app->request->post())) {
        if ($model->login()) {
            Yii::$app->session->setFlash('loginSuccess', 'Login berhasil!');
            $role = Yii::$app->user->identity->role;
            switch ($role) {
                case 'siswa':
                    return $this->redirect(['siswa/index']);
                case 'guru':
                    return $this->redirect(['guru/index']);
                case 'kepsek':
                    return $this->redirect(['kepsek/index']);
                case 'admin':
                    return $this->redirect(['admin/index']);
            }
        } else {
            Yii::$app->session->setFlash('loginError', 'Username atau password salah.');
        }
    }

    return $this->render('login', ['model' => $model]);
}

    public function actionLogout()
{
    Yii::$app->user->logout(false); // false = jangan hapus session seluruhnya
    Yii::$app->session->destroy(); // kalau mau bener-bener clear session
    return $this->redirect(['/auth/login']); 
}

    public function actionRegister()
    {
        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post()) && $user = $model->register()) {
            Yii::$app->user->login($user);
            return $this->redirect(['siswa/index']);
        }
        return $this->render('register', ['model' => $model]);
    }
}