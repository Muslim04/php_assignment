<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user'] -> admin != 1) {
    header("Location: index.php");
    exit();
}

function getBookById($id) {
    $books = json_decode(file_get_contents('data/books.json'), true);
    return isset($books[$id]) ? $books[$id] : null;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $description = $_POST['description'];
    $year = $_POST['year'];
    $planet = $_POST['planet'];

    if (empty($title)) {
        $errors['title'] = 'Title is required.';
    }
    if (empty($author)) {
        $errors['author'] = 'Author is required.';
    }
    if (empty($description)) {
        $errors['description'] = 'Description is required.';
    }
    if (empty($year) || !is_numeric($year) || $year < 0) {
        $errors['year'] = 'Valid year is required.';
    }
    if (empty($planet)) {
        $errors['planet'] = 'Source planet is required.';
    }

    if (empty($errors)) {
        $books = json_decode(file_get_contents('data/books.json'), true);
        if (isset($books[$id])) {
            $existingRatings = $books[$id]['ratings'] ?? [];

            $books[$id] = [
                'id' => $id,
                'title' => $title,
                'author' => $author,
                'description' => $description,
                'year' => (int)$year,
                'planet' => $planet,
                'image' => $books[$id]['image'],
                'ratings' => $existingRatings
            ];

            file_put_contents('data/books.json', json_encode($books, JSON_PRETTY_PRINT));
            $success = true;
        } else {
            $errors['general'] = 'Book not found.';
        }
    }
} else {
    if (isset($_GET['id'])) {
        $bookId = $_GET['id'];
        $book = getBookById($bookId);
        if (!$book) {
            $errors['general'] = 'Book not found.';
        }
    } else {
        $errors['general'] = 'Book ID not provided.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <header>
        <h1><a href="index.php">IK-Library</a> > Edit Book</h1>
        <a href="logout.php">Logout</a>
    </header>
    <div id="content">
        <?php if (isset($errors['general'])): ?>
            <p>Error: <?= $errors['general'] ?></p>
        <?php elseif ($success): ?>
            <p style="color: green;">Book details updated successfully!</p>
        <?php else: ?>
            <form action="" method="post">
                <input type="hidden" name="id" value="<?= htmlspecialchars($book['id']) ?>">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" value="<?= htmlspecialchars($book['title']) ?>">
                <?php if (isset($errors['title'])): ?><span><?= $errors['title'] ?></span><?php endif; ?><br>

                <label for="author">Author:</label>
                <input type="text" name="author" id="author" value="<?= htmlspecialchars($book['author']) ?>">
                <?php if (isset($errors['author'])): ?><span><?= $errors['author'] ?></span><?php endif; ?><br>

                <label for="description">Description:</label><br>
                <textarea name="description" id="description" cols="30" rows="5"><?= htmlspecialchars($book['description']) ?></textarea>
                <?php if (isset($errors['description'])): ?><span><?= $errors['description'] ?></span><?php endif; ?><br>

                <label for="year">Year:</label>
                <input type="number" name="year" id="year" value="<?= htmlspecialchars($book['year']) ?>">
                <?php if (isset($errors['year'])): ?><span><?= $errors['year'] ?></span><?php endif; ?><br>

                <label for="planet">Source Planet:</label>
                <input type="text" name="planet" id="planet" value="<?= htmlspecialchars($book['planet']) ?>">
                <?php if (isset($errors['planet'])): ?><span><?= $errors['planet'] ?></span><?php endif; ?><br>

                <button type="submit">Update Book</button>
            </form>
        <?php endif; ?>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>
</html>
