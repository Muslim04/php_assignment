<?php
$reg = json_decode(file_get_contents("data/books.json"), true);

session_start();

$admin = false;
$curr_user = false;

if (isset($_SESSION['user'])) {
    $curr_user = true;
    $user = $_SESSION['user'];
    if ($user->admin == 1) {
        $admin = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IK-Library</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/cards.css">
</head>
<body>
<header>
    <h1><a href="index.php">IK-Library</a> > Home | Welcome, </h1>
    <?php if ($curr_user): ?>
        <a href="user.php?id=<?= $user->id ?>">User Profile: <?= $user->username ?> ||</a>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    <?php endif; ?>
    <?php if ($admin): ?>
        <a href="book_create.php">Create book</a>
    <?php endif; ?>
</header>
<div id="content">
    <div id="card-list">
        <?php foreach ($reg as $book): ?>
            <div class="book-card">
                <div class="image">
                    <img src="assets/<?= $book['image'] ?>" alt="">
                </div>
                <div class="details">
                    <h2><a href="book.php?id=<?= $book['id'] ?>"> <?= $book['title'] ?></
                    ></a></h2>
                </div>
                <?php if ($admin): ?>
                    <div class="edit">
                        <a href="edit_book.php?id=<?= $book['id'] ?>">Edit</a>
                    </div>
                <?php endif; ?>
                <?php if ($curr_user): ?>
                    <div class="read-status">
                        <?php if (isset($user -> books_read) && in_array($book['id'], $user -> books_read)): ?>
                            <p><strong>Read Status:</strong> Marked as read</p>
                        <?php else: ?>
                            <form method="POST" action="book.php?id=<?= $book['id'] ?>">
                                <button type="submit" name="mark_read">Mark as Read</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<footer>
    <p>IK-Library | ELTE IK Webprogramming</p>
</footer>
</body>
</html>
