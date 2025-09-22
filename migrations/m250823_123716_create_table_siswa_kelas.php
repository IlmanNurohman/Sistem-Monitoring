<?php

use yii\db\Migration;

class m250823_123716_create_table_siswa_kelas extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->createTable('siswa_kelas', [
        'id' => $this->primaryKey(),
        'kelas_id' => $this->integer()->notNull(),
        'user_id' => $this->integer()->notNull(),
        'nama' => $this->string(100),
        'jk' => "ENUM('L','P')",
        'tanggal_lahir' => $this->date(),
        'alamat' => $this->text(),
        'nisn' => $this->string(50),
    ]);

    $this->addForeignKey(
        'fk_siswa_kelas_kelas',
        'siswa_kelas', 'kelas_id',
        'kelas', 'id',
        'CASCADE', 'CASCADE'
    );

    $this->addForeignKey(
        'fk_siswa_kelas_user',
        'siswa_kelas', 'user_id',
        'user', 'id',
        'CASCADE', 'CASCADE'
    );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
         $this->dropForeignKey('fk_siswa_kelas_kelas', 'siswa_kelas');
    $this->dropForeignKey('fk_siswa_kelas_user', 'siswa_kelas');
    $this->dropTable('siswa_kelas');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250823_123716_create_table_siswa_kelas cannot be reverted.\n";

        return false;
    }
    */
}