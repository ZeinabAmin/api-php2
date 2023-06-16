<?php
require_once "inc/dbConnection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userName = trim(htmlspecialchars($_POST['userName']));
    $email = trim(htmlspecialchars($_POST['email']));
    $password = $_POST['password'];
    $confirmPassword = $_POST["confirmPassword"];
    $image = $_FILES['image']; //arr
    // print_r($image);

    $imageName = $image['name'];
    $imgType = $image['type'];
    $imageTmpName = $image['tmp_name'];
    $imageError = $image['error'];
    $imageSize = $image['size']; //byte
    $imageSizeMb = $imageSize / (1024 ** 2); //mb
    $ext = pathinfo($imageName, PATHINFO_EXTENSION);
    $errors = [];

    if (empty($userName)) {
        $errors[] = "userName is required";
    } elseif (!is_string($userName)) {
        $errors[] = "userName must be string";
    } elseif (strlen($userName) >= 50) {
        $errors[] = "max length 50 ";
    }

    if (empty($email)) {
        $errors[] = "email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "must be email";
    }

    if (empty($password)) {
        $errors[] = "password is required";
    } elseif (!is_string($password)) {
        $errors[] = "password must be string";
    }

    if (empty($confirmPassword)) {

        $errors[] = "confirm password is required";
    } elseif (!is_string($password)) {
        $errors[] = "password must be string";
    } elseif ($password != $confirmPassword) {
        $errors[] = "check your password and confirm password";
    }

    if ($imageError > 0) {
        $errors[] = "Error while uploading";
    } else if (!in_array(strtolower($ext), ['jpg', 'png', 'jpeg', 'gif'])) {
        $errors[] = "Must be image";
    } else if ($imageSizeMb > 1) {
        $errors[] = "Image max size 1mb";
    }

    if (empty($errors)) {

        //check email
        $queryCheck = "SELECT * FROM `users` where `email`='$email'";
        $runqueryCheck = mysqli_query($conn, $queryCheck);
        if (!mysqli_num_rows($runqueryCheck) > 0) {
            $passwordHash = password_hash($password, PASSWORD_BCRYPT); // or PASSWORD_DEFAULT
            $randstr = uniqid();
            $imageNewName = "$randstr.$ext";
            move_uploaded_file($imageTmpName, "upload/$imageNewName");
            $query = "INSERT INTO `users`(`userName`, `email`, `password`,`image`) VALUES ('$userName','$email',
            '$passwordHash','$imageNewName')";
            $runquery = mysqli_query($conn, $query);

            // if (mysqli_num_rows($runquery) > 0) { //false

            if ($runquery) { // Check if the query succeeded
                if (mysqli_affected_rows($conn) > 0) { // Check if any rows were affected
                    echo json_encode(["msg" => "Added successfully"]);
                } else {
                    echo json_encode(["msg" => "No rows were affected"]);
                }
            } else {
                // The query failed
                echo json_encode(["msg" => "Failed to add"]);
            }
        } else {
            echo json_encode(["msg" => "this email already exists"]);
        }
    } else {
        $errorsJson = json_encode($errors);
        echo $errorsJson;
    }
} else {

    echo json_encode(["msg" => "method not allowed"]);
    http_response_code(405);
}
