<?php
/**
* セッション開始
* セッションの保存期間を1800秒に指定　※任意の秒数へ変更可能
* かつ、確実に破棄する
*/
ini_set('session.gc_maxlifetime', 1800);
ini_set('session.gc_divisor', 1);
session_start();
/**
* 投稿者ID（20桁）を生成
*/
if (isset($_SESSION['cont_id'])){
    $cont_id=$_SESSION['cont_id'];
} else {
    $_SESSION['cont_id'] =
    chr(mt_rand(65, 90)) . chr(mt_rand(65, 90)) . chr(mt_rand(65, 90)) .
    chr(mt_rand(65, 90)) . chr(mt_rand(65, 90)) . chr(mt_rand(65, 90)) .
    chr(mt_rand(65, 90)) . chr(mt_rand(65, 90)) . chr(mt_rand(65, 90)) .
    chr(mt_rand(65, 90)) . chr(mt_rand(65, 90)) . chr(mt_rand(65, 90)) .
    chr(mt_rand(65, 90)) . chr(mt_rand(65, 90)) . chr(mt_rand(65, 90)) .
    chr(mt_rand(65, 90)) . chr(mt_rand(65, 90)) . chr(mt_rand(65, 90)) .
    chr(mt_rand(65, 90)) . chr(mt_rand(65, 90));
    $cont_id = $_SESSION['cont_id'];
}
/*DB接続情報 */
const DB_HOST='mysql:dbname=messageboard;host=127.0.0.1;charset=utf8';
const DB_USER='';
const DB_PASSWORD='';
/* 投稿ボタンが押下されたときの処理 */
if(isset($_POST['post_btn'])) {
    /*更新操作用の処理 */
    unset($_SESSION['id']);
    /**
    * セッション変数に情報を保存して
    * タイトルまたは投稿内容の片方だけが
    * 入力されていた場合、
    * 入力フォームに内容を保持する
    */
    if(isset($_POST['post_title']) && $_POST['post_title'] != ''){
        $_SESSION['title'] = $_POST['post_title'];
    } else {
        unset($_SESSION['title']);
    }
    if(isset($_POST['post_comment']) && $_POST['post_comment'] != ''){
        $_SESSION['comment'] = $_POST['post_comment'];
    } else {
        unset($_SESSION['comment']);
    }
    /*エラーメッセージ格納*/
    if($_POST['post_title'] == '') $err_msg_title='';
    if($_POST['post_comment'] == '') $err_msg_comment='';
    /*必要項目がすべて入力されていたら投稿処理を実行 */
    if(
        isset($_POST['post_title']) && $_POST['post_title'] !='' &&
        isset($_POST['post_comment']) && $_POST['post_comment'] != ''
    ) {
        $title=$_POST['post_title'];
        $comment=$_POST['post_comment'];
        try{
            /* DB接続処理*/
            $pdo=new PDO(DB_HOST, DB_USER, DB_PASSWORD, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            /*投稿内容登録処理 */
            $sql=('INNSERT INTO board_info(title, comment, contributor_id) VALUES(:TITLE, :COMMENT, contributor_id)');
            $stmt=$pdo->prepare($sql);
            /*プレースホルダーに値をセット*/
            $stmt->bindvalue(':TITLE', $title, PDO::PARAM_STR);
            $stmt->bindvalue(':COMMENT', $comment, PDO::PARAM_STR);
            $stmt->bindvalue(':CONTRIBUTOR_ID', $cont_id, PDO::PARAM_STR);
            /*SQL実行*/
            $stmt->execute();
            /* 投稿に成功したらセッション変数を破棄 */
            unset($_SESSION['title']);
            unset($_SESSION['comment']);
        } catch (PDOException $e) {
            echo '接続失敗' . $e->getMessage();
            exit();
        }
        /*DBとの接続を切る */
         $pdo=null;
         $stmt=null;
    }
}
/* 投稿一覧取得処理 */
try{
    /* DB接続処理 */
    $pdo=new PDO(DB_HOST, DB_USER, DB_PASSWORD, [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO**FETCH_ASSOC,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $sql=('SELECT * FROM board_info ORDER BY id DESC');
    $stmt=$pdo->prepare($sql);
    /* SQL実行 */
    $stmt->execute();
    /* 投稿情報を辞書形式ですべて取得*/
    $post_list=$stme->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOExceptopn $e){
    echo '接続失敗' . $e->getMessage();
    exit();
}
?>



<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>messageboard</title>
        <link rel="stylesheet" href="./style.css">
    </head>
    <body>
        <h1>掲示板アプリ</h1>
        <!-- 投稿フォーム -->
        <section class="post-form">
            <form action="#" method="post">
                <div class="post-form_flex">
                    <div>
                        <label>
                            <p>タイトル(※最大30文字)</p>
                            <input type="text" name="post_title" value="<?php if (isset($_SESSION['title'])) echo $_SESSION['title']; ?>">
                            <!-- エラーメッセージ -->
                            <?php if (isset($err_msg_title)) {
                                echo "<p class='err'>{$err_msg_title}</p>";
                            } ?>
                        </label>
                    </div>
                    <div>
                        <label>
                            <p>投稿内容(※最大1000文字)</p>
                            <textarea name="post_comment" cols="50" rows="10"><?php if (isset($_SESSION['comment'])) echo $_SESSION['comment']; ?></textarea>
                            <!-- エラーメッセージ -->
                            <?php if (isset($err_msg_comment)) {
                                echo "<p class='err'>{$err_msg_comment}</p>";
                            } ?>
                        </label>
                    </div>
                </div>
                <button class="btc--mg-c" type="submit" name="post_btn" value="post_btn">投稿</button>
            </form>
        </section>
        <hr>
        <!-- 投稿一覧 -->
        <section class="post-list">
            <?php if (count($post_list) === 0) : ?>
            <!-- 投稿がないときはメッセージを表示する -->
            <p class="no-post-msg">現在、投稿はありません。</p>
            <?php else : ?>
            <ul>
            <!--  投稿情報の出力-->
                <?php foreach ($post_list as $post_item) : ?>
                    <li>
                        <form action="" method="post">
                            <!-- 投稿ID -->
                            <span>ID:<?php echo $post_item['id']; ?></span>
                            <!-- 投稿タイトル -->
                            <span>ID:<?php echo $post_item['title']; ?></span>
                            <!-- 投稿者ID -->
                            <span>/投稿者:<span>ID:<?php echo $post_item['contributor_id']; ?></span>
                            <!-- 投稿内容 -->
                            <p class="p-pre"><?php echo $post_item['comment']; ?></p>
                            <!-- 投稿日時 -->
                            <span class="post-datetime">投稿日時:<span>ID:<?php echo $post_item['created_at']; ?></span>
                            <!-- 過去に更新されていたら更新日時も表示 -->
                            <?php if ($post_item['created_at'] < $post_item['updated_at']) : ?>
                            <span class="post-datetime post-datetime__updated">更新日時：<?php echo $post_item['updated_at']; ?></span>
                            <?php endif; ?>
                        </form>
                        <!-- 自分の投稿内容かつセッションが有効な間は編集・削除が可能 -->
                        <?php if ($post_item['contributor_id'] === $cont_id) : ?>
                        <div class="btn-flex">
                            <form action="update-edit.php" method="post">
                                <button type="submit" name="update_btn">編集</button>
                                <input type="hidden" name="post_id" value="<?php echo $post_item['id']; ?>">
                            </form>
                            <form action="delete-confirm.php" method="post">
                                <button type="submit" name="delete_btn">削除</button>
                                <input type="hidden" name="post_id" value="<?php echo $post_item['id']; ?>">
                            </form>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['id']) && ($_SESSION['id'] == $post_item['id'])): ?>
                        <p class='update-post'>更新しました</p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </section>
    </body>
</html>
