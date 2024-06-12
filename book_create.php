<?php
session_start();

$disabled = 'disabled';

$isLoggedIn = isset($_SESSION['user']);
// echo "$isLoggedIn<br>";
if ($isLoggedIn) {
    include_once('Storage.php');
    $stor = new Storage(new JsonIO('users.json'));

    $currentUser = $_SESSION['user'];
    if ($currentUser->admin === 1) {
        $disabled = '';
    }
}

$books = json_decode(file_get_contents('data/books.json'), true);

$title = $_POST['title'] ?? '';
$author = $_POST['author'] ?? '';
$description = $_POST['description'] ?? '';
$year = $_POST['year'] ?? '';
$image = $_POST['image'] ?? '';
$planet = $_POST['planet'] ?? '';

$errors = [];
if ($_POST) {
    if (trim($title) === '') {
        $errors['title'] = 'The title is required.';
    }
    if (trim($author) === '') {
        $errors['author'] = 'The author is required.';
    }
    if (trim($description) === '') {
        $errors['description'] = 'The description is required.';
    }
    if (trim($year) === '' || !is_numeric($year) || $year <= 0) {
        $errors['year'] = 'A valid year is required.';
    }
    if (trim($image) === '') {
        $errors['image'] = 'The image is required.';
    }
    if (trim($planet) === '') {
        $errors['planet'] = 'The planet is required.';
    }
    // echo "Errors: <pre>" . print_r($errors, true) . "</pre><br>";
}
$predefinedImageUrls = [
    'book_cover_1.png',
    'book_cover_2.png',
    'book_cover_3.png',
    'book_cover_4.png',
    'book_cover_5.png',
    'book_cover_6.png',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Book</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/cards.css">
</head>
<body>
    <header>
        <h1><a href="index.php">IK-Library</a> > Home | Welcome, </h1>
        <?php if ($isLoggedIn): ?>
            <a href="user.php?id=<?= htmlspecialchars($currentUser->id) ?>">User Profile: <?= htmlspecialchars($currentUser->username) ?> ||</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </header>
    <div class="main-screen-wrapper">
        <h1 id="screen-name">Create Book</h1>
        <div class="create-book">
            <form method="POST" action="">
                <input type="text" name="title" placeholder="Enter Title" value="<?= htmlspecialchars($title) ?>"><br>
                <input type="text" name="author" placeholder="Enter Author" value="<?= htmlspecialchars($author) ?>"><br>
                <textarea name="description" placeholder="Enter Description"><?= htmlspecialchars($description) ?></textarea><br>
                <input type="number" name="year" placeholder="Enter Year" value="<?= htmlspecialchars($year) ?>"><br>
                
                <label for="image">Select Image URL:</label><br>
                <select name="image" id="image">
                    <?php foreach ($predefinedImageUrls as $url): ?>
                        <option value="<?= htmlspecialchars($url) ?>" <?= $image === $url ? 'selected' : '' ?>>
                            <?= htmlspecialchars($url) ?>
                        </option>
                    <?php endforeach; ?>
                </select><br>
                
                <input type="text" name="planet" placeholder="Enter Planet" value="<?= htmlspecialchars($planet) ?>"><br>
                <button <?= $disabled ?> type="submit" name="create">Create</button>
                <?php if (!$isLoggedIn || $currentUser->admin !== 1): ?>
                    <strong class="warning">Only admin can create a book</strong>
                <?php endif; ?>
            </form>

            <?php if (count($errors) > 0): ?>
                <div class="create-errors">
                    <h1>ERRORS OCCURRED</h1>
                    <?php foreach ($errors as $s): ?>
                        <p><?= htmlspecialchars($s) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_POST['create']) && count($errors) < 1): ?>
                <div class="create-success">
                    <h1>SUCCESS!</h1>
                </div>

                <?php
                $id = uniqid('book');
                $books[$id] = [
                    "id" => $id,
                    "title" => $title,
                    "author" => $author,
                    "description" => $description,
                    "year" => $year,
                    "image" => $image,
                    "planet" => $planet,
                    "ratings" => []
                ];

                file_put_contents('data/books.json', json_encode($books, JSON_PRETTY_PRINT));
                ?>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <p>IK-Library | ELTE IK Webprogramming</p>
    </footer>
</body>
</html>
