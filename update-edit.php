<?php
/**
* セッション開始
* セッションの保存期間を1800秒に指定　※任意の秒数へ変更可能
* かつ、確実に破棄する
*/
ini_set('session.gc_maxlifetime', 1800);
ini_set('session.gc_divisor', 1);
session_start();
// DB接続情報
const DB_HOST='mysql:dbname=messageboard;host=127.0.0.1;charset=utf8';
const DB_USER='';
const DB_PASSWORD='';

// 編集ホタンで遷移してきたときの処理
if(isset($_POST['update_btn'])){
    // 課題対象の投稿情報を取得
    if(isset($_POST['post_id']) && $_POST['post_id'] != ''){
        // セッションに投稿IDを保持
        $_SESSION['id'] = $_POST['post_id'];
        try
        {
            // DB接続j処理
            $pdo=new PDO(DB_HOST, DB_USER, DB_PASSWORD,[
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            // 投稿内容取得処理
            $sql=('SELECT id, title, comment FROM board_info WHERE id = :ID');
            $stmt=$pdo->prepare($sql);
            // プレースホルダーに値をセット
            $stmt->bindValue(':ID', $_SESSION['id'], PDO::PARAM_INT);
            // SQL実行
            $stmt->execute();
            // 投稿情報の取得
            $post_info=$stmt->fetch();
            $_SESSION['title']=$_post_info['title'];
            $_SESSION['comment']=$post_info['comment'];
        } catch(PDOException $e) {
            echo '接続失敗' . $e->getMessage();
            exit();
        }
        // DBとの接続を切る
        $pdo=null;
        $stmt=null;
    }
}
?>
// 更新ボタンが押下されたときの処理
if(isset($_POST['updae_submit_btn'])){
    /**
    * セッション変数に情報を保存して
    * タイトルまたは投稿内容の片方だけが
    * 入力されていた場合、
    * 入力フォームに内容を保持する
    */
    
    if(isset($_POST['post_title']) && $_POST['post_title'] != '') {
        $_SESSION['title'] = $_POST['post_title'];
    } else {
        unset($_SESSION['comment']);
    }
    // エラーメッセージ格納
    if($_POST['post_title'] == '') $err_msg_title ='';
    if($_POST['post_comment'] == '') $err_msg_comment = '';
    // 必要項目がすべて入力されていたら更新処理を実行

    if(isset($_POST['post_title']) && $_POST['post_title'] != '' && 
    isset($_POST['post_comment']) && $_POST['post_comment'] != '') 
    {
        $title=$_POST['post_title'];
        $comment=$_POST['post_comment'];
        try
        {
            // DB接続処理
            $pdo=new PDO(DB_HOST, DB_USER, DB_PASSWORD, [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            // 投稿内容更新処理
            $sql=('UPDATE board_info SET title = :TITLE, comment = :COMMENT WHERE id = :ID');
            $stmt=$pdo->prepare($sql);
            // プレースホルダーに値をセット
            $stmt->bindValue(':ID', $_SESSION['id'], PDO::PARAM_INT);
            $stmt->bindValue(':TITLE', $title, PDO::PARAM_STR);
            $stmt->bindValue(':COMMENT', $comment, PDO::PARAM_STR);
            // SQL実行
            $stmt->execute();
            // 更新に成功したらセッション変数を破棄
            // unset($_SESSION['id']);  // ※投稿IDは敢えて破棄せず、掲示板ページでID判定をするために情報を保持する★
            unset($_SESSION['title']);
            unset($_SESSION['comment']);
            // 掲示板ページに戻る
            header('Location: board.php');
            exit();
        }
        catch(PDOException $e) {
            echo '接続失敗' . $e->getMessage();
            exit();
        }
        // DBとの接続を切る
        $pdo=null;
        $stmt=null;
    }
}
/**
* キャンセルボタンが押下されたら
* セッション情報を破棄して
* 掲示板一覧画面へ戻る
*/
if(isset($_POST['catch_btn'])){
    unset($_SESSION['id']);
    unset($_SESSION['title']);
    unset($_SESSION['comment']);
    header('Location: board.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <mata charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>掲示板アプリ</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <h1>投稿編集画面</h1>
        <!-- 投稿編集フォーム -->
         <section class="post-form">
            <form action="#" method="post">
                <div class="post-form_flex">
                    <div>
                        <label>
                            <p>タイトル</p>
                            <input type="text" name="post_title" value="<?php if (isset($_SESSION['title'])) echo $_SESSION['title']; ?>">
                            <!-- エラーメッセージ -->
                            <?php if (isset($err_msg_comment)) echo "<p class='err'>{$err_msg_comment}</p>"; ?>
                        </label>
                    </div> 
                </div>  
                <div class="btn-flex">
                    <button type="submit" name="update_submit_btn" value="update_submit_btn">更新</button>
                    <button type="submit" name="cancel_btn" value="cancel_btn">キャンセル</button>
                </div>
            </form>
        </section>
    </body>
</html>
                        
