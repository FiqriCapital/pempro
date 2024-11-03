<?php
include 'db.php';

if (isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    
    $sql = "INSERT INTO posts (title, content) VALUES ('$title', '$content')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>
