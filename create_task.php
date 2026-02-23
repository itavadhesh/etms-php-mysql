<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/User.php"; // यह get_all_users() लाएगा

    // यह $users को A-Z क्रम में लाएगा
    $users = get_all_users($conn); 

 ?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Task</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <h4 class="title">Create Task </h4>
            <form class="form-1"
                  method="POST"
                  action="app/add-task.php">
                  
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
                
                <div class="input-holder">
                    <label>Title</label>
                    <input type="text" name="title" class="input-1" placeholder="Title" required><br>
                </div>
                <div class="input-holder">
                    <label>Description</label>
                    <textarea name="description" class="input-1" placeholder="Description"></textarea><br>
                </div>
                <div class="input-holder">
                    <label>Due Date</label>
                    <input type="date" name="due_date" class="input-1" placeholder="Due Date"><br>
                </div>

                <div class="input-holder">
                    <label for="assign_to_select">Assigned to </label>
                    
                    <div style="margin-bottom: 5px;">
                        <a href="#" id="select-all-btn" style="font-size: 12px;">Select All</a> | 
                        <a href="#" id="deselect-all-btn" style="font-size: 12px;">Deselect All</a>
                    </div>
                    
                    <div style="margin-bottom: 10px;">
                        <input type="text" id="employee-search" placeholder="Search employee..." class="input-1" style="width: 100%; box-sizing: border-box;">
                    </div>
                    
                    <div id="assign_to_select" class="input-1" style="height: 150px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
                        <?php if ($users != 0) { 
                            foreach ($users as $user) {
                                if ($user['role'] == 'employee') {
                        ?>
                        
                        <div class="checkbox-item" style="display: flex; align-items: center; margin-bottom: 5px;">
                            
                            <input type="checkbox" 
                                   name="assigned_to_ids[]" 
                                   value="<?=$user['id']?>" 
                                   id="user_<?=$user['id']?>"
                                   style="flex-shrink: 0;"> <label for="user_<?=$user['id']?>" style="margin-left: 5px; font-weight: normal; cursor: pointer;">
                                <?=$user['full_name']?>
                            </label>
                        </div>
                        <?php 
                                } // if role end
                            } // foreach end
                        } else { // अगर कोई एम्प्लॉई नहीं है
                        ?>
                            <p>No employees found.</p>
                        <?php
                        } // if users end 
                        ?>
                    </div>
                    <br>
                </div>
                <button type="submit" name="create_task" class="edit-btn">Create Task</button>
            </form>
            
        </section>
    </div>

<script type="text/javascript">
    var active = document.querySelector("#navList li:nth-child(3)");
    active.classList.add("active");

    // --- Select All / Deselect All के लिए ---
    
    document.getElementById('select-all-btn').addEventListener('click', function(e) {
        e.preventDefault(); 
        let checkboxes = document.querySelectorAll('input[name="assigned_to_ids[]"]');
        for (let i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = true; 
        }
    });

    document.getElementById('deselect-all-btn').addEventListener('click', function(e) {
        e.preventDefault(); 
        let checkboxes = document.querySelectorAll('input[name="assigned_to_ids[]"]');
        for (let i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = false; 
        }
    });

    // --- यह सर्च लॉजिक है ---
    document.getElementById('employee-search').addEventListener('keyup', function(e) {
        let searchTerm = e.target.value.toLowerCase();
        let items = document.querySelectorAll('.checkbox-item');
        
        items.forEach(function(item) {
            let employeeName = item.querySelector('label').textContent.toLowerCase();
            
            if (employeeName.includes(searchTerm)) {
                // === ⬇️ यहाँ भी बदलाव है ⬇️ ===
                item.style.display = 'flex'; // 'block' की जगह 'flex'
            } else {
                item.style.display = 'none'; 
            }
        });
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