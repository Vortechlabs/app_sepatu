<?php
session_start();
include("../../koneksi.php");

if (!isset($_SESSION['id_users'])) {
    header("Location: ../../autentikasi/login.php");
    exit();
}

$id = $_SESSION['id_users'];
$username = $_POST['username'];
$email = $_POST['email'];
$no_telepon = $_POST['no_telepon'];

// Ambil data user lama
$query = $koneksi->prepare("SELECT image FROM users WHERE id_users = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$lama = $result->fetch_assoc();
$oldImage = $lama['image'];

$newImageName = $oldImage;

// Cek apakah ada file baru diupload
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $fileTmp = $_FILES['image']['tmp_name'];
    $fileName = time() . "_" . $_FILES['image']['name'];
    $targetDir = "../../assets/profil/";
    $targetFile = $targetDir . $fileName;

    // Hapus file lama
    if (file_exists($targetDir . $oldImage) && $oldImage != '') {
        unlink('../../' . $oldImage);
    }

    move_uploaded_file($fileTmp, $targetFile);
    $newImageName = "assets/profil/" . $fileName;

}

// Update data
$update = $koneksi->prepare("UPDATE users SET username=?, email=?, no_telepon=?, image=? WHERE id_users=?");
$update->bind_param("ssisi", $username, $email, $no_telepon, $newImageName, $id);

if ($update->execute()) {
    echo "<script>alert('Profil berhasil diperbarui');window.location='profil.php';</script>";
} else {
    echo "<script>alert('Gagal memperbarui profil');window.history.back();</script>";
}
?>
