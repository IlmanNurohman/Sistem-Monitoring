<?php
use yii\helpers\Url;

$this->title = 'Live Chat Siswa & Guru';
?>
<div class="container" style="padding-top: 20px;">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Layanan</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="#">
                        <i class="bi bi-envelope"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="bi bi-chevron-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Pesan</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="  col-mb-12">
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-head-row card-tools-still-right">
                            <div class="card-title"> Pesan</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="  col-mb-12">

                                <div class="container mt-4">
                                    <div class="card">

                                        <div class="card-body" id="chat-box"
                                            style="height:400px; overflow-y:auto; background:#e5ddd5;">
                                            <!-- Pesan akan muncul di sini -->
                                        </div>
                                        <div class="card-footer d-flex">
                                            <input type="text" id="chat-message" class="form-control me-2"
                                                placeholder="Tulis pesan...">
                                            <button class="btn btn-primary" id="send-btn"><i class="bi bi-send"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .chat-bubble {
        display: inline-block;
        padding: 8px 45px 16px 12px;
        /* kanan dikasih ruang buat jam + status */
        border-radius: 15px;
        max-width: 75%;
        position: relative;
        word-wrap: break-word;
        margin-bottom: 6px;
        font-size: 0.95rem;
    }

    /* Jam + status masuk bubble (pojok kanan bawah) */
    .chat-meta {
        font-size: 0.7rem;
        position: absolute;
        bottom: 3px;
        right: 8px;
        display: flex;
        gap: 4px;
        align-items: center;
    }

    .chat-time {
        color: rgba(0, 0, 0, 0.6);
    }

    .chat-status {
        font-size: 0.8rem;
    }

    /* Bubble pengirim (siswa/yang login) */
    .chat-bubble.me {
        background: #fff;
        /* putih */
        color: #000;
        /* teks hitam */
        border-bottom-right-radius: 3px;
    }

    .chat-bubble.me::after {
        content: "";
        position: absolute;
        right: -8px;
        bottom: 0;
        width: 0;
        height: 0;
        border-left: 10px solid #fff;
        /* putih */
        border-top: 10px solid transparent;
    }

    /* Centang status */
    .chat-status {
        font-size: 0.8rem;
    }

    .chat-status.single {
        color: rgba(0, 0, 0, 0.5);
        /* abu-abu */
    }

    .chat-status.double {
        color: #0d6efd;
        /* biru */
    }

    /* Bubble penerima (guru) */
    .chat-bubble.other {
        background: #fff;
        /* putih */
        color: #000;
        border-bottom-left-radius: 3px;
    }

    .chat-bubble.other::after {
        content: "";
        position: absolute;
        left: -8px;
        bottom: 0;
        width: 0;
        height: 0;
        border-right: 10px solid #fff;
        border-top: 10px solid transparent;
    }
    </style>

    <?php
$sendUrl = Url::to(['chat/send']);
$fetchUrl = Url::to(['chat/fetch', 'receiver_id'=>$receiverId]);
$readUrl = Url::to(['chat/mark-read', 'sender_id'=>$receiverId]);
$csrf = Yii::$app->request->csrfToken;

$js = <<<JS
function fetchMessages() {
    $.get('{$fetchUrl}', function(data) {
        var chatBox = $('#chat-box');
        chatBox.html('');
        data.forEach(msg => {
            var isReceiver = (msg.sender_id == {$receiverId});
            var align = isReceiver ? 'text-start' : 'text-end';
            var role = isReceiver ? 'other' : 'me';

            // Format jam
            var time = '';
            if (msg.created_at) {
                var dateObj = new Date(msg.created_at);
                var hours = dateObj.getHours().toString().padStart(2, '0');
                var minutes = dateObj.getMinutes().toString().padStart(2, '0');
                time = hours + ':' + minutes;
            }

            // Status centang (hanya untuk pesan kita)
            // Status centang (hanya untuk pesan kita)
var status = '';
if(!isReceiver) {
    if (msg.is_read == 1) {
        status = '<span class="chat-status double">✓✓</span>'; // biru
    } else {
        status = '<span class="chat-status single">✓</span>';  // abu-abu
    }
}


            var bubble = `
                <div class="\${align}">
                    <div class="chat-bubble \${role}">
                        \${msg.message}
                        <div class="chat-meta">
                            <span class="chat-time">\${time}</span>
                            \${status}
                        </div>
                    </div>
                </div>
            `;
            chatBox.append(bubble);
        });
        chatBox.scrollTop(chatBox[0].scrollHeight);

        // Tandai pesan guru sebagai sudah dibaca
        $.post('{$readUrl}', {_csrf: '{$csrf}'});
    });
}

// Polling setiap 3 detik
setInterval(fetchMessages, 3000);

// Kirim pesan
$('#send-btn').on('click', function() {
    var message = $('#chat-message').val();
    if(message.trim() != '') {
        $.post('{$sendUrl}', {
            receiver_id: {$receiverId},
            message: message,
            _csrf: '{$csrf}'
        }, function(res){
            if(res.status=='success'){
                $('#chat-message').val('');
                fetchMessages();
            } else {
                alert(res.message || 'Gagal mengirim pesan');
            }
        });
    }
});

// Tekan Enter untuk kirim
$('#chat-message').on('keypress', function(e) {
    if(e.which === 13) {
        $('#send-btn').click();
        return false;
    }
});

// Load awal
fetchMessages();
JS;

$this->registerJs($js);
?>