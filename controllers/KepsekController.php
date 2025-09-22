<?php

namespace app\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;

class KepsekController extends Controller
{

    public function init()
    {
        parent::init();
        $this->layout = 'sidebar-kepsek'; // ✅ set layout
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
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return \Yii::$app->user->identity->role === 'kepsek';
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
        return $this->render('index');
    }
}