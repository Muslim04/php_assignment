<?php
session_start();

if(isset($_GET['id'])){
    $id = $_GET['id'];
    $books = json_decode(file_get_contents('data/books.json'), true);
    if(isset($books[$id])){
        $book = $books[$id];
    } else {
        header('location:index.php');
        exit;
    }    
} else {
    header('location:index.php');
    exit;
}

$totalRating = 0;
if (!empty($book['ratings'])) {
    foreach ($book['ratings'] as $rating) {
        $totalRating += $rating['rating'];
    }
    $average_rating = $totalRating / count($book['ratings']);
} else {
    $average_rating = 0;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review'])) {
    $review = $_POST['review'];
    $rating = $_POST['rating'];

    if (trim($review) === '') {
        $errors['review'] = 'Review is required.';
    }
    if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
        $errors['rating'] = 'Rating must be between 1 and 5.';
    }

    if (empty($errors)) {
        $newRating = [
            'username' => $_SESSION['user']->username,
            'review' => htmlspecialchars($review),
            'rating' => (int) $rating
        ];
        $books[$id]['ratings'][] = $newRating;
        file_put_contents('data/books.json', json_encode($books, JSON_PRETTY_PRINT));
        
        $users = json_decode(file_get_contents('users.json'), true);
        $userId = $_SESSION['user']->id;
        if (isset($users[$userId])) {
            $users[$userId]['reviews'][] = [
                'book_id' => $id,
                'review' => htmlspecialchars($review),
                'rating' => (int) $rating
            ];
            file_put_contents('data/users.json', json_encode($users, JSON_PRETTY_PRINT));
        }

        header("Location: book.php?id=$id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($book['title']) ?> - IK-Library</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/cards.css">
</head>
<body>
    <header>
        <h1><a href="index.php">IK-Library</a> > Detailed Description:</h1>
    </header>
    <div id="content">
        <img src="assets/<?= htmlspecialchars($book['image']) ?>" alt="Book Cover"><br>
        <h3>Title: <?= htmlspecialchars($book['title']) ?></h3> 
        <h3>Author: <?= htmlspecialchars($book['author']) ?></h3>
        <p><strong>Description:</strong><br> <?= nl2br(htmlspecialchars($book['description'])) ?></p> 
        <h3>Year: <?= htmlspecialchars($book['year']) ?></h3> 
        <h3>Source Planet: <?= htmlspecialchars($book['planet']) ?></h3> 
        <h3>Average Rating: <?= number_format($average_rating, 1) ?></h3>

        <?php if (!empty($book['ratings'])): ?>
            <h3>Reviews:</h3>
            <ul>
                <?php foreach ($book['ratings'] as $rating): ?>
                    <li>
                        <strong>Rating by <?= htmlspecialchars($rating['username']) ?>:</strong><br>
                        <?= htmlspecialchars($rating['review']) ?> (<?= $rating['rating'] ?>)
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (isset($_SESSION['user'])): ?>
            <h3>Add Your Review:</h3>
            <form method="POST" action="">
                <textarea name="review" placeholder="Enter your review"><?= isset($_POST['review']) ? htmlspecialchars($_POST['review']) : '' ?></textarea><br>
                <?php if (isset($errors['review'])): ?>
                    <p class="error"><?= $errors['review'] ?></p>
                <?php endif; ?>
                <label for="rating">Rating (1-5):</label>
                <input type="number" name="rating" id="rating" min="1" max="5" value="<?= isset($_POST['rating']) ? $_POST['rating'] : '' ?>"><br>
                <?php if (isset($errors['rating'])): ?>
                    <p class="error"><?= $errors['rating'] ?></p>
                <?php endif; ?>
                <button type="submit" name="submit">Submit Review</button>
            </form>
        <?php else: ?>
            <p>You need to <a href="login.php">login</a> to leave a review.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>
</html>
