<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public $file_foto;
    public static function tableName()
    {
        return 'user';
    }
    public $new_password;

    public function rules()
    {
        return [
            [['auth_key', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 10],
            [['username', 'email', 'password_hash', 'role'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
 [['username', 'email', 'password_hash', 'role', 'foto'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
             [['nisn'], 'unique', 'message' => 'NISN sudah terdaftar.'],
               [['new_password'], 'string', 'min' => 6], // minimal 6 karakter

             [['file_foto'], 'file',
    'skipOnEmpty' => true,
    'extensions' => ['png', 'jpg', 'jpeg'],
    'mimeTypes' => ['image/jpeg', 'image/png'],
    'maxSize' => 2 * 1024 * 1024, // 2MB
    'tooBig' => 'Ukuran foto maksimal 2MB',
],

        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'email' => 'Email',
            'password_hash' => 'Password Hash',
            'new_password' => 'Password Baru',
            'auth_key' => 'Auth Key',
            'role' => 'Role',
            'status' => 'Status',
             'nisn' => 'NISN',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function uploadFoto()
{
    if ($this->file_foto) {
        $fileName = 'user_' . $this->id . '_' . time() . '.' . $this->file_foto->extension;
        $path = \Yii::getAlias('@webroot/uploads/' . $fileName);

        if ($this->file_foto->saveAs($path)) {
            // hapus foto lama kalau ada
            if ($this->foto && file_exists(\Yii::getAlias('@webroot/uploads/' . $this->foto))) {
                unlink(\Yii::getAlias('@webroot/uploads/' . $this->foto));
            }
            $this->foto = $fileName;
            return true;
        }
    }
    return false;
}

    /* ==================== Tambahan penting ==================== */

    // Hashing password
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    // Validasi password saat login
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    // Generate auth key
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /* ==================== IdentityInterface ==================== */

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token]);
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    public function getSiswaKelas()
{
    return $this->hasOne(SiswaKelas::class, ['user_id' => 'id']);
}

public function getKelas()
{
    return $this->hasOne(Kelas::class, ['id' => 'kelas_id'])
        ->via('siswaKelas');
}


}