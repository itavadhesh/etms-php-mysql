<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/Task.php";
    include "app/Model/User.php"; // इसे रहने देते हैं, हालाँकि $users की ज़रूरत नहीं है
    
    $text = "All Task";
    if (isset($_GET['due_date']) &&  $_GET['due_date'] == "Due Today") {
        $text = "Due Today";
      $tasks = get_all_tasks_due_today($conn); // नोट: यह फंक्शन JOIN नहीं करता
      $num_task = count_tasks_due_today($conn);

    }else if (isset($_GET['due_date']) &&  $_GET['due_date'] == "Overdue") {
        $text = "Overdue";
      $tasks = get_all_tasks_overdue($conn); // नोट: यह फंक्शन JOIN नहीं करता
      $num_task = count_tasks_overdue($conn);

    }else if (isset($_GET['due_date']) &&  $_GET['due_date'] == "No Deadline") {
        $text = "No Deadline";
      $tasks = get_all_tasks_NoDeadline($conn); // नोट: यह फंक्शन JOIN नहीं करता
      $num_task = count_tasks_NoDeadline($conn);

    }else{
         $text = "All Task";
         $tasks = get_all_tasks($conn); // यह फंक्शन JOIN करता है
        $num_task = count_tasks($conn);
    }
    
    // नोट: $users की ज़रूरत सिर्फ तब है जब $tasks JOIN होकर नहीं आ रहा हो।
    $users = get_all_users($conn); 
    

 ?>
<!DOCTYPE html>
<html>
<head>
    <title>All Tasks</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <h4 class="title-2">
                <a href="create_task.php" class="btn">Create Task</a>
                <a href="tasks.php?due_date=Due Today">Due Today</a>
                <a href="tasks.php?due_date=Overdue">Overdue</a>
                <a href="tasks.php?due_date=No Deadline">No Deadline</a>
                <a href="tasks.php">All Tasks</a>
 
            </h4>
         <h4 class="title-2"><?=$text?> (<?=$num_task?>)</h4>
            <?php if (isset($_GET['success'])) {?>
            <div class="success" role="alert">
              <?php echo stripcslashes($_GET['success']); ?>
            </div>
        <?php } ?>
            <?php if ($tasks != 0) { ?>
            <table class="main-table">
                <tr>
                    <th>Sr.No.</th>
                    <th>Title</th>
                    <th>Description</th>
                    
                    <th>Assigned To</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php $i=0; foreach ($tasks as $task) { ?>
                <tr>
                    <td><?=++$i?></td>
                    <td><?=$task['title']?></td>
                    <td><?=$task['description']?></td>
                    
                    <td>
                        <?php 
                        // --- कोड को तेज़ (fast) बनाने के लिए बदला गया ---
                        // अगर $task['full_name'] मौजूद है (JOIN से आया है)
                        if (isset($task['full_name'])) {
                            echo $task['full_name'];
                        } else {
                            // अगर JOIN से नहीं आया (जैसे 'Due Today' में), तो पुराना तरीका अपनाएँ
                            foreach ($users as $user) {
                                if($user['id'] == $task['assigned_to_id']){ 
                                    echo $user['full_name'];
                                    break; // मिलते ही लूप रोक दें
                                }}}
                        ?>
                    </td>
                    
                    <td><?php if($task['due_date'] == "" || $task['due_date'] == "0000-00-00") echo "No Deadline";
                             else echo $task['due_date'];
                        ?></td>
                    <td><?=$task['status']?></td>
                    <td>
                        <a href="edit-task.php?id=<?=$task['id']?>" class="edit-btn">Edit</a>
                        <a href="delete-task.php?id=<?=$task['id']?>" class="delete-btn">Delete</a>
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
    var active = document.querySelector("#navList li:nth-child(4)");
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
 ?><?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/Task.php";
    include "app/Model/User.php"; // इसे रहने देते हैं, हालाँकि $users की ज़रूरत नहीं है
    
    $text = "All Task";
    if (isset($_GET['due_date']) &&  $_GET['due_date'] == "Due Today") {
        $text = "Due Today";
      $tasks = get_all_tasks_due_today($conn); // नोट: यह फंक्शन JOIN नहीं करता
      $num_task = count_tasks_due_today($conn);

    }else if (isset($_GET['due_date']) &&  $_GET['due_date'] == "Overdue") {
        $text = "Overdue";
      $tasks = get_all_tasks_overdue($conn); // नोट: यह फंक्शन JOIN नहीं करता
      $num_task = count_tasks_overdue($conn);

    }else if (isset($_GET['due_date']) &&  $_GET['due_date'] == "No Deadline") {
        $text = "No Deadline";
      $tasks = get_all_tasks_NoDeadline($conn); // नोट: यह फंक्शन JOIN नहीं करता
      $num_task = count_tasks_NoDeadline($conn);

    }else{
         $text = "All Task";
         $tasks = get_all_tasks($conn); // यह फंक्शन JOIN करता है
        $num_task = count_tasks($conn);
    }
    
    // नोट: $users की ज़रूरत सिर्फ तब है जब $tasks JOIN होकर नहीं आ रहा हो।
    $users = get_all_users($conn); 
    

 ?>
<!DOCTYPE html>
<html>
<head>
    <title>All Tasks</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <h4 class="title-2">
                <a href="create_task.php" class="btn">Create Task</a>
                <a href="tasks.php?due_date=Due Today">Due Today</a>
                <a href="tasks.php?due_date=Overdue">Overdue</a>
                <a href="tasks.php?due_date=No Deadline">No Deadline</a>
                <a href="tasks.php">All Tasks</a>
 
            </h4>
         <h4 class="title-2"><?=$text?> (<?=$num_task?>)</h4>
            <?php if (isset($_GET['success'])) {?>
            <div class="success" role="alert">
              <?php echo stripcslashes($_GET['success']); ?>
            </div>
        <?php } ?>
            <?php if ($tasks != 0) { ?>
            <table class="main-table">
                <tr>
                    <th>Sr.No.</th>
                    <th>Title</th>
                    <th>Description</th>
                    
                    <th>Assigned To</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php $i=0; foreach ($tasks as $task) { ?>
                <tr>
                    <td><?=++$i?></td>
                    <td><?=$task['title']?></td>
                    <td><?=$task['description']?></td>
                    
                    <td>
                        <?php 
                        // --- कोड को तेज़ (fast) बनाने के लिए बदला गया ---
                        // अगर $task['full_name'] मौजूद है (JOIN से आया है)
                        if (isset($task['full_name'])) {
                            echo $task['full_name'];
                        } else {
                            // अगर JOIN से नहीं आया (जैसे 'Due Today' में), तो पुराना तरीका अपनाएँ
                            foreach ($users as $user) {
                                if($user['id'] == $task['assigned_to_id']){ 
                                    echo $user['full_name'];
                                    break; // मिलते ही लूप रोक दें
                                }}}
                        ?>
                    </td>
                    
                    <td><?php if($task['due_date'] == "" || $task['due_date'] == "0000-00-00") echo "No Deadline";
                             else echo $task['due_date'];
                        ?></td>
                    <td><?=$task['status']?></td>
                    <td>
                        <a href="edit-task.php?id=<?=$task['id']?>" class="edit-btn">Edit</a>
                        <a href="delete-task.php?id=<?=$task['id']?>" class="delete-btn">Delete</a>
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
    var active = document.querySelector("#navList li:nth-child(4)");
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