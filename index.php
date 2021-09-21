<?php
session_start();
require('mydb.php');

if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    $_SESSION['time'] = time();

    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    $members->execute(array($_SESSION['id']));
    $member = $members->fetch();
} else {
    header('Location: login.php');
    exit();
}

if (!empty($_POST)) {
    if ($_POST['message'] != '') {
        $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, created=NOW()');
        $message->execute(array(
            $member['id'],
            $_POST['message']
        ));

        header('Location: index.php');
        exit();
    }
}
if (empty($_REQUEST['page'])) {
    $page = 1;
} else {
    $page = $_REQUEST['page'];
    $page = max($page, 1);
}

$counts = $db->query('SELECT COUNT(*) AS cnt FROM posts');
$cnt = $counts->fetch();
$maxPage = ceil($cnt['cnt'] / 5);
$page = min($page, $maxPage);

$start = ($page - 1) * 5;
$posts = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC LIMIT ?,5');
$posts->bindParam(1, $start, PDO::PARAM_INT);
$posts->execute();

if (isset($_REQUEST['res'])) {
    $response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m,	posts p WHERE m.id=p.member_id AND p.id=? ORDER BY p.created DESC');
    $response->execute(array($_REQUEST['res']));
    $table = $response->fetch();
    $message = ('@' . $table['name'] . ' ' . $table['message']);
}
function h($value)
{
    return htmlspecialchars($value, ENT_QUOTES);
}
function makeLink($value)
{
    return mb_ereg_replace("(https?)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)", '<a href="\1\2">\1\2</a>', $value);
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
        <h1>ひとこと提示版</h1>
    </div>
    <div id="content">
    <div style="text-align: right;"><a href="logout.php">ログアウト</a></div>
        <form action="" method="POST">
            <dl>
                <dt><?php echo h($member['name']); ?>さん、メッセージをどうぞ</dt>
                <dd><textarea name="message" cols="50" rows="5"><?php if (!empty($message)) {
    echo h($message);
} ?></textarea>
                <input type="hidden" name="reply_post_id" value="<?php if (!empty($_REQUEST['res'])) {
    echo h($_REQUEST['res']);
} ?>" />
                </dd>    
            </dl>
            <div>
                <input type="submit" value="投稿する">    
            </div>
        </form>
        <?php
        foreach ($posts as $post):
        ?>
		<div class="msg">
			<img src="member_picture/<?php echo htmlspecialchars($post['picture'], ENT_QUOTES); ?>" width="48" height="48" alt="<?php echo htmlspecialchars($post['name'], ENT_QUOTES); ?>" />
			<p><?php echo htmlspecialchars($post['message'], ENT_QUOTES); ?><span class="name">（<?php echo htmlspecialchars($post['name'], ENT_QUOTES); ?>）</span>
            [<a href="index.php?res=<?php if (!empty($post['id'])) {
            echo htmlspecialchars($post['id']);
        } ?>">Re</a>]</p>
			<p class="day"><?php echo htmlspecialchars($post['created'], ENT_QUOTES); ?></p>
            <?php if ($post['reply_post_id'] > 0): ?>
                <a href="view.php?id=<?php echo htmlspecialchars($post['reply_post_id'], ENT_QUOTES); ?>">返信先のメッセージ</a>
            <?php endif ?>

            <?php if ($_SESSION['id'] == $post['member_id']): ?>
					[<a href="delete.php?id=<?php echo htmlspecialchars($post['id'], ENT_QUOTES); ?>" style="color:#F33;">削除</a>]
			<?php endif; ?></p>
		</div>
		<?php endforeach; ?>
        <ul class="paging">
            <?php if ($page > 1) { ?>
            <li><a href="index.php?page=<?php print($page - 1); ?>">前のページへ</a></li>
            <?php } else { ?>
            <li>前のページへ</li><?php } ?>
            <?php if ($page < $maxPage) { ?>
            <li><a href="index.php?page=<?php print($page + 1); ?>">次のページへ</a></li>
            <?php } else { ?>
            <li>次のページへ</li> <?php } ?>
        </ul>
    </div>
</div>
</body>
</html>