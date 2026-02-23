<?php 
session_start();

// 1. चेक करें कि एडमिन लॉग इन है और ID मिली है
if (isset($_SESSION['role']) && $_SESSION['role'] == "admin" && isset($_GET['id'])) {
    
    // (डेटाबेस कनेक्शन को शामिल करें)
    include "DB_connection.php";
    
    $task_id = $_GET['id'];

    try {
        // --- 1. सबसे पहले सभी कमेंट्स डिलीट करें ---
        $sql_comments = "DELETE FROM task_comments WHERE task_id = ?";
        $stmt_comments = $conn->prepare($sql_comments);
        $stmt_comments->execute([$task_id]);

        // --- 2. फिर सभी नोटिफिकेशन डिलीट करें (task_id के आधार पर) ---
        $sql_notify = "DELETE FROM notifications WHERE task_id = ?";
        $stmt_notify = $conn->prepare($sql_notify);
        $stmt_notify->execute([$task_id]);

        // --- 3. आखिर में खुद टास्क को डिलीट करें ---
        $sql_task = "DELETE FROM tasks WHERE id = ?";
        $stmt_task = $conn->prepare($sql_task);
        $stmt_task->execute([$task_id]);
        
        // 4. सफलता (Success)
        header("Location: tasks.php?success=Task and all related data deleted.");
        exit;

    } catch (PDOException $e) {
        // कोई एरर हो तो
        header("Location: tasks.php?error=" . $e->getMessage());
        exit;
    }

} else {
    // अगर कोई सीधे इस पेज पर आए
    header("Location: tasks.php?error=Invalid action");
    exit();
}
?>