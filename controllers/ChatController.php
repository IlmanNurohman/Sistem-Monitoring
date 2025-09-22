<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\ChatMessages;
use app\models\User;
use yii\web\Response;

class ChatController extends Controller
{
    public function init()
    {
        parent::init();

        // Set layout sesuai role
        if (!Yii::$app->user->isGuest) {
            if (Yii::$app->user->identity->role === 'siswa') {
                $this->layout = 'sidebar-siswa';
            } elseif (Yii::$app->user->identity->role === 'guru') {
                $this->layout = 'sidebar-guru';
            }
        }
    }

   public function actionList()
{
    $user = Yii::$app->user->identity;
    if (!$user) {
        throw new \yii\web\ForbiddenHttpException('User belum login');
    }

    if ($user->role !== 'guru') {
        return $this->redirect(['index']);
    }

    // Ambil kelas yang dia walikan
    $kelas = \app\models\Kelas::find()->where(['wali_guru_id' => $user->id])->one();
    if (!$kelas) {
        throw new \yii\web\ForbiddenHttpException('Guru tidak punya kelas');
    }

    // Ambil semua siswa di kelas itu
    $siswaKelas = \app\models\SiswaKelas::find()
        ->with('user')
        ->where(['kelas_id' => $kelas->id])
        ->all();

    $chatList = [];
    foreach ($siswaKelas as $sk) {
        if ($sk->user) {
            // Ambil pesan terakhir
            $lastMessage = \app\models\ChatMessages::find()
                ->where(['or',
                    ['sender_id' => $sk->user->id, 'receiver_id' => $user->id],
                    ['sender_id' => $user->id, 'receiver_id' => $sk->user->id]
                ])
                ->orderBy(['created_at' => SORT_DESC])
                ->one();

            // Hitung unread (pesan siswa -> guru yang belum dibaca)
            $unread = \app\models\ChatMessages::find()
                ->where([
                    'sender_id' => $sk->user->id,
                    'receiver_id' => $user->id,
                    'is_read' => 0
                ])
                ->count();

            if ($lastMessage) {
                $chatList[] = [
                    'user' => $sk->user,
                    'lastMessage' => $lastMessage,
                    'unread' => $unread,
                ];
            }
        }
    }

    // Urutkan berdasarkan created_at pesan terakhir (desc)
    usort($chatList, function($a, $b) {
        return strtotime($b['lastMessage']->created_at) - strtotime($a['lastMessage']->created_at);
    });

    return $this->render('list', [
        'chatList' => $chatList
    ]);
}

public function actionIndex($receiverId = null)
{
    $user = Yii::$app->user->identity;
    if (!$user) {
        throw new \yii\web\ForbiddenHttpException('User belum login');
    }

    // Kalau siswa → langsung ke wali
    if ($user->role === 'siswa') {
        $siswaKelas = $user->siswaKelas;
        if ($siswaKelas && $siswaKelas->kelas && $siswaKelas->kelas->waliGuru) {
            $receiverId = $siswaKelas->kelas->waliGuru->id;
        } else {
            throw new \yii\web\ForbiddenHttpException('Wali guru tidak ditemukan');
        }
    } elseif ($user->role === 'guru') {
        // Kalau guru tapi tidak pilih siswa → arahkan ke daftar chat
        if (!$receiverId) {
            return $this->redirect(['list']);
        }
    }

    return $this->render('index', [
        'receiverId' => $receiverId
    ]);
}


    // Kirim pesan
    public function actionSend()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $user = Yii::$app->user->identity;
        if(!$user){
            return ['status'=>'error','message'=>'User belum login'];
        }

        $receiver_id = Yii::$app->request->post('receiver_id');
        $text = Yii::$app->request->post('message');

        if(!$receiver_id || !$text){
            return ['status'=>'error','message'=>'Data tidak lengkap'];
        }

        $receiver = User::findOne($receiver_id);
        if(!$receiver){
            return ['status'=>'error','message'=>'Receiver tidak ditemukan'];
        }

        // Validasi role (siswa hanya bisa ke guru, guru hanya bisa ke siswa)
        if(($user->role=='siswa' && $receiver->role!='guru') 
           || ($user->role=='guru' && $receiver->role!='siswa')){
            return ['status'=>'error','message'=>'Role tidak sesuai'];
        }

        $message = new ChatMessages();
        $message->sender_id = $user->id;
        $message->receiver_id = $receiver_id;
        $message->message = $text;

        if($message->save()){
             $this->sendPushNotification($receiver_id, $text);
            return ['status'=>'success'];
        }
        return ['status'=>'error','errors'=>$message->errors];
    }

    // Ambil pesan
    public function actionFetch($receiver_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $user = Yii::$app->user->identity;
        if(!$user){
            return [];
        }

        $receiver = User::findOne($receiver_id);
        if(!$receiver){
            return [];
        }

        $messages = ChatMessages::find()
            ->where(['sender_id'=>$user->id,'receiver_id'=>$receiver_id])
            ->orWhere(['sender_id'=>$receiver_id,'receiver_id'=>$user->id])
            ->orderBy('created_at ASC')
            ->asArray()
            ->all();

        return $messages;
    }

    public function actionMarkRead($sender_id)
{
    \app\models\ChatMessages::updateAll(['is_read' => 1], [
        'sender_id' => $sender_id,
        'receiver_id' => Yii::$app->user->id,
        'is_read' => 0
    ]);
    return $this->asJson(['status' => 'success']);
}

private function sendPushNotification($userId, $messageText)
{
    $user = User::findOne($userId);
    if (!$user || !$user->onesignal_player_id) {
        return;
    }

    $content = ["en" => $messageText];

    $fields = [
         'app_id' => '827ae35c-f117-4c67-8fdb-f73618f29245',
        'include_player_ids' => [$user->onesignal_player_id],
        'contents' => $content,
        'headings' => ["en" => "Pesan Baru"],
    ];

    $fields = json_encode($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json; charset=utf-8',
        'Authorization: os_v2_app_qj5ogxhrc5ggpd63643br4usiv22fi7aq5ietzftnvyog53vjpyklrkw7rcx263xh7nlaxs2empcgmdjyz4jfvetjn6u2tqbh7xdsny'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);
}

public function actionSavePlayerId()
{
    Yii::$app->response->format = Response::FORMAT_JSON;
    $user = Yii::$app->user->identity;

    if (!$user) {
        return ['status' => 'error', 'message' => 'Belum login'];
    }

    $data = json_decode(Yii::$app->request->getRawBody(), true);
    if (empty($data['player_id'])) {
        return ['status' => 'error', 'message' => 'Player ID kosong'];
    }

    $user->player_id = $data['player_id'];
    if ($user->save(false, ['player_id'])) {
        return ['status' => 'success'];
    }

    return ['status' => 'error', 'message' => 'Gagal simpan player_id'];
}


}