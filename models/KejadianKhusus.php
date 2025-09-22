<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "kejadian_khusus".
 *
 * @property int $id
 * @property int $siswa_id
 * @property string $tipe
 * @property string|null $keterangan
 * @property string|null $status
 * @property string|null $tanggapan_guru
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User $siswa
 */
class KejadianKhusus extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const TIPE_IJIN = 'ijin';
    const TIPE_IZIN_SAKIT = 'izin sakit';
    const STATUS_PENDING = 'pending';
    const STATUS_DITERIMA = 'diterima';
    const STATUS_DITOLAK = 'ditolak';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kejadian_khusus';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['keterangan', 'tanggapan_guru'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'pending'],
            [['siswa_id', 'tipe'], 'required'],
            [['siswa_id'], 'integer'],
            [['tipe', 'keterangan', 'status', 'tanggapan_guru'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            ['tipe', 'in', 'range' => array_keys(self::optsTipe())],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
            [['siswa_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['siswa_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'siswa_id' => 'Siswa ID',
            'tipe' => 'Tipe',
            'keterangan' => 'Keterangan',
            'status' => 'Status',
            'tanggapan_guru' => 'Tanggapan Guru',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Siswa]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSiswa()
    {
        return $this->hasOne(User::class, ['id' => 'siswa_id']);
    }


    /**
     * column tipe ENUM value labels
     * @return string[]
     */
    public static function optsTipe()
    {
        return [
            self::TIPE_IJIN => 'ijin',
            self::TIPE_IZIN_SAKIT => 'izin sakit',
        ];
    }

    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_PENDING => 'pending',
            self::STATUS_DITERIMA => 'diterima',
            self::STATUS_DITOLAK => 'ditolak',
        ];
    }

    /**
     * @return string
     */
    public function displayTipe()
    {
        return self::optsTipe()[$this->tipe];
    }

    /**
     * @return bool
     */
    public function isTipeIjin()
    {
        return $this->tipe === self::TIPE_IJIN;
    }

    public function setTipeToIjin()
    {
        $this->tipe = self::TIPE_IJIN;
    }

    /**
     * @return bool
     */
    public function isTipeIzinSakit()
    {
        return $this->tipe === self::TIPE_IZIN_SAKIT;
    }

    public function setTipeToIzinSakit()
    {
        $this->tipe = self::TIPE_IZIN_SAKIT;
    }

    /**
     * @return string
     */
    public function displayStatus()
    {
        return self::optsStatus()[$this->status];
    }

    /**
     * @return bool
     */
    public function isStatusPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function setStatusToPending()
    {
        $this->status = self::STATUS_PENDING;
    }

    /**
     * @return bool
     */
    public function isStatusDiterima()
    {
        return $this->status === self::STATUS_DITERIMA;
    }

    public function setStatusToDiterima()
    {
        $this->status = self::STATUS_DITERIMA;
    }

    /**
     * @return bool
     */
    public function isStatusDitolak()
    {
        return $this->status === self::STATUS_DITOLAK;
    }

    public function setStatusToDitolak()
    {
        $this->status = self::STATUS_DITOLAK;
    }

    public function getSiswaKelas() {
    return $this->hasOne(SiswaKelas::class, ['user_id' => 'siswa_id']);
}

}