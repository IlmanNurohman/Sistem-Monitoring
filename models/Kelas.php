<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "kelas".
 *
 * @property int $id
 * @property string $nama_kelas
 * @property int $wali_guru_id
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property SiswaKelas[] $siswaKelas
 * @property User $waliGuru
 */
class Kelas extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kelas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at'], 'default', 'value' => null],
            [['nama_kelas', 'wali_guru_id'], 'required'],
            [['wali_guru_id', 'created_at', 'updated_at'], 'integer'],
            [['nama_kelas'], 'string', 'max' => 50],
            [['wali_guru_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['wali_guru_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nama_kelas' => 'Nama Kelas',
            'wali_guru_id' => 'Wali Guru ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[SiswaKelas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSiswaKelas()
    {
        return $this->hasMany(SiswaKelas::class, ['kelas_id' => 'id']);
    }

    /**
     * Gets query for [[WaliGuru]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWaliGuru()
    {
        return $this->hasOne(User::class, ['id' => 'wali_guru_id']);
    }

}
