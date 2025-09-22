<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%nilai}}`.
 */
class m250825_033234_create_nilai_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('nilai', [
        'id' => $this->primaryKey(),
        'user_id' => $this->integer()->notNull(), // relasi ke siswa
        'mapel' => $this->string(100)->notNull(),
        'jenis_nilai' => $this->string(20)->notNull(), // harian, uts, uas, akhir
        'tanggal' => $this->date()->notNull(),
        'nilai' => $this->integer()->notNull(),
        'created_at' => $this->integer(),
        'updated_at' => $this->integer(),
    ]);

    $this->addForeignKey(
        'fk_nilai_user',
        'nilai', 'user_id',
        'user', 'id',
        'CASCADE'
    );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_nilai_user', 'nilai');
    $this->dropTable('nilai');
    }
}