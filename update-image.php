<?php
require_once "inc/dbConnection.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uri = $_SERVER["REQUEST_URI"];
    $uriArray = explode("/", $uri);
    $id = end($uriArray);
    $image = $_FILES['image']; //arr
    // print_r($image);
    $imageName = $image['name'];
    $imageType = $image['type'];
    $imageTmpName = $image['tmp_name'];
    $imageError = $image['error'];
    $imageSize = $image['size']; //byte
    $imageSizeMb = $imageSize / (1024 ** 2); //mb
    $ext = pathinfo($imageName, PATHINFO_EXTENSION);
    $errors = [];

    if ($imageError > 0) {
        $errors[] = "Error while uploading or empty";
    } else if (!in_array(strtolower($ext), ['jpg', 'png', 'jpeg', 'gif'])) {
        $errors[] = "Must be image";
    } else if ($imageSizeMb > 1) {
        $errors[] = "Image max size 1mb";
    }

    if (empty($errors)) {
        $randstr = uniqid();
        $imageNewName = "$randstr.$ext";
        move_uploaded_file($imageTmpName, "upload/$imageNewName");

        $query = "UPDATE `users` SET `image`='$imageNewName' Where id=$id ";
        $runquery = mysqli_query($conn, $query);

        if ($runquery) { // Check if the query succeeded
            if (mysqli_affected_rows($conn) > 0) { // Check if any rows were affected
                echo json_encode(["msg" => "Added successfully"]);
            } else {
                echo json_encode(["msg" => "No rows were affected"]);
            }
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
