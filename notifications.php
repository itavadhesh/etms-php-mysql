<?php 
session_start();
// सुनिश्चित करें कि यूज़र लॉग इन है
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {

    include "DB_connection.php";
    
    $current_user_id = $_SESSION['id'];
    $notifications = []; // शुरू में खाली ऐरे

    try {
        // --- 1. (बदलाव) पहले सभी नोटिफिकेशन को लाएँ ---
        $sql_get_all = "SELECT * FROM notifications WHERE recipient = ? ORDER BY created_at DESC";
        $stmt_get = $conn->prepare($sql_get_all);
        $stmt_get->execute([$current_user_id]);

        if($stmt_get->rowCount() > 0){
            $notifications = $stmt_get->fetchAll();
        }

        // --- 2. (बदलाव) अब, जब नोटिफिकेशन आ गए हैं, तब उन्हें 'पढ़ा हुआ' मार्क करें ---
        $sql_update_read = "UPDATE notifications SET is_read = 1 WHERE recipient = ? AND is_read = 0";
        $stmt_update = $conn->prepare($sql_update_read);
        $stmt_update->execute([$current_user_id]);

    } catch (PDOException $e) {
        // कोई एरर हो तो
        $error_message = "Database error: " . $e->getMessage();
    }

?>
<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <h4 class="title">All Notifications</h4>
            
            <?php if (isset($error_message)) { ?>
                <div class="danger" role="alert"><?= $error_message ?></div>
            <?php } ?>

            <?php if (isset($_GET['success'])) {?>
            <div class="success" role="alert">
              <?php echo stripcslashes($_GET['success']); ?>
            </div>
            <?php } ?>
            
            <?php if (count($notifications) > 0) { ?>
            <table class="main-table">
                <tr>
                    <th>Sr.No.</th>
                    <th style="width: 50%;">Notices / Messages</th>
                    <th>Type</th>
                    <th>Date & Time</th>
                </tr>
                <?php $i=0; foreach ($notifications as $notification) { 
                    
                    // --- ⬇️ (बदलाव) 'unread' क्लास को यहाँ जोड़ा गया है ⬇️ ---
                    $tr_class = ($notification['is_read'] == 0) ? 'tr-unread' : '';
                ?>
                
                <tr class="<?= $tr_class ?>"> <td><?=++$i?></td>
                    <td><?=$notification['message']?></td>
                    <td><?=$notification['type']?></td>
                    <td><?= date('d M Y, h:i A', strtotime($notification['created_at'])) ?></td> 
                </tr>
               <?php    } // foreach end ?>
            </table>
            <?php } else { ?>
                <h3>You have zero notification</h3>
            <?php   } // else end ?>
            
        </section>
    </div>


<script type="text/javascript">
    // नेविगेशन में 'Notifications' को एक्टिव करें
    var active = document.querySelector("#navList li:nth-child(4)"); 
    if (active) active.classList.add("active");
</script>


</body>
</html>
<?php 
}else{ 
    // अगर यूज़र लॉग इन नहीं है
    $em = "First login";
    header("Location: login.php?error=$em");
    exit();
}
?>