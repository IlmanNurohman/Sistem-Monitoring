<?php

use yii\db\Migration;

class m250823_123647_create_table_kelas extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->createTable('kelas', [
        'id' => $this->primaryKey(),
        'nama_kelas' => $this->string(50)->notNull(),
        'wali_guru_id' => $this->integer()->notNull(),
        'created_at' => $this->integer(),
        'updated_at' => $this->integer(),
    ]);

    $this->addForeignKey(
        'fk_kelas_wali_guru',
        'kelas', 'wali_guru_id',
        'user', 'id',
        'CASCADE', 'CASCADE'
    );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_kelas_wali_guru', 'kelas');
    $this->dropTable('kelas');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250823_123647_create_table_kelas cannot be reverted.\n";

        return false;
    }
    */
}