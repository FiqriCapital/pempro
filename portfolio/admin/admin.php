<?php
include 'db.php';

// Menghitung statistik
$post_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM posts WHERE created_at >= NOW() - INTERVAL 30 DAY"))['count'];
$comment_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM comments WHERE created_at >= NOW() - INTERVAL 30 DAY"))['count'];
$visitor_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM visitors WHERE visit_date >= NOW() - INTERVAL 30 DAY"))['count'];

// Data untuk grafik (contoh data harian selama 30 hari terakhir)
$posts_per_day = [];
$comments_per_day = [];
$visitors_per_day = [];

for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    
    $post_count_day = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM posts WHERE DATE(created_at) = '$date'"))['count'];
    $comment_count_day = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM comments WHERE DATE(created_at) = '$date'"))['count'];
    $visitor_count_day = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM visitors WHERE DATE(visit_date) = '$date'"))['count'];
    
    $posts_per_day[] = $post_count_day;
    $comments_per_day[] = $comment_count_day;
    $visitors_per_day[] = $visitor_count_day;
}

// Simpan IP pengunjung
$visitor_ip = $_SERVER['REMOTE_ADDR'];
mysqli_query($conn, "INSERT INTO visitors (visitor_ip) VALUES ('$visitor_ip')");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Menambahkan Chart.js -->
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

        <!-- Grafik Statistik -->
        <h2>Grafik Aktivitas Bulan Ini</h2>
        <canvas id="activityChart" width="400" height="200"></canvas> <!-- Grafik ditampilkan di sini -->
        
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

    <script>
        // Data untuk grafik dari PHP
        const labels = [
            <?php
            for ($i = 29; $i >= 0; $i--) {
                echo '"' . date('Y-m-d', strtotime("-$i days")) . '",';
            }
            ?>
        ];

        const postData = [<?php echo implode(',', $posts_per_day); ?>];
        const commentData = [<?php echo implode(',', $comments_per_day); ?>];
        const visitorData = [<?php echo implode(',', $visitors_per_day); ?>];

        // Membuat grafik menggunakan Chart.js
        const ctx = document.getElementById('activityChart').getContext('2d');
        const activityChart = new Chart(ctx, {
            type: 'line', // Tipe grafik
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Postingan',
                        data: postData,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                    },
                    {
                        label: 'Komentar',
                        data: commentData,
                        borderColor: 'rgba(153, 102, 255, 1)',
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        fill: true,
                    },
                    {
                        label: 'Pengunjung',
                        data: visitorData,
                        borderColor: 'rgba(255, 159, 64, 1)',
                        backgroundColor: 'rgba(255, 159, 64, 0.2)',
                        fill: true,
                    },
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    },
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Tanggal'
                        }
                    },
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Jumlah'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
