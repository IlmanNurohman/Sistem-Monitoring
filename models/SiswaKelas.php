<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "siswa_kelas".
 *
 * @property int $id
 * @property int $kelas_id
 * @property int $user_id
 * @property string|null $nama
 * @property string|null $jk
 * @property string|null $tanggal_lahir
 * @property string|null $alamat
 * @property string|null $nisn
 *
 * @property Kelas $kelas
 * @property User $user
 */
class SiswaKelas extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const JK_L = 'L';
    const JK_P = 'P';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'siswa_kelas';
    }

    /**
     * {@inheritdoc}
     */
   public function rules()
{
    return [
        [['kelas_id'], 'required'], // hanya kelas_id yang wajib
        [['kelas_id', 'user_id'], 'integer'],
        [['nama', 'jk', 'tanggal_lahir', 'alamat', 'nisn'], 'safe'],
        ['jk', 'in', 'range' => array_keys(self::optsJk())],
        [['kelas_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kelas::class, 'targetAttribute' => ['kelas_id' => 'id']],
        [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['id' => 'user_id']],
    ];
}


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kelas_id' => 'Kelas ID',
            'user_id' => 'User ID',
            'nama' => 'Nama',
            'jk' => 'Jk',
            'tanggal_lahir' => 'Tanggal Lahir',
            'alamat' => 'Alamat',
            'nisn' => 'Nisn',
        ];
    }

    /**
     * Gets query for [[Kelas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKelas()
    {
        return $this->hasOne(Kelas::class, ['id' => 'kelas_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }


    /**
     * column jk ENUM value labels
     * @return string[]
     */
    public static function optsJk()
    {
        return [
            self::JK_L => 'L',
            self::JK_P => 'P',
        ];
    }

    /**
     * @return string
     */
    public function displayJk()
    {
        return self::optsJk()[$this->jk];
    }

    /**
     * @return bool
     */
    public function isJkL()
    {
        return $this->jk === self::JK_L;
    }

    public function setJkToL()
    {
        $this->jk = self::JK_L;
    }

    /**
     * @return bool
     */
    public function isJkP()
    {
        return $this->jk === self::JK_P;
    }

    public function setJkToP()
    {
        $this->jk = self::JK_P;
    }
}