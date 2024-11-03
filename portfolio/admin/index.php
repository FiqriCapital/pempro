<?php
include 'db.php';

// Menghitung statistik
$post_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM posts WHERE created_at >= NOW() - INTERVAL 30 DAY"))['count'];
$comment_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM comments WHERE created_at >= NOW() - INTERVAL 30 DAY"))['count'];
$visitor_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM visitors WHERE visit_date >= NOW() - INTERVAL 30 DAY"))['count'];

// Simpan IP pengunjung
$visitor_ip = $_SERVER['REMOTE_ADDR'];
mysqli_query($conn, "INSERT INTO visitors (visitor_ip) VALUES ('$visitor_ip')");

// Fetch daily data for the last 30 days
$dates = [];
$post_counts = [];
$comment_counts = [];
$visitor_counts = [];

for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dates[] = $date;

    // Get post count for the date
    $post_count_daily = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM posts WHERE DATE(created_at) = '$date'"))['count'];
    $post_counts[] = $post_count_daily;

    // Get comment count for the date
    $comment_count_daily = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM comments WHERE DATE(created_at) = '$date'"))['count'];
    $comment_counts[] = $comment_count_daily;

    // Get visitor count for the date
    $visitor_count_daily = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM visitors WHERE DATE(visit_date) = '$date'"))['count'];
    $visitor_counts[] = $visitor_count_daily;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <div class="container">
        <h1>Admin Dashboard</h1>
        
        <div class="stats">
            <div class="stat-box">
                <p><?php echo $post_count; ?></p>
                <small>Total Postingan Bulan Ini</small>
            </div>
            <div class="stat-box">
                <p><?php echo $comment_count; ?></p>
                <small>Total Komentar Bulan Ini</small>
            </div>
            <div class="stat-box">
                <p><?php echo $visitor_count; ?></p>
                <small>Total Pengunjung Bulan Ini</small>
            </div>
        </div>

        <h2>Grafik Statistik</h2>
        <canvas id="statsChart" width="400" height="200"></canvas>
        <script>
            const ctx = document.getElementById('statsChart').getContext('2d');
            const statsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($dates); ?>,
                    datasets: [
                        {
                            label: 'Jumlah Postingan',
                            data: <?php echo json_encode($post_counts); ?>,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Jumlah Komentar',
                            data: <?php echo json_encode($comment_counts); ?>,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 1
                        },
                        {
                            label: 'Jumlah Pengunjung',
                            data: <?php echo json_encode($visitor_counts); ?>,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>

        <h2>Tambah Postingan Baru</h2>
        <form action="add_post.php" method="POST">
            <input type="text" name="title" placeholder="Judul Postingan" required><br>
            <textarea name="content" placeholder="Konten Postingan" required></textarea><br>
            <button type="submit" name="submit">Tambah Postingan</button>
        </form>

        <h2>Edit Postingan</h2>
        <div class="edit-post-list">
            <?php
            $result = mysqli_query($conn, "SELECT * FROM posts");
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<p>" . $row['title'] . " <a href='edit_post.php?id=" . $row['id'] . "'>Edit</a></p>";

                // Menampilkan komentar untuk setiap postingan
                $post_id = $row['id'];
                $comments = mysqli_query($conn, "SELECT * FROM comments WHERE post_id = $post_id");
                while ($comment = mysqli_fetch_assoc($comments)) {
                    echo "<div class='comment'>";
                    echo "<p>" . htmlspecialchars($comment['comment_text']) . " <small>(" . $comment['created_at'] . ")</small></p>";
                    echo "</div>";
                }

                // Form untuk menambahkan komentar
                echo "<form action='add_comment.php' method='POST'>";
                echo "<input type='hidden' name='post_id' value='$post_id'>";
                echo "<textarea name='comment_text' placeholder='Tulis komentar...' required></textarea><br>";
                echo "<button type='submit' name='submit'>Kirim Komentar</button>";
                echo "</form>";
            }
            ?>
        </div>
        
        <footer>
            <p>&copy; 2024 Dashboard Admin Blog</p>
        </footer>
    </div>

</body>
</html>
