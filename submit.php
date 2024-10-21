<?php
// Include koneksi database
include 'config.php';

$response = ['status' => '', 'message' => ''];

// Validasi request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $nama = $_POST['nama'];
    $nik = $_POST['nik'];
    $noKK = $_POST['noKK'];
    $umur = $_POST['umur'];
    $jenisKelamin = $_POST['jenisKelamin'];
    $provinsi = $_POST['provinsi'];
    $kota = $_POST['kota'];
    $kecamatan = $_POST['kecamatan'];
    $kelurahan = $_POST['kelurahan'];
    $alamat = $_POST['alamat'];
    $rt = $_POST['rt'];
    $rw = $_POST['rw'];
    $penghasilanSebelum = $_POST['penghasilanSebelum'];
    $penghasilanSetelah = $_POST['penghasilanSetelah'];
    $alasan = $_POST['alasan'];
    $deklarasi = $_POST['deklarasi'] ? 1 : 0;

    // Handle file upload
    $fotoKTP = $_FILES['fotoKTP'];
    $fotoKK = $_FILES['fotoKK'];
    
    // Validasi ukuran file (max 2MB)
    if ($fotoKTP['size'] > 2097152 || $fotoKK['size'] > 2097152) {
        $response['status'] = 'error';
        $response['message'] = 'Ukuran file tidak boleh lebih dari 2MB';
        echo json_encode($response);
        exit;
    }

    // Tentukan direktori upload
    $uploadDir = 'uploads/';
    $fotoKTPPath = $uploadDir . basename($fotoKTP['name']);
    $fotoKKPath = $uploadDir . basename($fotoKK['name']);

    // Pindahkan file ke direktori uploads
    if (move_uploaded_file($fotoKTP['tmp_name'], $fotoKTPPath) && move_uploaded_file($fotoKK['tmp_name'], $fotoKKPath)) {
        // Simpan data ke database
        $stmt = $conn->prepare("INSERT INTO penerima_bansos 
            (nama, nik, no_kk, foto_ktp, foto_kk, umur, jenis_kelamin, provinsi, kota, kecamatan, kelurahan, alamat, rt, rw, penghasilan_sebelum, penghasilan_setelah, alasan, deklarasi)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("sssssiissssssssdds", $nama, $nik, $noKK, $fotoKTPPath, $fotoKKPath, $umur, $jenisKelamin, $provinsi, $kota, $kecamatan, $kelurahan, $alamat, $rt, $rw, $penghasilanSebelum, $penghasilanSetelah, $alasan, $deklarasi);

        if ($stmt->execute()) {
            $response['status'] = 'success';
            $response['message'] = 'Data berhasil disimpan';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Gagal menyimpan data: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Gagal mengupload file';
    }
    
    echo json_encode($response);
}

$conn->close();
?>
