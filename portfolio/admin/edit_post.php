<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $post = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM posts WHERE id = $id"));

    if (isset($_POST['update'])) {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $content = mysqli_real_escape_string($conn, $_POST['content']);
        
        $sql = "UPDATE posts SET title = '$title', content = '$content' WHERE id = $id";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: index.php");
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
} else {
    echo "Post not found!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Postingan</title>
</head>
<body>
    <h2>Edit Postingan</h2>
    <form action="" method="POST">
        <input type="text" name="title" value="<?php echo $post['title']; ?>" required><br>
        <textarea name="content" required><?php echo $post['content']; ?></textarea><br>
        <button type="submit" name="update">Update Postingan</button>
    </form>
</body>
</html>
