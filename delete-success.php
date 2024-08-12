
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
* 掲示場TOPへ自動で遷移する処理★
*/
header('refresh: 3; url=board.php');
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>掲示板アプリ</title>
        <link rel="stylesheet" href="./style.css">
    </head>
    <body>
        <h1>削除が完了しました。</h1>
        <p class="delete-success-msg">3秒後に自動で掲示板TOPへ戻ります。</p>
    </body>
</html>