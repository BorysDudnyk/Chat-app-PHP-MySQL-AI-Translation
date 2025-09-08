<?php 
session_start();

if (isset($_SESSION['username'])) {
    include 'app/db.conn.php';
    include 'app/helpers/user.php';
    include 'app/helpers/chat.php';
    include 'app/helpers/opened.php';
    include 'app/helpers/timeAgo.php';

    if (!isset($_GET['user']) || empty($_GET['user'])) {
        header("Location: home.php");
        exit;
    }

    $chatWith = getUser($_GET['user'], $conn);

    if (empty($chatWith)) {
        header("Location: home.php");
        exit;
    }

    $chats = getChats($_SESSION['user_id'], $chatWith['user_id'], $conn);
    opened($chatWith['user_id'], $conn, $chats);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Чат</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="icon" href="img/logo.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="w-400 shadow p-4 rounded">
        <a href="home.php" class="fs-4 link-dark">&#8592;</a>
        <div class="d-flex align-items-center">
            <img src="uploads/<?=htmlspecialchars($chatWith['p_p'], ENT_QUOTES)?>" class="w-15 rounded-circle" alt="Аватар"/>
            <h3 class="display-4 fs-sm m-2">
                <?=htmlspecialchars($chatWith['name'], ENT_QUOTES)?> <br />
                <div class="d-flex align-items-center" title="Статус">
                    <?php if (last_seen($chatWith['last_seen']) === "Active"): ?>
                        <div class="online"></div>
                        <small class="d-block p-1">Онлайн</small>
                    <?php else: ?>
                        <small class="d-block p-1 last-seen">
                            Був(ла) в мережі: <?=htmlspecialchars(last_seen($chatWith['last_seen']), ENT_QUOTES)?>
                        </small>
                    <?php endif; ?>
                </div>
            </h3>
        </div>

        <div class="shadow p-4 rounded d-flex flex-column mt-2 chat-box" id="chatBox" style="max-height: 400px; overflow-y: auto;">
            <?php 
            if (!empty($chats)) {
                foreach($chats as $chat){
                    if($chat['from_id'] == $_SESSION['user_id']) { ?>
                        <p class="rtext align-self-end border rounded p-2 mb-1">
                            <?=htmlspecialchars($chat['message'], ENT_QUOTES)?>
                            <small class="d-block"><?=htmlspecialchars($chat['created_at'], ENT_QUOTES)?></small>
                        </p>
                    <?php } else { ?>
                        <p class="ltext border rounded p-2 mb-1 position-relative">
                            <span class="original-text"><?=htmlspecialchars($chat['message'], ENT_QUOTES)?></span>
                            <button class="btn btn-sm btn-link translate-btn p-0 m-0" style="position: absolute; top: 0; right: 5px;" data-text="<?=htmlspecialchars($chat['message'], ENT_QUOTES)?>">🌐</button>
                            <small class="d-block"><?=htmlspecialchars($chat['created_at'], ENT_QUOTES)?></small>
                        </p>
                    <?php }
                }
            } else { ?>
                <div class="alert no-messages text-center">
                    <i class="fa fa-comments d-block fs-big"></i>
                    Немає повідомлень, розпочніть розмову
                </div>
            <?php } ?>
        </div>

        <div class="input-group mb-3">
            <textarea rows="3" id="message" class="form-control" placeholder="Напишіть повідомлення..."></textarea>
            <button class="btn btn-primary" id="sendBtn" type="button">
                <i class="fa fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        const scrollDown = () => {
            const chatBox = document.getElementById('chatBox');
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        scrollDown();

        $(document).ready(function(){
            const sendBtn = $("#sendBtn");
            const messageBox = $("#message");

            sendBtn.on('click', function(){
                const message = messageBox.val().trim();
                if (message === "") return;

                sendBtn.prop('disabled', true);

                $.post("app/ajax/insert.php", {
                    message: message,
                    to_id: <?=json_encode($chatWith['user_id'])?>
                }, function(data){
                    messageBox.val("");
                    $("#chatBox").append(data);
                    scrollDown();
                    sendBtn.prop('disabled', false);
                });
            });

            messageBox.on('keypress', function(e){
                if (e.which === 13 && !e.shiftKey) {
                    e.preventDefault();
                    sendBtn.click();
                }
            });

            const updateLastSeen = () => {
                $.get("app/ajax/update_last_seen.php");
            }

            const fetchMessages = () => {
                $.post("app/ajax/getMessage.php", {
                    id_2: <?=json_encode($chatWith['user_id'])?>
                }, function(data){
                    if (data.trim() !== "") {
                        $("#chatBox").append(data);
                        scrollDown();
                    }
                });
            }

            updateLastSeen();
            fetchMessages();

            setInterval(updateLastSeen, 10000);
            setInterval(fetchMessages, 2000);

            $(document).on('click', '.translate-btn', function() {
                const btn = $(this);
                const originalText = btn.data('text');
                const span = btn.siblings('.original-text');

                btn.prop('disabled', true).text('⌛');

                $.post('translate.php', { text: originalText, target: 'uk' })
                    .done(function(result){
                        if(result.trim() !== "") {
                            span.html(result);
                        } else {
                            alert('Помилка перекладу.');
                        }
                    })
                    .fail(function() {
                        alert('Не вдалося підключитись до сервера перекладу.');
                    })
                    .always(function() {
                        btn.prop('disabled', false).text('🌐');
                    });
            });
        });
    </script>
</body>
</html>
<?php
} else {
    header("Location: index.php");
    exit;
}
?>
