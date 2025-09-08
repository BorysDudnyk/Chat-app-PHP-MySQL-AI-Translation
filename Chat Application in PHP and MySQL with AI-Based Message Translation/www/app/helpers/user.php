<?php  

function getUser($username, $conn){
   $sql = "SELECT * FROM users 
           WHERE username=?";
   $stmt = $conn->prepare($sql);
   $stmt->execute(array($username));

   if ($stmt->rowCount() === 1) {
   	 $user = $stmt->fetch();
   	 return $user;
   } else {
   	$user = array();
   	return $user;
   }
}
