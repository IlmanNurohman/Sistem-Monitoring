<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "evaluasi".
 *
 * @property int $id
 * @property int $siswa_kelas_id
 * @property int $wali_guru_id
 * @property string $aspek
 * @property string $nilai
 * @property string|null $keterangan
 * @property string $tanggal
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property SiswaKelas $siswaKelas
 * @property Kelas $waliGuru
 */
class Evaluasi extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const NILAI_A = 'A';
    const NILAI_B = 'B';
    const NILAI_C = 'C';
    const NILAI_D = 'D';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'evaluasi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['keterangan', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['siswa_kelas_id', 'wali_guru_id', 'aspek', 'nilai', 'tanggal'], 'required'],
            [['siswa_kelas_id', 'wali_guru_id', 'created_at', 'updated_at'], 'integer'],
            [['nilai', 'keterangan'], 'string'],
            [['tanggal'], 'safe'],
            [['aspek'], 'string', 'max' => 100],
            ['nilai', 'in', 'range' => array_keys(self::optsNilai())],
            [['siswa_kelas_id'], 'exist', 'skipOnError' => true, 'targetClass' => SiswaKelas::class, 'targetAttribute' => ['siswa_kelas_id' => 'id']],
            [['wali_guru_id'], 'exist', 'skipOnError' => true, 'targetClass' => Kelas::class, 'targetAttribute' => ['wali_guru_id' => 'wali_guru_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'siswa_kelas_id' => 'Siswa Kelas ID',
            'wali_guru_id' => 'Wali Guru ID',
            'aspek' => 'Aspek',
            'nilai' => 'Nilai',
            'keterangan' => 'Keterangan',
            'tanggal' => 'Tanggal',
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
        return $this->hasOne(SiswaKelas::class, ['id' => 'siswa_kelas_id']);
    }

    /**
     * Gets query for [[WaliGuru]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWaliGuru()
    {
        return $this->hasOne(Kelas::class, ['wali_guru_id' => 'wali_guru_id']);
    }


    /**
     * column nilai ENUM value labels
     * @return string[]
     */
    public static function optsNilai()
    {
        return [
            self::NILAI_A => 'A',
            self::NILAI_B => 'B',
            self::NILAI_C => 'C',
            self::NILAI_D => 'D',
        ];
    }

    /**
     * @return string
     */
    public function displayNilai()
    {
        return self::optsNilai()[$this->nilai];
    }

    /**
     * @return bool
     */
    public function isNilaiA()
    {
        return $this->nilai === self::NILAI_A;
    }

    public function setNilaiToA()
    {
        $this->nilai = self::NILAI_A;
    }

    /**
     * @return bool
     */
    public function isNilaiB()
    {
        return $this->nilai === self::NILAI_B;
    }

    public function setNilaiToB()
    {
        $this->nilai = self::NILAI_B;
    }

    /**
     * @return bool
     */
    public function isNilaiC()
    {
        return $this->nilai === self::NILAI_C;
    }

    public function setNilaiToC()
    {
        $this->nilai = self::NILAI_C;
    }

    /**
     * @return bool
     */
    public function isNilaiD()
    {
        return $this->nilai === self::NILAI_D;
    }

    public function setNilaiToD()
    {
        $this->nilai = self::NILAI_D;
    }
    
}