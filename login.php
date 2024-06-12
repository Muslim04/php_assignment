<?php
require_once("Storage.php");

function new_storage($filename){
    return new Storage(new JsonIO("$filename.json"), false);
}

$users = new_storage('users');

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (count($_POST) > 0) {
    $errors = [];
    if (trim($username) === '') {
        $errors['username'] = 'Username field is required!';
    }

    if (trim($password) === '') {
        $errors['password'] = 'Password field is required!';
    }

    if (count($errors) === 0) {
        $match = $users->findOne(["username" => $username]);

        if ($match === null) {
            $errors['username'] = 'No such user found!';
        } else if ($match->password !== $password) {
            $errors['password'] = 'Incorrect password!';
        } else {
            session_start();
            $_SESSION['user'] = $match;
            header("Location: index.php");
            exit();
        }
    }

    $errors = array_map(fn($e) => "<span>$e</span>", $errors);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles/form.css">
    <style>
        .register-link {
            margin-top: 10px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
<div class="container">
    <form action="login.php" method="post">
        <label for="username">Username:</label>
        <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" id="username" required>
        <?= $errors['username'] ?? '' ?>
        
        <label for="password">Password:</label>
        <input type="password" name="password" value="<?= htmlspecialchars($password) ?>" id="password" required>
        <?= $errors['password'] ?? '' ?>
        
        <button type="submit">Login</button>
    </form>

    <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
</div>
</body>
</html>
