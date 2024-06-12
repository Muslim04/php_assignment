<?php
    require_once("Storage.php");
    function new_storage($filename){
        return new Storage(new JsonIO("$filename.json"),false);
    }
    $users = new_storage('users');

    $username = $_POST['username'] ??'';
    $email = $_POST['email']??'';
    $password = $_POST['password']??'';
    $password_confirm = $_POST['password_confirm']??'';

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
            $errors['password_confirm'] = 'Confirm the the password';
        if($password !== $password_confirm)
            $errors['password'] = 'Passwords doest not match';

        $errors = array_map(fn($e) => "<span style='color: red'> $e </span>", $errors);

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
    <title>registration</title>
    <link rel="stylesheet" href= "styles/form.css">

</head>
<body>

<?php if(count($_POST) > 0 && count($errors) == 0): ?>
        <span style="color: green;">Successfully saved!</span><br>
    <?php endif; ?>
<form action="register.php" method="post">
        Name: <input type="text" name="username" value="<?= $username ?>" id="name" > <?= $errors['username'] ?? '' ?><br>
        E-mail: <input type="email" name="email" value="<?= $email ?>" id="email" > <?= $errors['email'] ?? '' ?><br>
        Password: <input type="password" name="password" value="<?= $password?>" id="password" ><?= $errors['password'] ?? '' ?><br>
        Confirm Password: <input type="password" name="password_confirm" value="<?= $password_confirm ?>" id="password_confirm" > <?= $errors['password_confirm'] ?? '' ?><br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>