<?php
require_once "inc/dbConnection.php";
if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    $uri = $_SERVER["REQUEST_URI"];
    $uriArray = explode("/", $uri);
    $id = end($uriArray);

    //print_r($_POST); // read data from body form-data
    $data = json_decode(file_get_contents("php://input")); //ass arr //to read data from body raw in postman or html file
    // (php://input) file in php //json
    //(php://input) is a PHP stream that allows you to read raw data from the request body of an HTTP request, and can be accessed using the file_get_contents() function.

    // print_r($data);
    $newPassword = $data->newPassword;
    $confirmNewPassword = $data->confirmNewPassword;

    $errors = [];

    if (empty($newPassword)) {
        $errors[] = "new Password is required";
    } elseif (!is_string($newPassword)) {
        $errors[] = "new Password must be string";
    }

    if (empty($confirmNewPassword)) {

        $errors[] = "confirm new Password is required";
    } elseif (!is_string($newPassword)) {
        $errors[] = "new Password must be string";
    } elseif ($newPassword != $confirmNewPassword) {
        $errors[] = "check your new Password and confirm new Password";
    }

    if (empty($errors)) {
        $newPasswordHash = password_hash($confirmNewPassword, PASSWORD_BCRYPT);
        $query = "UPDATE `users` set `password`='$newPasswordHash' where id=$id";
        $runquery = mysqli_query($conn, $query);
        echo json_encode(["msg" => "password updated successfully"]);
    } else {
        //     $errorsJson = json_encode($errors);
        //     echo $errorsJson;
        foreach ($errors as $value) {
            echo json_encode(["msg" => "$value"]);
        }
    }
} else {
    // http_response_code(404);
    echo json_encode(["msg" => "method not allowed"]);
    http_response_code(405);
}
