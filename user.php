<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/User.php";

    // यह $users को लाता है (या [] (खाली ऐरे) अगर कोई यूज़र नहीं है)
    $users = get_all_users($conn);
 
 ?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        .table-photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #eee;
        }
    </style>

</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            
            <h4 class="title-2">
                Manage Users
                
                <a href="add-user.php" class="btn" style="margin-left: 15px; background: #00CF22;">Add User</a>
            </h4>
            <?php if (isset($_GET['success'])) {?>
            <div class="success" role="alert">
              <?php echo stripcslashes($_GET['success']); ?>
            </div>
            <?php } ?>

            <?php if (count($users) > 0) { ?>
            <table class="main-table">
                <tr>
                    <th>Sr.No.</th>
                    <th>Photo</th>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
                <?php $i=0; foreach ($users as $user) { ?>
                <tr>
                    <td><?=++$i?></td>
                    
                    <td>
                        <?php 
                            $photo_path = "img/user.png"; 
                            
                            if (isset($user['admin_set_photo']) && !empty($user['admin_set_photo']) && file_exists("uploads/".htmlspecialchars($user['admin_set_photo']))) {
                                $photo_path = "uploads/" . htmlspecialchars($user['admin_set_photo']);
                            }
                        ?>
                        <img src="<?=$photo_path?>" class="table-photo" alt="Profile Pic">
                    </td>
                    <td><?=$user['full_name']?></td>
                    <td><?=$user['username']?></td>
                    <td><?=$user['role']?></td>
                    <td>
                        <a href="edit-user.php?id=<?=$user['id']?>" class="edit-btn">Edit</a>
                        <a href="delete-user.php?id=<?=$user['id']?>" class="delete-btn">Remove</a>
                    </td>
                </tr>
               <?php    } ?>
            </table>
            <?php }else { ?>
                <h3>Empty</h3>
            <?php   }?>
        </section>
    </div>

<script type="text/javascript">
    var active = document.querySelector("#navList li:nth-child(2)");
    active.classList.add("active");
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