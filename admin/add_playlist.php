<?php

include '../components/connect.php';

if(isset($_COOKIE['tutor_id'])){
   $tutor_id = $_COOKIE['tutor_id'];
}else{
   $tutor_id = '';
   header('location:login.php');
}


// if(isset($_POST['submit'])){
//    $title = $_POST['title'];
//    $title = filter_var($title, FILTER_SANITIZE_STRING);
//    $description = $_POST['description'];
//    $description = filter_var($description, FILTER_SANITIZE_STRING);
//    $status = $_POST['status'];
//    $status = filter_var($status, FILTER_SANITIZE_STRING);
//    $date_now = date("Y-m-d");
//    $image = $_FILES['image']['name'];
//    $image = filter_var($image, FILTER_SANITIZE_STRING);
//    $ext = pathinfo($image, PATHINFO_EXTENSION);
//    $rename = unique_id().'.'.$ext;
//    $image_size = $_FILES['image']['size'];
//    $image_tmp_name = $_FILES['image']['tmp_name'];
//    $image_folder = '../uploaded_files/'.$rename;
//    $add_playlist = $conn->prepare('INSERT INTO playlist (tutor_id,title, description, thumb,date, status) VALUES(?,?,?,?,?,?)');
//    $add_playlist->execute(['504', $title, $description, $rename, $date_now, $status]);
//    move_uploaded_file($image_tmp_name, $image_folder);
//    $message[] = 'new playlist created! ';  

// }
if (isset($_POST['submit'])) {
   // Sanitize input data
   $title = filter_var($_POST['title'], FILTER_SANITIZE_STRING);
   $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
   $status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);

   // Get the current date
   $date_now = date("Y-m-d");

   // Process uploaded image
   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $ext = pathinfo($image, PATHINFO_EXTENSION);
   $rename = unique_id().'.'.$ext;
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploaded_files/'.$rename;

   // Check if tutor_id exists before inserting
   $check_tutor = $conn->prepare('SELECT COUNT(*) FROM tutors WHERE id = ?');
   $check_tutor->execute([$tutor_id]);

   if ($check_tutor->fetchColumn() > 0) {
       // Tutor exists, proceed with the insertion
       $add_playlist = $conn->prepare('INSERT INTO playlist (tutor_id, title, description, thumb, date, status) VALUES(?,?,?,?,?,?)');
       $add_playlist->execute([$tutor_id, $title, $description, $rename, $date_now, $status]);

       // Move uploaded image to the destination folder
       move_uploaded_file($image_tmp_name, $image_folder);

       // Provide feedback
       $message[] = 'New playlist created!';
   } else {
       // Tutor does not exist, handle accordingly (e.g., show an error message)
       $message[] = 'Error: Tutor does not exist.';
   }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Add Playlist</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>
   
<section class="playlist-form">

   <h1 class="heading">create playlist</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <p>playlist status <span>*</span></p>
      <select name="status" class="box" required>
         <option value="" selected disabled>-- select status</option>
         <option value="active">active</option>
         <option value="deactive">deactive</option>
      </select>
      <p>playlist title <span>*</span></p>
      <input type="text" name="title" maxlength="100" required placeholder="enter playlist title" class="box">
      <p>playlist description <span>*</span></p>
      <textarea name="description" class="box" required placeholder="write description" maxlength="1000" cols="30" rows="10"></textarea>
      <p>playlist thumbnail <span>*</span></p>
      <input type="file" name="image" accept="image/*" required class="box">
      <input type="submit" value="create playlist" name="submit" class="btn">
   </form>

</section>















<?php include '../components/footer.php'; ?>

<script src="../js/admin_script.js"></script>

</body>
</html>