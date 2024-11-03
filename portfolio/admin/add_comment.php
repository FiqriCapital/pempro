<?php
include 'db.php';

if (isset($_POST['submit'])) {
    $post_id = $_POST['post_id'];
    $comment_text = mysqli_real_escape_string($conn, $_POST['comment_text']);
    
    $sql = "INSERT INTO comments (post_id, comment_text) VALUES ('$post_id', '$comment_text')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
}
?>
