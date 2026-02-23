<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/User.php";
    
    if (!isset($_GET['id'])) {
        header("Location: user.php");
        exit();
    }
    $id = $_GET['id'];
    $user = get_user_by_id($conn, $id);

    if ($user == 0) {
        header("Location: user.php");
        exit();
    }

 ?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">

    <style>
        .profile-pic-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #eee;
            cursor: pointer;
        }
    </style>

</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <h4 class="title">Edit User <a href="user.php">Users</a></h4>
            
            <form class="form-1"
                  method="POST"
                  action="app/update-user.php"
                  enctype="multipart/form-data"> 

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

                <div class="profile-pic-container">
                    <label for="photo-upload">Profile Picture</label><br>
                    <?php 
                        // === YAHAN BADLAV KIYA GAYA HAI (Line 86) ===
                        $photo_path = "img/user.png"; // Default photo
                        
                        // Admin panel hamesha 'admin_set_photo' ko dekhega
                        if (isset($user['admin_set_photo']) && !empty($user['admin_set_photo']) && file_exists("uploads/".htmlspecialchars($user['admin_set_photo']))) {
                            $photo_path = "uploads/" . htmlspecialchars($user['admin_set_photo']);
                        }
                        // === BADLAV KHATAM ===
                    ?>
                    <img src="<?=$photo_path?>" alt="Profile Picture" class="profile-pic" id="profilePicPreview">
                    <br>
                    <input type="file" name="photo" id="photo-upload" onchange="previewPhoto()" style="margin-top: 10px;">
                    
                    <input type="hidden" name="old_photo" value="<?=htmlspecialchars($user['admin_set_photo'] ?? '')?>">
                    </div>


                <div class="input-holder">
                    <lable>Full Name</lable>
                    <input type="text" name="full_name" class="input-1" placeholder="Full Name" value="<?=htmlspecialchars($user['full_name'])?>"><br>
                </div>
                <div class="input-holder">
                    <lable>Username</lable>
                    <input type="text" name="user_name" value="<?=$user['username']?>" class="input-1" placeholder="Username"><br>
                </div>
                
                <div class="input-holder">
                    <lable>New Password</lable>
                    <input type="password" name="password" class="input-1" placeholder="New Password"><br>
                </div>

                <input type="text" name="id" value="<?=$user['id']?>" hidden>

                <button class="edit-btn">Update</button>
            </form>
            
        </section>
    </div>

<script type="text/javascript">
    var active = document.querySelector("#navList li:nth-child(2)");
    active.classList.add("active");

    // Photo preview ke liye JavaScript
    function previewPhoto() {
        var file = document.getElementById('photo-upload').files[0];
        var reader = new FileReader();
        
        reader.onloadend = function() {
            document.getElementById('profilePicPreview').src = reader.result;
        }
        
        if (file) {
            reader.readAsDataURL(file);
        } else {
            document.getElementById('profilePicPreview').src = "<?=$photo_path?>";
        }
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