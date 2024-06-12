<?php
session_start();

$books = json_decode(file_get_contents('data/books.json'), true);

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $users = json_decode(file_get_contents('users.json'), true);
    if(isset($users[$id])){
        $user = $users[$id];
    } else {
        header('location:index.php');
        exit;
    }    
} else {
    header('location:index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($user['username']) ?>'s Profile - IK-Library</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/cards.css">
</head>
<body>
    <header>
        <h1><a href="index.php">IK-Library</a> > <?= htmlspecialchars($user['username']) ?>'s Profile</h1>
    </header>
    <div id="content">
        <h2>Username: <?= htmlspecialchars($user['username']) ?><br></h2>
        <h2>Email: <?= htmlspecialchars($user['email']) ?> <br></h2>
        <h2>ID: <?= htmlspecialchars($user['id']) ?> <br></h2>

        <?php if (isset($user['reviews']) && !empty($user['reviews'])): ?>
            <h3>Reviews left by <?= htmlspecialchars($user['username']) ?>:</h3>
            <ul>
                <?php foreach ($user['reviews'] as $review): ?>
                    <?php if (isset($books[$review['book_id']])): ?>
                        <li>
                            <strong>Book: <?= htmlspecialchars($books[$review['book_id']]['title']) ?></strong><br>
                            <?= htmlspecialchars($review['review']) ?> (<?= $review['rating'] ?>)
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p><?= htmlspecialchars($user['username']) ?> has not left any reviews yet.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>
</html>
