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
        UserLogic::delete($_POST['delete']);
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
            $sql = 'SELECT * FROM tweets '; 
            $stmt = connect()->query($sql);
            foreach($stmt as $message):?>
            <ul>
                <li><?php h(print($message['name']));?></li>
                <br>
                <li><?php h(print($message['text']));?></li>
                <li><?php print($message['datetime']);?></li>
                <?php if($message['user_id'] == $_SESSION['user_id']): 
                    /*
                $message['name'] == $_SESSION['name']かつdelete_atが1の時には投稿を表示させないようにしたい
            */
                    ?>
                <form action="notice.php" method="post">
                    <input value="削除" type="submit" name="delete" href="notice.php?id=<?php echo htmlspecialchars($message['email'], ENT_QUOTES); ?>">
                </form>
                <?php endif; ?>
            </ul>
            <?php endforeach; ?>
        
</body>
</html>