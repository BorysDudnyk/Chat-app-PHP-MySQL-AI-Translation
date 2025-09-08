<?php
session_start();

if (isset($_SESSION['username'])) {

    if (isset($_POST['key'])) {
        include '../db.conn.php';

        $key = "%{$_POST['key']}%";

        $sql = "SELECT * FROM users
                WHERE username LIKE ? OR name LIKE ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array($key, $key));

        if ($stmt->rowCount() > 0) {
            $users = $stmt->fetchAll();

            foreach ($users as $user) {
                
                if ($user['user_id'] == $_SESSION['user_id']) continue;
                ?>
                <li class="list-group-item">
                    <a href="chat.php?user=<?=htmlspecialchars($user['username'])?>"
                       class="d-flex justify-content-between align-items-center p-2">
                        <div class="d-flex align-items-center">
                            <img src="uploads/<?=htmlspecialchars($user['p_p'])?>"
                                 class="w-10 rounded-circle" alt="Фото профілю">

                            <h3 class="fs-xs m-2 mb-0">
                                <?=htmlspecialchars($user['name'])?>
                            </h3>                
                        </div>
                    </a>
                </li>
                <?php
            }
        } else {
            ?>
            <div class="alert alert-info text-center">
                <i class="fa fa-user-times d-block fs-big"></i>
                Користувача "<?=htmlspecialchars($_POST['key'])?>" не знайдено.
            </div>
            <?php
        }
    }

} else {
    header("Location: ../../index.php");
    exit;
}
?>
