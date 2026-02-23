<?php 
session_start();

// === YE SAHI HAI ===
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
// ===================

    include "DB_connection.php";
    include "app/Model/User.php";
    
    $user = get_user_by_id($conn, $_SESSION['id']);
    
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* ... (Aapka CSS style waise hi rahega) ... */
        .profile-pic-preview {
            width: 120px; 
            height: 120px;
            border-radius: 50%;
            border: 4px solid #eee;
            object-fit: cover; 
            margin: 0 auto 10px; 
            display: block; 
        }
        .file-input-wrapper {
          
            margin-bottom: 10px; /* Thoda margin kam kiya */
        }
    </style>
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <h4 class="title">Edit Profile <a href="profile.php" class="edit-btn" style="padding: 5px 10px; font-size: 14px; text-decoration: none;">View Profile</a></h4>
            
            <form class="form-1"
                  method="POST"
                  action="app/update-profile.php"
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

                <?php 
                    // === YAHAN BADLAV KIYA GAYA HAI ===
                    // Purana code jo '$_SESSION['photo']' use karta tha, use hata diya gaya hai.
                    
                    $photo_url = "img/user.png"; // Default photo

                    // 1. Pehle employee ki personal photo check karo
                    if (isset($_SESSION['employee_set_photo']) && !empty($_SESSION['employee_set_photo']) && file_exists("uploads/".$_SESSION['employee_set_photo'])) {
                        
                        $photo_url = "uploads/" . $_SESSION['employee_set_photo'];
                
                    // 2. Agar wo nahi hai, to admin ki set ki hui photo check karo
                    } else if (isset($_SESSION['admin_set_photo']) && !empty($_SESSION['admin_set_photo']) && file_exists("uploads/".$_SESSION['admin_set_photo'])) {
                        
                        $photo_url = "uploads/" . $_SESSION['admin_set_photo'];
                    }
                    // === BADLAV YAHAN KHATAM HUA ===
                ?>
                <br>

                <lable>Profile Picture</lable>
                
                <br><br>

                <img src="<?=$photo_url?>" 
                     alt="Profile Picture" 
                     class="profile-pic-preview" 
                     id="profile-pic-preview">
                
                <div class="file-input-wrapper">
                    <input type="file" name="photo" id="upload-photo" accept="image/png, image/jpeg, image/jpg">
                </div>
                
                <a href="#" id="remove-photo-link" style="color: red; text-decoration: none; font-size: 14px; margin-bottom: 20px; display: inline-block;">
                    <i class="fa fa-trash"></i> Remove Photo
                </a>
                
                <input type="hidden" name="remove_photo" id="remove-photo-input" value="0">
                
                <div class="input-holder">
                    <lable>Full Name</lable>
                    <input type="text" name="full_name" class="input-1" placeholder="Full Name" value="<?=htmlspecialchars($user['full_name'])?>"><br>
                </div>

                <div class="input-holder">
                    <lable>Old Password</lable>
                    <input type="password" name="old_password" class="input-1" placeholder="Old Password"><br>

                </div>
                <div class="input-holder">
                    <lable>New Password</lable>
                    <input type="password" name="new_password" class="input-1" placeholder="New Password"><br>
                </div>
                <div class="input-holder">
                    <lable>Confirm Password</lable>
                    <input type="password" name="confirm_password" class="input-1" placeholder="Confirm Password"><br>
                </div>

                <button class="edit-btn">Change</button>
            </form>
            
        </section>
    </div>

<script type="text/javascript">
    // --- Ye poora JavaScript section bilkul sahi hai ---
    
    // Active link ko set karne ke liye JavaScript
    var navLinks = document.querySelectorAll("#navList li a");
    navLinks.forEach(function(link) {
        if (link.href.includes("profile.php") || link.href.includes("edit_profile.php")) {
            link.parentElement.classList.add("active");
        }
    });

    // File upload karne par preview dikhane ke liye
    document.getElementById('upload-photo').onchange = function(evt) {
        const [file] = this.files;
        if (file) {
            document.getElementById('profile-pic-preview').src = URL.createObjectURL(file);
            document.getElementById('remove-photo-input').value = '0';
        }
    };

    // Remove Photo link ke liye
    document.getElementById('remove-photo-link').onclick = function(e) {
        e.preventDefault(); 
        document.getElementById('remove-photo-input').value = '1';
        document.getElementById('profile-pic-preview').src = 'img/user.png';
        document.getElementById('upload-photo').value = null;
    };
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