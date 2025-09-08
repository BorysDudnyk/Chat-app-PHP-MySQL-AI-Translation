<?php 
session_start();

if (!isset($_SESSION['username'])) {
?>
<!DOCTYPE html>
<html lang="uk">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Чат - Реєстрація</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" href="img/logo.png">
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
  <div class="w-400 p-5 shadow rounded">
    <form method="post" action="app/http/signup.php" enctype="multipart/form-data">
      <div class="d-flex justify-content-center align-items-center flex-column">
        <img src="img/logo.png" class="w-25">
        <h3 class="display-4 fs-1 text-center">Реєстрація</h3>   
      </div>

      <?php 
        if (isset($_GET['error'])) { 
      ?>
        <div class="alert alert-warning" role="alert">
          <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
      <?php 
        }

        if (isset($_GET['name'])) {
          $name = htmlspecialchars($_GET['name']);
        } else {
          $name = '';
        }

        if (isset($_GET['username'])) {
          $username = htmlspecialchars($_GET['username']);
        } else {
          $username = '';
        }
      ?>

      <div class="mb-3">
        <label class="form-label">Ім’я</label>
        <input type="text" name="name" value="<?php echo $name; ?>" class="form-control">
      </div>

      <div class="mb-3">
        <label class="form-label">Ім’я користувача</label>
        <input type="text" name="username" value="<?php echo $username; ?>" class="form-control">
      </div>

      <div class="mb-3">
        <label class="form-label">Пароль</label>
        <input type="password" name="password" class="form-control">
      </div>

      <div class="mb-3">
        <label class="form-label">Фото профілю</label>
        <input type="file" name="pp" class="form-control">
      </div>

      <button type="submit" class="btn btn-primary">Зареєструватися</button>
      <a href="index.php" class="btn-login">Увійти</a>
    </form>
  </div>
</body>
</html>
<?php
} else {
  header("Location: home.php");
  exit;
}
?>
