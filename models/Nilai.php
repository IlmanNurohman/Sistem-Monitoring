<?php 
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Nilai extends ActiveRecord
{
    public static function tableName()
    {
        return 'nilai';
    }
    public $excelFile;

   public function rules()
{
    return [
        [['user_id', 'mapel', 'jenis_nilai', 'tanggal', 'nilai', 'semester'], 'required'],
        [['user_id', 'nilai', 'semester'], 'integer'],
        [['tanggal'], 'safe'],
        [['mapel'], 'string', 'max' => 100],
        [['jenis_nilai'], 'in', 'range' => ['harian', 'uts', 'uas', 'akhir']],
        [['semester'], 'in', 'range' => [1, 2]], // 🔥 semester hanya 1 atau 2
        [['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        [['excelFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'xls, xlsx'],
    ];
}

public function attributeLabels()
{
    return [
        'id' => 'ID',
        'user_id' => 'Siswa',
        'mapel' => 'Mata Pelajaran',
        'jenis_nilai' => 'Jenis Nilai',
        'tanggal' => 'Tanggal',
        'nilai' => 'Nilai',
        'semester' => 'Semester', // 🔥 label baru
    ];
}

    public function getUser()
{
    return $this->hasOne(User::class, ['id' => 'user_id']);
}

public function getSiswaKelas()
{
    return $this->hasOne(SiswaKelas::class, ['user_id' => 'user_id']);
}

}