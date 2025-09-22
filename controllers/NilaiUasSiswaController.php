<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use app\models\Nilai;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

class NilaiUasSiswaController extends \yii\web\Controller
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
                'only' => ['index', 'kelas-saya'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
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
        // ambil user_id siswa yang sedang login
        $userId = Yii::$app->user->id;

        // query nilai hanya milik siswa tersebut
        $query = Nilai::find()
            ->where(['user_id' => $userId]);

        // filter jenis_nilai (misal harian/uts/uas)
        $jenis = Yii::$app->request->get('jenis_nilai');
        if (!empty($jenis)) {
            $query->andWhere(['jenis_nilai' => $jenis]);
        }

        // filter mapel kalau mau
        $mapel = Yii::$app->request->get('mapel');
        if (!empty($mapel)) {
            $query->andWhere(['mapel' => $mapel]);
        }

        // bungkus ke dataProvider
        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['tanggal' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'jenis' => $jenis,
            'mapel' => $mapel,
        ]);
    }


}