<?php

include 'components/connect.php';

if(isset($_COOKIE['user_id'])){
   $user_id = $_COOKIE['user_id'];
}else{
   $user_id = '';
}

if(isset($_POST['submit'])){
   // Sanitize and validate user input
   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
   $pass = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);
   
   if (empty($email) || empty($pass)) {
       // Handle empty input fields
       $message[] = 'Please enter both email and password.';
   } else {
       // Check if the user exists in the database
       $select_user = $conn->prepare("SELECT id, password FROM `users` WHERE email = ? LIMIT 1");
       $select_user->execute([$email]);
       $row = $select_user->fetch(PDO::FETCH_ASSOC);
   
       if ($select_user->rowCount() > 0) {
           // Verify the password using a secure hashing algorithm (e.g., bcrypt)
           $storedPasswordHash = $row['password'];
           $string = sha1($pass);
           $first_20_chars = substr($string, 0, 20);

           if ($first_20_chars == $storedPasswordHash) {
               // Password is correct, set a session or cookie for authentication
               setcookie('user_id', $row['id'], time() + 60*60*24*30, '/');
               header('location: home.php');
               exit(); // Make sure to exit after redirection
           } else {
               $message[] = 'Incorrect email or password.';
           }
       } else {
           $message[] = 'Incorrect email or password.';
       }
   }
   
   // Handle any messages or errors
   if (!empty($message)) {
       foreach ($message as $msg) {
           echo $msg . '<br>';
       }
   }
   

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container">

   <form action="" method="post" enctype="multipart/form-data" class="login">
      <h3>welcome back!</h3>
      <p>your email <span>*</span></p>
      <input type="email" name="email" placeholder="enter your email" maxlength="50" required class="box">
      <p>your password <span>*</span></p>
      <input type="password" name="pass" placeholder="enter your password" maxlength="20" required class="box">
      <p class="link">don't have an account? <a href="register.php">register now</a></p>
      <input type="submit" name="submit" value="login now" class="btn">
   </form>

</section>












<?php include 'components/footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>
   
</body>
</html>