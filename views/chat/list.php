<?php
use yii\helpers\Url;


/** @var $chatList array */

$this->title = 'Daftar Chat Siswa';
?>

<div class="container" style="padding-top: 20px;">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Pembelajaran</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="#">
                        <i class="bi bi-exclamation-triangle"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="bi bi-chevron-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Kejadian Khusus</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="  col-mb-12">
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-head-row card-tools-still-right">
                            <div class="card-title">Daftar Pesan</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="  col-mb-12">
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($chatList as $chat): ?>
                                    <li class="list-group-item">
                                        <a href="<?= Url::to(['chat/index', 'receiverId' => $chat['user']->id]) ?>"
                                            class="d-flex justify-content-between align-items-center text-decoration-none text-dark">
                                            <div>
                                                <div><strong><?= $chat['user']->username ?></strong></div>
                                                <div class="text-muted small"
                                                    style="max-width:250px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                                    <?= $chat['lastMessage']->message ?>
                                                </div>
                                            </div>
                                            <div class="text-end">
                                                <div class="small text-muted">
                                                    <?= \app\models\ChatMessages::formatTime($chat['lastMessage']->created_at) ?>
                                                </div>

                                                <?php if ($chat['unread'] > 0): ?>
                                                <span
                                                    class="badge bg-primary rounded-pill"><?= $chat['unread'] ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </a>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>