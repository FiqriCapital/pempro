<?php
$host = 'localhost';
$user = 'root';
// u721127026_fiqri
$password = '';
// Fiqri123@
$dbname = 'blog_dashboard';
// u721127026_blog_dashboard

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
