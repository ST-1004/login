<?php
session_start();
require('../mydb.php');

if (!empty($_POST)) {
    if (($_POST['name']) == '') {
        $error['name'] = 'blank';
    }
    if ($_POST['email'] == '') {
        $error['email'] = 'blank';
    }
    if (strlen($_POST['password']) < 4) {
        $error['password'] = 'length';
    }
    if ($_POST['password'] == '') {
        $error['password'] = 'blank';
    }
    $fileName = $_FILES['image']['name'];
    if (!empty($fileName)) {
        $ext = substr($fileName, -3);
        if ($ext != 'jpg' && $ext != 'gif') {
            $error['image'] = 'type';
        }
    }

    if (!empty($_POST['email'])) {
        $member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
        $member->execute(array($_POST['email']));
        $record = $member->fetch();
        if ($record['cnt'] > 0) {
            $mailchecked = 'ok';
            $error = 'blank';
        }
    }

    if (empty($error)) {
        $image = date('YmdHis') . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/' .$image);
        $_SESSION['join'] = $_POST;
        $_SESSION['join']['image'] = $image;
        header('Location: check.php');
        exit();
    }
}
if (isset($_REQUEST['action']) == 'rewrite') {
    $_POST = $_SESSION['join'];
    $error['rewrite'] = true;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ひとこと掲示板</title>

	<link rel="stylesheet" href="../style.css">
</head>

<body>
<div id="wrap">
  <div id="head">
    <h1>会員登録</h1>
  </div>
  <div id="content">
		<p>次のフォームに必要事項をご記入ください。</p>
		<form action="" method="post" enctype="multipart/form-data">
		<dl>
		<dt>ニックネーム<span class="required">必須</span></dt>
		<dd><input type="text" name="name" size="35" maxlength="255" value="<?php if (isset($_POST['name'])!=false) {
    echo htmlspecialchars(($_POST['name']), ENT_QUOTES);
} ?>"/>
			<?php if (isset($error['name']) == 'blank'): ?>
			<p class="error">* ニックネームを入力してください</p>
			<?php endif; ?>
		</dd>
		<dt>メールアドレス<span class="required">必須</span></dt>
		<dd>
			<input type="text" name="email" size="35" maxlength="255" value="<?php if (isset($_POST['email'])!=false) {
    echo htmlspecialchars($_POST['email'], ENT_QUOTES);
} ?>"/>
      <?php if (!empty($error['email'])): ?>
      <p class="error">* メールアドレスを入力してください</p>
      <?php endif; ?>
		<?php if (!empty($mailchecked) && $mailchecked == 'ok'): ?>
		<p class="error">* メールアドレスが重複しています</p>
			<?php endif; ?>
		</dd>
		<dt>パスワード<span class="required">必須</span></dt>
		<dd>
			<input type="password" name="password" size="10" maxlength="20" value="<?php if (isset($_POST['password'])!=false) {
    echo htmlspecialchars($_POST['password'], ENT_QUOTES);
} ?>"/>
      <?php if (isset($error['password']) == 'blank'): ?>
				<p class="error">* パスワードを入力してください</p>
			<?php endif; ?>
			<?php if (isset($error['password']) == 'length'): ?>
				<p class="error">* パスワードは4文字以上で入力してください</p>
			<?php endif; ?>
		</dd>
		<dt>アイコン用の画像</dt>
		<dd><input type="file" name="image" size="35" />
			<?php if (isset($error['image']) == 'type'): ?>
			<p class="error">* 写真などは「.gif」または「.jpg」の画像を指定してください
			</p>
			<?php endif; ?>
			<?php if (!empty($error)): ?>
			<p class="error">* 恐れ入りますが、画像を改めて指定してください</p>
			<?php endif; ?>
		</dd>
		</dl>
		<div><input type="submit" value="入力内容を確認する" /></div>
		</form>
  </div>

</div>
</body>
</html>