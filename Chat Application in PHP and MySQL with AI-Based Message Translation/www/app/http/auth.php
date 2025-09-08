<?php  
session_start();

if (isset($_POST['username']) && isset($_POST['password'])) {

   include '../db.conn.php';
   
   $username = $_POST['username'];
   $password = $_POST['password'];
   
   if (empty($username)) {
      $em = "Username is required";
      header("Location: ../../index.php?error=" . urlencode($em));
      exit;
   } elseif (empty($password)) {
      $em = "Password is required";
      header("Location: ../../index.php?error=" . urlencode($em));
      exit;
   } else {
      $sql  = "SELECT * FROM users WHERE username=?";
      $stmt = $conn->prepare($sql);
      $stmt->execute(array($username));

      if ($stmt->rowCount() === 1) {
         $user = $stmt->fetch();

         if ($user['username'] === $username) {
            
            if (hash('sha256', $password) === $user['password']) {
               
               $_SESSION['username'] = $user['username'];
               $_SESSION['name'] = $user['name'];
               $_SESSION['user_id'] = $user['user_id'];

               header("Location: ../../home.php");
               exit;
            } else {
               $em = "Incorrect Username or password";
               header("Location: ../../index.php?error=" . urlencode($em));
               exit;
            }
         } else {
            $em = "Incorrect Username or password";
            header("Location: ../../index.php?error=" . urlencode($em));
            exit;
         }
      } else {
         $em = "Incorrect Username or password";
         header("Location: ../../index.php?error=" . urlencode($em));
         exit;
      }
   }
} else {
   header("Location: ../../index.php");
   exit;
}
?>
