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
        $sql = 'INSERT INTO users (name, email, password) VALUES (?,?,?)';

        //ユーザーデータを配列に入れる
        $arr = [];
        $arr[] = $userData['username'];
        $arr[] = $userData['email'];
        $arr[] = password_hash($userData['password'],PASSWORD_DEFAULT);
        try{
            $stmt = connect()->prepare($sql);
            $result = $stmt->execute($arr);   
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
            foreach ($stmt as $name){
                $_SESSION['name'] = $name['name'];
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
            $email = h($_SESSION['email']);
            $name = h($_SESSION['name']);
            $text = h($_POST['text']);
            $sql = "INSERT INTO tweets (id,email,name,text,datetime,delete_at) VALUE(NULL,?,?,?,?,?)";
            if (strlen($text) >= 1 && strlen($text) <= 200) {
            $stmt = connect()->prepare($sql);
            $stmt->bindParam(1, $email);
            $stmt->bindParam(2, $name);
            $stmt->bindParam(3, $text);
            $stmt->bindParam(4, $date);
            $stmt->bindParam(5, $delete_at);
            $stmt->execute();
            echo '投稿しました';
            header('Location: ../notice/notice.php');
            return;
    } else {
        echo '投稿エラー: 200文字以内で入力してください';
    }
    
    }

    /**
     *削除機能
     * @param string $
     * @param string $
     * @return bool $
     */
    public static function delete(){ 
            
            $sql = "UPDATE tweets SET delete_at = :delete_at WHERE email = :email ";
            $stmt = connect()->prepare($sql);
            $stmt-> execute(array(':delete_at' => '1', ':email' => $_SESSION['email']));
            
        
    }
}

?>