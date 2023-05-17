<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);
require_once '../dbconnect.php';
require_once '../functions.php';
class UserLogic

{
    /**
     * ユーザーを登録する
     * @param array $userData
     * @return bool $result
     */
    public static function createUser($userData)
    {
        $result = false;
        $sql = 'INSERT INTO users (name, email, password,delete_at_user) VALUES (?,?,?,?)';

        //ユーザーデータを配列に入れる
        $delete_at_user = "0";
        $password = password_hash($userData['password'],PASSWORD_DEFAULT);
        try{
            $stmt = connect()->prepare($sql);
            $stmt->bindParam(1, $userData['username']);
            $stmt->bindParam(2, $userData['email']);
            $stmt->bindParam(3, $password);
            $stmt->bindParam(4, $delete_at_user);
            $result = $stmt->execute();   
            return $result;           
        }catch(Exception $e){
            return $result;
        }
    }


    /**
     *ログイン処理
     * @param string $email
     * @param string $password
     * @return bool $result
     */
    public static function login($email,$password)
    {
        $result = false;
        $user = self::getUserByEmail($email);

        if(!$user){
            $_SESSION['msg'] = 'emailが一致しません。';
            return $result;
        }

        if(password_verify($password,$user['password'])){
            session_regenerate_id(true);
            $_SESSION['login_user'] = $user;
            $_SESSION['email'] = $email;
            $sql = "SELECT * FROM users WHERE email = '$email' ";
            $stmt = connect()->query($sql);
            $stmt->execute();
            foreach ($stmt as $user){
                $_SESSION['name'] = $user['name'];
                $_SESSION['user_id'] = $user['user_id'];
                //$_SESSION['name'] は送れてる
            }
            $result = true;
            return $result;
        }

        $_SESSION['msg'] = 'パスワードが一致しません。';
        return $result;
        
    }

    public static function logout(){
        if( !empty($_POST['logout']) ) {
            unset($_SESSION['email']);
            header('Location: ../public/login.php');
            return;
        } 
    }

    /**
     *emailからユーザーを取得
     * @param string $email
     * @return bool $result
     */
    public static function getUserByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = ?';

        //ユーザーデータを配列に入れる
        $arr = [];
        $arr[] = $email;
        try{
            $stmt = connect()->prepare($sql);
            $stmt->execute($arr);  
            $user = $stmt->fetch(); 
            return $user;           
        }catch(Exception $e){
            return false;
        }
    
    }

    /**
     *投稿処理
     * @param string $name
     * @param string $text
     * @return bool $data
     */
    public static function post($text){ 
            $text = $_POST['text'];
            date_default_timezone_set('Asia/Tokyo');
            $date = date("Y-m-d H:i:s");
            $delete_at = "0";
            $_SESSION['0'] = $delete_at;
            $user_id = h($_SESSION['user_id']);
            $email = h($_SESSION['email']);
            $name = h($_SESSION['name']);
            $text = h($_POST['text']);
            $sql = "INSERT INTO tweets (post_id,user_id,email,name,text,datetime,delete_at) VALUE(NULL,?,?,?,?,?,?)";
            if (strlen($text) >= 1 && strlen($text) <= 200) {
            $stmt = connect()->prepare($sql);
            $stmt->bindParam(1, $user_id);
            $stmt->bindParam(2, $email);
            $stmt->bindParam(3, $name);
            $stmt->bindParam(4, $text);
            $stmt->bindParam(5, $date);
            $stmt->bindParam(6, $delete_at);
            $stmt->execute();
            echo '投稿しました';
            header('Location: ../notice/notice.php');
            return;
    } else {
        echo '投稿エラー: 200文字以内で入力してください';
    }
    
    }

    /**
     *論理削除機能
     * @param string $post_id
     */
    public static function delete($post_id){ 
            $sql = "UPDATE tweets SET delete_at = :delete_at WHERE post_id = :post_id ";
            $stmt = connect()->prepare($sql);
            $stmt-> execute(array(':delete_at' => 1, ':post_id' => $post_id));
            header('Location: ../notice/notice.php');
            exit();
    }

    public static function mail_duplication($email){ 
        $sql = "SELECT user_id FROM users WHERE email = :email";
        $stmt = connect()->prepare($sql);
        $stmt->bindvalue(':email',$email,PDO::PARAM_STR);
        $stmt->execute();
        $count=$stmt->fetch(PDO::FETCH_ASSOC);
        // 重複データの有無をチェック
        if ($count['user_id']>0) {
            // 重複するデータがない場合
        }else{
            // 重複するデータがある場合
        }
    }
}

?>　