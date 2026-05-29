<?php
session_start();
require_once '../backend/config.php';
if(!isset($_SESSION['user_id']))
{
    $msg = "Je moet eerst inloggen!";
    header("Location: $base_url/admin/login.php?msg=$msg");
    exit;
}

$action = $_POST['action'];
if($action == 'create')
{
    //Validatie
    $title = $_POST['title'];
    if(empty($title))
    {
        $errors[] = "Vul een titel in!";
    }

    $themeland = $_POST['themeland'];
    if(empty($themeland))
    {
        $errors[] = "Vul een themagebied in!";
    }

    if(isset($_POST['fast_pass']))
    {
        $fast_pass = 1;
    }
    else
    {
        $fast_pass = 0;
    }

    $description = $_POST['description'];
    if(empty($description))
    {
        $errors[] = "Vul een description in!";
    }

    $min_length = $_POST['min_length'];
    if(empty($min_length))
    {
        $errors[] = "Vul een minimale lengte in!";
    }
    elseif(!is_numeric($min_length))
    {
        $errors[] = "Minimale lengte moet een getal zijn!";
    }

    $target_dir = "../../img/attracties/";
    $target_file = $_FILES['img_file']['name'];
    if(file_exists($target_dir . $target_file))
    {
        $errors[] = "Bestand bestaat al!";
    }

    //Evt. errors dumpen
    if(isset($errors))
    {
        var_dump($errors);
        die();
    }

    //Plaats geuploade bestand in map
    move_uploaded_file($_FILES['img_file']['tmp_name'], $target_dir . $target_file);

    //Query
    require_once 'conn.php';
    $query = "INSERT INTO rides (title, themeland, fast_pass, img_file, description, min_length) VALUES(:title, :themeland, :fast_pass, :img_file, :description, :min_length)";
    $statement = $conn->prepare($query);
    $statement->execute([
        ":title" => $title,
        ":themeland" => $themeland,
        ":fast_pass" => $fast_pass,
        ":img_file" => $target_file,
        ":description" => $description,
        "min_length" => $min_length,
    ]);

    header("Location: ../attracties/index.php");
    exit;
}

if($action == "update")
{
    $id = $_POST['id'];
    $description = $_POST['description'];
    $title = $_POST['title'];
    if(empty($title))
    {
        $errors[] = "Vul een titel in!";
    }
    $themeland = $_POST['themeland'];
    if(empty($themeland))
    {
        $errors[] = "Vul een thema in!";
    }
    $min_length = $_POST['min_length'];
    if(empty($min_length))
    {
        $errors[] = "Vul een minimale lengte in!";
    }
    elseif(!is_numeric($min_length))
    {
        $errors[] = "Minimale lengte moet een getal zijn!";
    }

    if(isset($_POST['fast_pass']))
    {
        $fast_pass = 1;
    }
    else
    {
        $fast_pass = 0;
    }

    if(empty($_FILES['img_file']['name']))
    {
        $target_file = $_POST['old_img'];
    }
    else
    {
        $target_dir = "../../img/attracties/";
        $target_file = $_FILES['img_file']['name'];
        if(file_exists($target_dir . $target_file))
        {
            $errors[] = "Bestand bestaat al!";
        }

        //Plaats geuploade bestand in map
        move_uploaded_file($_FILES['img_file']['tmp_name'], $target_dir . $target_file);
    }

    //Evt. errors dumpen
    if(isset($errors))
    {
        var_dump($errors);
        die();
    }

    //Query
    require_once 'conn.php';
    $query = "UPDATE rides SET title = :title, themeland = :themeland, fast_pass = :fast_pass, description = :description, min_length = :min_length, img_file = :img_file WHERE id = :id";
    $statement = $conn->prepare($query);
    $statement->execute([
        ":title" => $title,
        ":themeland" => $themeland,
        ":fast_pass" => $fast_pass,
        ":img_file" => $target_file,
        ":id" => $id,
        ":description" => $description,
        ":min_length" => $min_length,
    ]);

    header("Location: ../attracties/index.php");
    exit;
}

if($action == "delete")
{
    $id = $_POST['id'];
    require_once 'conn.php';
    $query = "DELETE FROM rides WHERE id = :id";
    $statement = $conn->prepare($query);
    $statement->execute([
        ":id" => $id
    ]);
    header("Location: ../attracties/index.php");
    exit;
}
