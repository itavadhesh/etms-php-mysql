<?php 
session_start();
// (print_r को यहाँ से हटा दिया गया है)
// सुनिश्चित करें कि एम्प्लॉई लॉग इन है
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "employee") {
    
    include "DB_connection.php";
    include "app/Model/Task.php";

    // --- पेज को लोड करना ---
    if (isset($_GET['id'])) {
        $task_id = $_GET['id'];
        $task = get_task_by_id($conn, $task_id);

        if ($task == 0 || $task['assigned_to_id'] != $_SESSION['id']) {
           header("Location: my_task.php?error=Invalid Task");
           exit;
        }
    } else {
        header("Location: my_task.php");
        exit;
    }

    // --- (नया) सभी कमेंट्स को लोड करना ---
    $sql_comments = "SELECT tc.*, u.full_name, u.role 
                     FROM task_comments tc
                     JOIN users u ON tc.user_id = u.id
                     WHERE tc.task_id = ? 
                     ORDER BY tc.created_at ASC";
    $stmt_comments = $conn->prepare($sql_comments);
    $stmt_comments->execute([$task_id]);
    $comments = $stmt_comments->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit My Task</title>
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
            <h4 class="title">
                Edit Task
                <a href="my_task.php" class="btn" style="float: right; margin-top: -5px;">My Task</a>
            </h4>

            <?php if (isset($_GET['error'])) {?>
            <div class="danger" role="alert"><?=stripcslashes($_GET['error'])?></div>
            <?php } ?>
            <?php if (isset($_GET['success'])) {?>
            <div class="success" role="alert"><?=stripcslashes($_GET['success'])?></div>
            <?php } ?>
            
            <form class="form-1" method="POST" action="app/update_task_status_only.php">
                <input type="hidden" name="id" value="<?=$task['id']?>">
                <div class="input-holder"><p><b>Title: </b><?=$task['title']?></p></div>
                <div class="input-holder"><p><b>Description: </b><?=$task['description']?></p></div><br>
                <div class="input-holder">
                    <label>Status</label>
                    <select name="status" class="input-1">
                        <option value="pending" <?php if($task['status'] == "pending") echo"selected";?>>Pending</option>
                        <option value="in_progress" <?php if($task['status'] == "in_progress") echo"selected";?>>In Progress</option>
                        <option value="completed" <?php if($task['status'] == "completed") echo"selected";?>>Completed</option>
                    </select><br>
                </div>
                <button type="submit" name="update_status_button" class="edit-btn">Update Status</button>
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
                        <textarea name="comment_text" class="input-1" rows="4" placeholder="Ask a question or post an update..." required></textarea>
                    </div>
                    <button type="submit" name="post_comment_button" class="edit-btn">Post Comment</button>
                </form>
            </div>
            
        </section>
    </div>

<script type="text/javascript">
    var active = document.querySelector("#navList li:nth-child(2)");
    active.classList.add("active");
</script>
</body>
<?php include 'inc/footer.php'; ?>
</html>
<?php 
}else{ 
    $em = "First login";
    header("Location: login.php?error=$em");
    exit();
}
?>