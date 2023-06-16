<?php
require_once "inc/dbConnection.php";

if ($_SERVER["REQUEST_METHOD"] == "PUT") {
    $uri = $_SERVER["REQUEST_URI"];
    $uriArray = explode("/", $uri);
    $id = end($uriArray);

    // $newUserName = $_POST["newUserName"];
    // $newEmail = $_POST["newEmail"];
    //print_r($_POST); // read data from body form-data

    $data = json_decode(file_get_contents("php://input")); //ass arr //to read data from body raw in postman or html file
    // (php://input) file in php //json
    //(php://input) is a PHP stream that allows you to read raw data from the request body of an HTTP request, and can be accessed using the file_get_contents() function.

    // print_r($data);

    $newUserName = $data->userName;
    $newEmail = $data->email;

    $errors = [];

    if (empty($newUserName)) {
        $errors[] = "new user name  is required";
    } elseif (!is_string($newUserName)) {
        $errors[] = "new user name must be string";
    } elseif (strlen($newUserName) >= 50) {
        $errors[] = "max length 50 ";
    }

    if (empty($newEmail)) {
        $errors[] = "email is required";
    } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "must be email";
    }

    if (empty($errors)) {

        $query = "SELECT * FROM `users` WHERE id=$id";
        $runquery = mysqli_query($conn, $query);
       if (mysqli_num_rows($runquery) > 0) {
            $query_email = "SELECT * FROM `users` where email='$newEmail' ";
            $runquery_email = mysqli_query($conn, $query_email);
            if (!mysqli_num_rows($runquery_email) > 0) { // if email does not already exist
                $query_update = "UPDATE `users` SET `userName`='$newUserName', `email`='$newEmail' Where id=$id ";
                $runquery_update = mysqli_query($conn, $query_update);
                echo json_encode(["msg" => "updated successfully"]);
            } else {
                $query_update = "UPDATE `users` SET `userName`='$newUserName' Where id=$id ";
                $runquery_update = mysqli_query($conn, $query_update);
                echo json_encode(["msg" => "updated successfully"]);
            }
        } else {
            echo json_encode(["msg" => "not found"]); //id
        }
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
