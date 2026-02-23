<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/Task.php";
    include "app/Model/User.php";
    
    if (!isset($_GET['id'])) {
        header("Location: tasks.php");
        exit();
    }
    $id = $_GET['id'];
    $task = get_task_by_id($conn, $id);

    if ($task == 0) {
        header("Location: tasks.php");
        exit();
    }
    $users = get_all_users($conn);

    // --- (नया) सभी कमेंट्स को लोड करना ---
    $sql_comments = "SELECT tc.*, u.full_name, u.role 
                     FROM task_comments tc
                     JOIN users u ON tc.user_id = u.id
                     WHERE tc.task_id = ? 
                     ORDER BY tc.created_at ASC";
    $stmt_comments = $conn->prepare($sql_comments);
    $stmt_comments->execute([$id]);
    $comments = $stmt_comments->fetchAll();
 ?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Task</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    
    <style>
        .comment-thread { margin-top: 30px; border-top: 2px solid #eee; padding-top: 20px; }
        .comment { background: #f9f9f9; border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; border-radius: 5px; }
        .comment strong { display: block; color: #333; }
        .comment small { font-size: 0.8em; color: #777; }
        .comment-admin { background: #eef8ff; border-color: #bce8f1; } 
        .comment-admin strong { color: #0056b3; }
        .comment-employee { background: #fff8ee; border-color: #f1e6bc; } 
        .comment-employee strong { color: #b38600; }
    </style>
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <h4 class="title">Edit Task <a href="tasks.php">Tasks</a></h4>
            <form class="form-1"
                  method="POST"
                  action="app/update-task.php"> 
                  
                <?php if (isset($_GET['error'])) {?>
                <div class="danger" role="alert"><?=stripcslashes($_GET['error'])?></div>
                <?php } ?>
                <?php if (isset($_GET['success'])) {?>
                <div class="success" role="alert"><?=stripcslashes($_GET['success'])?></div>
                <?php } ?>
                
                <div class="input-holder">
                    <label>Title</label>
                    <input type="text" name="title" class="input-1" placeholder="Title" value="<?=$task['title']?>"><br>
                </div>
                <div class="input-holder">
                    <label>Description</label>
                    <textarea name="description" rows="5" class="input-1" ><?=$task['description']?></textarea><br>
                </div>
                <div class="input-holder">
                    <label>Due Date</label>
                    <input type="date" name="due_date" class="input-1" placeholder="Due Date" value="<?=$task['due_date']?>"><br>
                </div>
                
                <div class="input-holder">
                    <label>Assigned to</label>
                    <select name="assigned_to_id" class="input-1">
                        <option value="0">Select employee</option>
                        <?php if ($users !=0) { 
                            foreach ($users as $user) {
                                if ($user['role'] == 'employee') {
                                    if ($task['assigned_to_id'] == $user['id']) { ?>
                                        <option selected value="<?=$user['id']?>"><?=$user['full_name']?></option>
                                    <?php } else { ?>
                                        <option value="<?=$user['id']?>"><?=$user['full_name']?></option>
                        <?php } } } } ?>
                    </select><br>
                </div>
                
                <div class="input-holder">
                    <label>Current Status</label>
                    <input type="text" class="input-1" 
                           value="<?= htmlspecialchars(ucfirst(str_replace('_', ' ', $task['status']))) ?>" 
                           readonly 
                           style="background: #f0f0f0; font-weight: bold; color: #333;">
                </div>
                <input type="text" name="id" value="<?=$task['id']?>" hidden>
                <button type="submit" name="update_task_button" class="edit-btn">Update Task Details</button>
            </form>
            
            <div class="comment-thread">
                <h4 class="title-2">Conversation</h4>
                
                <?php if (count($comments) > 0) {
                    foreach ($comments as $comment) {
                        $comment_class = ($comment['role'] == 'admin') ? 'comment comment-admin' : 'comment comment-employee';
                ?>
                    <div class="<?= $comment_class ?>">
                        <strong><?= htmlspecialchars($comment['full_name']) ?>:</strong>
                        <p style="margin: 5px 0;"><?= nl2br(htmlspecialchars($comment['comment_text'])) ?></p>
                        <small><?= date('d M Y, h:i A', strtotime($comment['created_at'])) ?></small>
                    </div>
                <?php 
                    } // foreach end
                } else { // अगर कोई कमेंट नहीं है
                ?>
                    <p>No comments on this task yet.</p>
                <?php } ?>
                
                <form method="POST" action="app/add_comment.php" style="margin-top: 20px;">
                    <input type="hidden" name="task_id" value="<?=$task['id']?>">
                    
                    <div class="input-holder">
                        <label>Post a new comment or reply</label>
                        <textarea name="comment_text" class="input-1" rows="4" placeholder="Post a reply or ask a question..." required></textarea>
                    </div>
                    <button type="submit" name="post_comment_button" class="edit-btn">Post Reply</button>
                </form>
            </div>
            
        </section>
    </div>

<script type="text/javascript">
    var active = document.querySelector("#navList li:nth-child(4)");
    active.classList.add("active");
</script>
<?php include 'inc/footer.php'; ?>
</body>
</html>
<?php }else{ 
    $em = "First login";
    header("Location: login.php?error=$em");
    exit();
}
?>