<?php
require_once("Storage.php");

function new_storage($filename){
    return new Storage(new JsonIO("$filename.json"),false);
}

$users = new_storage('users');

$username = $_POST['username'] ??'';
$email = $_POST['email'] ??'';
$password = $_POST['password'] ??'';
$password_confirm = $_POST['password_confirm'] ??'';

$match = $users->findOne(["username" => $username]);

if(count($_POST) > 0) {
    $errors = [];
    if(trim($username) === '')
        $errors['username'] = 'Username field is required!';
    else if($match !== null)
        $errors['username'] = 'This username is taken! Please, choose another username';

    if(!filter_var($email,FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'The email address is not valid';

    if(trim($password) === '')
        $errors['password'] = 'Password field is required!';
    if(trim($password_confirm) === '')
        $errors['password_confirm'] = 'Confirm the password';
    if($password !== $password_confirm)
        $errors['password'] = 'Passwords do not match';

    $errors = array_map(fn($e) => "<span>$e</span>", $errors);

    if(count($errors) == 0) {
        $users->add([
            "username" => $_POST['username'],
            "email" => $_POST['email'],
            "password" => $_POST['password'],
            "admin" => 0
        ]);
        header("Location: index.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="styles/form.css">
    <style>
        .login-link {
            margin-top: 10px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
<div class="container">
    <form action="register.php" method="post">
        <label for="username">Name:</label>
        <input type="text" name="username" value="<?= $username ?>" id="username">
        <?= $errors['username'] ?? '' ?>
        
        <label for="email">E-mail:</label>
        <input type="email" name="email" value="<?= $email ?>" id="email">
        <?= $errors['email'] ?? '' ?>
        
        <label for="password">Password:</label>
        <input type="password" name="password" id="password">
        <?= $errors['password'] ?? '' ?>
        
        <label for="password_confirm">Confirm Password:</label>
        <input type="password" name="password_confirm" id="password_confirm">
        <?= $errors['password_confirm'] ?? '' ?>
        
        <button type="submit">Submit</button>
    </form>

    <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
</div>
</body>
</html>
