<?php 
session_start();

// 1. चेक करें कि एडमिन लॉग इन है और ID मिली है
if (isset($_SESSION['role']) && $_SESSION['role'] == "admin" && isset($_GET['id'])) {
    
    include "DB_connection.php";
    
    $user_id_to_delete = $_GET['id']; // जिस एम्प्लॉई को डिलीट करना है

    try {
        
        // --- 1. उस एम्प्लॉई के सभी टास्क IDs को ढूँढें ---
        $sql_get_tasks = "SELECT id FROM tasks WHERE assigned_to_id = ?";
        $stmt_get_tasks = $conn->prepare($sql_get_tasks);
        $stmt_get_tasks->execute([$user_id_to_delete]);
        $task_ids = $stmt_get_tasks->fetchAll(PDO::FETCH_COLUMN);

        // --- 2. अगर उस एम्प्लॉई के पास कोई टास्क था... ---
        if (count($task_ids) > 0) {
            
            // 2a. उन सभी टास्क IDs से जुड़े सारे नोटिफिकेशन (Admin के और Employee के) डिलीट करें
            $placeholders = implode(',', array_fill(0, count($task_ids), '?')); // ?,?,? बनाता है
            $sql_notify = "DELETE FROM notifications WHERE task_id IN ($placeholders)";
            $stmt_notify = $conn->prepare($sql_notify);
            $stmt_notify->execute($task_ids);

            // 2b. उन सभी टास्क IDs से जुड़े सारे कमेंट्स (Admin और Employee के) डिलीट करें
            $sql_comments = "DELETE FROM task_comments WHERE task_id IN ($placeholders)";
            $stmt_comments = $conn->prepare($sql_comments);
            $stmt_comments->execute($task_ids);

            // 2c. अब उन सभी टास्क को डिलीट करें
            $sql_tasks = "DELETE FROM tasks WHERE id IN ($placeholders)";
            $stmt_tasks = $conn->prepare($sql_tasks);
            $stmt_tasks->execute($task_ids);
        }

        // --- 3. उस एम्प्लॉई द्वारा किए गए (दूसरे टास्क पर) कमेंट्स भी डिलीट करें ---
        $sql_user_comments = "DELETE FROM task_comments WHERE user_id = ?";
        $stmt_user_comments = $conn->prepare($sql_user_comments);
        $stmt_user_comments->execute([$user_id_to_delete]);

        // --- 4. उस एम्प्लॉई को भेजे गए (जो टास्क से जुड़े नहीं हैं) नोटिफिकेशन भी डिलीट करें ---
        $sql_user_notify = "DELETE FROM notifications WHERE recipient = ?";
        $stmt_user_notify = $conn->prepare($sql_user_notify);
        $stmt_user_notify->execute([$user_id_to_delete]);
        
        // --- 5. आखिर में खुद एम्प्लॉई को डिलीट करें ---
        $sql_user = "DELETE FROM users WHERE id = ? AND role = 'employee'";
        $stmt_user = $conn->prepare($sql_user);
        $stmt_user->execute([$user_id_to_delete]);
        
        // 6. सफलता (Success) - (यह user.php पर वापस भेजता है)
        header("Location: user.php?success=Employee and all their related data deleted.");
        exit;

    } catch (PDOException $e) {
        // कोई एरर हो तो
        header("Location: user.php?error=" . $e->getMessage());
        exit;
    }

} else {
    // अगर कोई सीधे इस पेज पर आए
    header("Location: user.php?error=Invalid action");
    exit();
}
?>