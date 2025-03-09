<?php
// index.php

// Konfigurasi koneksi database
$host     = "localhost";
$dbname   = "nama_database";       // Ganti dengan nama database kamu
$username = "nama_pengguna";       // Ganti dengan username database kamu
$password = "kata_sandi";          // Ganti dengan password database kamu

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Operasi CREATE
if (isset($_POST['create'])) {
    $name        = $_POST['name'];
    $description = $_POST['description'];
    $stmt = $pdo->prepare("INSERT INTO items (name, description) VALUES (?, ?)");
    $stmt->execute([$name, $description]);
    header("Location: index.php");
    exit;
}

// Operasi UPDATE
if (isset($_POST['update'])) {
    $id          = $_POST['id'];
    $name        = $_POST['name'];
    $description = $_POST['description'];
    $stmt = $pdo->prepare("UPDATE items SET name = ?, description = ? WHERE id = ?");
    $stmt->execute([$name, $description, $id]);
    header("Location: index.php");
    exit;
}

// Operasi DELETE
if (isset($_GET['action']) && $_GET['action'] == 'delete') {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>CRUD PHP Example</title>
</head>
<body>
    <h1>Contoh CRUD dengan PHP dan MySQL</h1>

    <?php
    // Jika aksi edit diakses, tampilkan form update
    if (isset($_GET['action']) && $_GET['action'] == 'edit') {
        $id = $_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($item) {
    ?>
        <h2>Edit Data</h2>
        <form method="post" action="index.php">
            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
            <p>
                <label>Nama:</label>
                <input type="text" name="name" value="<?php echo $item['name']; ?>" required>
            </p>
            <p>
                <label>Deskripsi:</label>
                <textarea name="description" required><?php echo $item['description']; ?></textarea>
            </p>
            <p>
                <input type="submit" name="update" value="Update">
                <a href="index.php">Batal</a>
            </p>
        </form>
    <?php
        } else {
            echo "Data tidak ditemukan";
        }
    } else {
    ?>
        <!-- Form Create -->
        <h2>Tambah Data Baru</h2>
        <form method="post" action="index.php">
            <p>
                <label>Nama:</label>
                <input type="text" name="name" placeholder="Masukkan nama" required>
            </p>
            <p>
                <label>Deskripsi:</label>
                <textarea name="description" placeholder="Masukkan deskripsi" required></textarea>
            </p>
            <p>
                <input type="submit" name="create" value="Create">
            </p>
        </form>
    <?php
    }
    ?>

    <!-- Tampilan Data (Read) -->
    <h2>Daftar Data</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
        </tr>
        <?php
        $stmt = $pdo->query("SELECT * FROM items ORDER BY id DESC");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['description'] . "</td>";
            echo "<td>
                    <a href='index.php?action=edit&id=" . $row['id'] . "'>Edit</a> |
                    <a href='index.php?action=delete&id=" . $row['id'] . "' onclick='return confirm(\"Yakin ingin menghapus?\");'>Delete</a>
                  </td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>
