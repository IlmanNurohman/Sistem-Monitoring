<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class RegisterForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $file_foto;

    public function rules()
    {
        return [
            [['username', 'email', 'password'], 'required'],
            ['email', 'email'],
            [['username', 'email'], 'unique', 'targetClass' => '\app\models\User'],
            [['file_foto'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
        ];
    }

    public function register()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->role = 'siswa';

            // handle upload foto
            $this->file_foto = UploadedFile::getInstance($this, 'file_foto');
            if ($this->file_foto) {
                $fileName = 'user_' . time() . '.' . $this->file_foto->extension;
                $path = Yii::getAlias('@webroot/uploads/') . $fileName;
                if ($this->file_foto->saveAs($path)) {
                    $user->foto = 'uploads/' . $fileName;
                }
            }

            return $user->save() ? $user : null;
        }
        return null;
    }
}