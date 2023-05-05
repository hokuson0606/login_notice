<?php 
    ini_set("display_errors", 1);
    error_reporting(E_ALL);
    require_once '../functions.php';
    require_once '../classes/UserLogic.php';
    
    session_start();

    
    if($_SESSION['email'] ==''){
        header('Location: ../public/login.php');
        return;
    }
    
    if(isset($_POST['send']) === true){
        UserLogic::post($_POST['send']);
    }

    if( !empty($_POST['logout']) ) {
        UserLogic::logout($_POST['logout']);
    }    

    if( isset($_POST['delete']) === true) {
        $post_id = $_POST['delete'];
        UserLogic::delete($post_id);
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <title>掲示板</title>
</head>
<body>
    <?php if(isset($err['msg'])): ?>
        <p><?php echo $err['msg']; ?></p>
    <?php endif; ?>
    <p>ようこそ<?php echo h($_SESSION['name'] )?>さん</p>
    <form action="notice.php" method="post">
        
        <p>text</p><input type="text" id="text" name="text">
        <input type="submit" name="send" value="送信する">
        <input type="submit" name="logout" value="ログアウト">
    </form>
    <h2>表示欄</h2>
        <?php
            $sql = 'SELECT * FROM tweets WHERE delete_at = 0'; 
            $stmt = connect()->query($sql);
            foreach($stmt as $post_data):?>
            <ul>
                <li><?php h(print($post_data['name']));?></li>
                <br>
                <li><?php h(print($post_data['text']));?></li>
                <li><?php print($post_data['datetime']);?></li>
                <?php if($post_data['user_id'] == $_SESSION['user_id']):?>   
                    <form action="notice.php" method="post">
                        <input value="<?php echo $post_data['post_id']; ?>" type="hidden" name="delete">
                        <input value="削除" type="submit"  >
                    </form>
                <?php endif; ?>
            </ul>
            <?php endforeach; ?>
        
</body>
</html>