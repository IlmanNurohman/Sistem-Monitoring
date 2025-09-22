<?php

namespace app\models;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

use Yii;

/**
 * This is the model class for table "chat_messages".
 *
 * @property int $id
 * @property int $sender_id
 * @property int $receiver_id
 * @property string $message
 * @property string|null $created_at
 * @property int|null $is_read
 */
class ChatMessages extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chat_messages';
    }
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false, // tidak ada kolom updated_at
                'value' => function () {
                    return date('Y-m-d H:i:s'); // otomatis isi datetime
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['is_read'], 'default', 'value' => 0],
            [['sender_id', 'receiver_id', 'message'], 'required'],
            [['sender_id', 'receiver_id', 'is_read'], 'integer'],
            [['message'], 'string'],
            [['created_at'], 'safe'],
            [['is_deleted_for_all'], 'boolean'],
[['deleted_for_user'], 'safe'],

        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sender_id' => 'Sender ID',
            'receiver_id' => 'Receiver ID',
            'message' => 'Message',
            'created_at' => 'Created At',
            'is_read' => 'Is Read',
        ];
    }

    public static function formatTime($datetime)
{
    $time = strtotime($datetime);
    $today = strtotime(date('Y-m-d'));
    $yesterday = strtotime('-1 day', $today);

    if ($time >= $today) {
        // Hari ini → tampil jam (08:30)
        return date('H:i', $time);
    } elseif ($time >= $yesterday) {
        // Kemarin
        return 'Kemarin';
    } else {
        // Lebih lama → tampil tanggal (dd/mm)
        return date('d/m', $time);
    }
}
}