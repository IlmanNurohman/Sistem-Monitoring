<?php
namespace app\commands;

use yii\console\Controller;
use Yii;

class HashController extends Controller
{
    public function actionGenerate($password)
    {
        $hash = Yii::$app->security->generatePasswordHash($password);
        echo "Password: $password\nHash: $hash\n";
    }
}