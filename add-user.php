<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
 
 ?>
<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Style for profile picture preview */
        .profile-pic-preview {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #ddd;
            background-color: #f8f8f8;
            margin-bottom: 10px;
            overflow: hidden; /* This crops the image */
        }
        .profile-pic-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* This scales and crops the image nicely */
        }
        #photoInput {
            border: 1px solid #ccc;
            padding: 5px;
            width: 100%;
        }
    </style>
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <h4 class="title">Add User <a href="user.php">Users</a></h4>
            <form class="form-1"
                  method="POST"
                  action="app/add-user.php"
                  enctype="multipart/form-data"> <!-- YAHAN BADLAV KIYA GAYA HAI -->

                <?php if (isset($_GET['error'])) {?>
                <div class="danger" role="alert">
                  <?php echo stripcslashes($_GET['error']); ?>
                </div>
                <?php } ?>

                <?php if (isset($_GET['success'])) {?>
                <div class="success" role="alert">
                  <?php echo stripcslashes($_GET['success']); ?>
                </div>
                <?php } ?>

                <!-- === YAHAN NAYA CODE SHURU HOTA HAI === -->
                <div class="input-holder">
                    <lable>Profile Picture (Optional)</lable>
                    <!-- Circular image preview -->
                  
                    <!-- File input -->
                    <input type="file" name="photo" id="photoInput" onchange="previewPhoto(event)">
                </div>
                <!-- === NAYA CODE YAHAN KHATAM HOTA HAI === -->

                <div class="input-holder">
                    <lable>Full Name</lable>
                    <input type="text" name="full_name" class="input-1" placeholder="Full Name"><br>
                </div>
                <div class="input-holder">
                    <lable>Username</lable>
                    <input type="text" name="user_name" class="input-1" placeholder="Username"><br>
                </div>
                <div class="input-holder">
                    <lable>Password</lable>
                    <input type="password" name="password" class="input-1" placeholder="Password"><br> <!-- TYPE BADLA GAYA -->
                </div>

                <button class="edit-btn">Add</button>
            </form>
            
        </section>
    </div>

<script type="text/javascript">
    // Script for active nav link
    var active = document.querySelector("#navList li:nth-child(2)");
    active.classList.add("active");

    // Script for photo preview
    function previewPhoto(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('profilePreview');
            output.src = reader.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
</body>
<?php include 'inc/footer.php'; ?>
</html>
<?php }else{ 
   $em = "First login";
   header("Location: login.php?error=$em");
   exit();
}
 ?>
