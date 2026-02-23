<?php 
session_start();

// === YAHAN BADLAV KIYA GAYA HAI ===
// Humne check kiya ki user login hai, lekin role check (== "employee") hata diya
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
// ===================================

    include "DB_connection.php";
    include "app/Model/User.php";
    
    // Ye session ID se user le aayega (chahe admin ho ya employee)
    $user = get_user_by_id($conn, $_SESSION['id']);
    
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <h4 class="title">Profile <a href="edit_profile.php" class="edit-btn" style="padding: 5px 10px; font-size: 14px; text-decoration: none;">Edit Profile</a></h4>
            
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
            <div style="text-align: center; margin-bottom: 20px;">
                <img src="<?=$photo_url?>" 
                     alt="Profile Photo" 
                     style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 5px solid #00c853;">
            </div>

           <table class="main-table" style="max-width: 400px; margin: 0 auto;">
                <tr>
                    <td style="font-weight: bold; width: 120px;">Full Name</td>
                    <td><?=htmlspecialchars($user['full_name'])?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Username</td>
                    <td><?=$user['username']?></td>
                </tr>
                 <tr>
                    <td style="font-weight: bold;">Role</td>
                    <td style="text-transform: capitalize;"><?=$user['role']?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Joined At</td>
                    <td><?=$user['created_at']?></td>
                </tr>
            </table>

        </section>
    </div>

<script type="text/javascript">
    // Active link ko set karne ke liye JavaScript
    var navLinks = document.querySelectorAll("#navList li a");
    navLinks.forEach(function(link) {
        // Check karein ki link 'profile.php' hai
        if (link.href.includes("profile.php") && !link.href.includes("edit_profile.php")) { // Zyada specific
            // Uske parent 'li' ko active class dein
            link.parentElement.classList.add("active");
        }
    });
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