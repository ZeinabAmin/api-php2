<?php
require_once "inc/dbConnection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $errors = [];
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

    if (empty($errors)) {
        $query = "SELECT * FROM `users` WHERE email='$email' ";
        $runquery = mysqli_query($conn, $query);
        if (mysqli_num_rows($runquery) > 0) // or  == 1
        {

            $user = mysqli_fetch_assoc($runquery);
            $userHashPassword = $user['password'];
            $iscorrect = password_verify($password, $userHashPassword); //true or false

            if ($iscorrect) {
                // print_r($user);
                echo json_encode(["msg" => "login success", 'id' => $user['id'], 'token' => uniqid()]);
            } else {
                echo json_encode(["msg" => "password is not correct"]);
            }
        } else {
            echo json_encode(["msg" => "Email not match"]);
        }
    } else {

        foreach ($errors as $value) {
            echo json_encode(["msg" => "$value"]);
        }
        // $errorsJson = json_encode($errors);
        // echo $errorsJson;
    }
} else {

    echo json_encode(["msg" => "method not allowed"]);
    http_response_code(405);
}
