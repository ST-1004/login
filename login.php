<!DOCTYPE html>
<?php
require('mydb.php');

session_start();

if (!empty($_POST)) {
    if ($_POST['email'] != '' && $_POST['password'] != '') {
        $login = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');
        $login->execute(array($_POST['email'],
        sha1($_POST['password'])
        ));
        $members = $login->fetch();

        if ($members) {
            $_SESSION['id'] = $members['id'];
            $_SESSION['time'] = time();

            header('Location: index.php');
            exit();
        } else {
            $error['login'] = 'failed';
        }
    } else {
        $error['login'] = 'blank';
    }
}
?>

<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ひとこと掲示板</title>

	<link rel="stylesheet" href="style.css">
</head>
<body>
<div id="wrap">
  <div id="head">
    <h1>ログインする</h1>
  </div>
    <div id="content">
        <div id="lead">
            <p>メールアドレスとパスワードを記入してログインしてください。</p>
            <p>入会がまだの方はこちらからどうぞ。</p>
            <p>&raquo;<a href="join/">入会手続きをする</a></p>
        </div>
        <form action="" method="POST">
            <dl>
                <dt>メールアドレス</dt>
                <dd><input type="text" name="email" size="35" maxlength="255" value="<?php if (!empty($_POST['email'])) {
    echo htmlspecialchars($_POST['email'], ENT_QUOTES);
} ?>">
                <?php if (!empty($error['login']) && $error['login'] == 'blank'): ?>
                <p class="error">* メールアドレスとパスワードをご記入ください。<?php echo($error['login']) ?></p>
                <?php endif; ?>
                <?php if (!empty($error['login']) && $error['login'] == 'failed'): ?>
                <p class="error">* ログインに失敗しました。正しくご記入ください。<?php echo($error['login']) ?></p>
                <?php endif; ?>
                </dd>
                <dt>パスワード</dt>
                <dd><input type="password" name="password" size="35" maxlength="255" value="<?php if (!empty($_POST['password'])) {
    echo htmlspecialchars($_POST['password'], ENT_QUOTES);
} ?>"></dd>
                <dt>ログイン情報の記録</dt>
                <dd><input id="save" type="checkbox" name="save" value="on"><label for="save">次回からが自動的にログインする</label></dd>
                <div><input type="submit" value="ログインする"></div>
            </dl>
        </form>
    </div>

</div>
</body>
</html>