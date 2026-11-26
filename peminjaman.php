<?php
include 'config.php';

// Inisialisasi variabel
$success = "";
$error = "";
$peminjaman = [];
$anggotaList = [];
$bukuList = [];

// CREATE
if (isset($_POST['create'])) {
    $id_peminjaman = trim($_POST['id_peminjaman']);
    $id_anggota = $_POST['id_anggota'];
    $id_buku = $_POST['id_buku'];
    $tgl_pinjam = $_POST['tgl_pinjam'];
    $tgl_kembali = $_POST['tgl_kembali'];

    if (empty($id_peminjaman)) {
        $error = "⚠️ ID Peminjaman harus diisi.";
    } else if (strtotime($tgl_kembali) < strtotime($tgl_pinjam)) {
        $error = "⚠️ Tanggal kembali harus setelah tanggal pinjam.";
    } else {
        try {
            // Cek apakah ID peminjaman sudah ada
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM peminjaman WHERE id_peminjaman = ?");
            $checkStmt->execute([$id_peminjaman]);
            if ($checkStmt->fetchColumn() > 0) {
                $error = "⚠️ ID Peminjaman sudah digunakan, gunakan ID lain.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO peminjaman (id_peminjaman, id_anggota, id_buku, tgl_pinjam, tgl_kembali) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$id_peminjaman, $id_anggota, $id_buku, $tgl_pinjam, $tgl_kembali]);
                $success = "✅ Data peminjaman berhasil ditambahkan!";
            }
        } catch (PDOException $e) {
            $error = "⚠️ Gagal tambah: " . $e->getMessage();
        }
    }
}

// UPDATE
if (isset($_POST['update'])) {
    $id_peminjaman = $_POST['id_peminjaman'];
    $id_anggota = $_POST['id_anggota'];
    $id_buku = $_POST['id_buku'];
    $tgl_pinjam = $_POST['tgl_pinjam'];
    $tgl_kembali = $_POST['tgl_kembali'];

    if (strtotime($tgl_kembali) < strtotime($tgl_pinjam)) {
        $error = "⚠️ Tanggal kembali harus setelah tanggal pinjam.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE peminjaman SET id_anggota=?, id_buku=?, tgl_pinjam=?, tgl_kembali=? WHERE id_peminjaman=?");
            $stmt->execute([$id_anggota, $id_buku, $tgl_pinjam, $tgl_kembali, $id_peminjaman]);
            $success = "✅ Data peminjaman berhasil diperbarui!";
        } catch (PDOException $e) {
            $error = "⚠️ Gagal update: " . $e->getMessage();
        }
    }
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM peminjaman WHERE id_peminjaman=?");
        $stmt->execute([$id]);
        $success = "✅ Data peminjaman berhasil dihapus!";
    } catch (PDOException $e) {
        $error = "⚠️ Gagal hapus: " . $e->getMessage();
    }
}

// READ - Ambil Data Anggota untuk dropdown
try {
    $stmt = $pdo->query("SELECT * FROM Anggota ORDER BY id_anggota");
    $anggotaList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "⚠️ Gagal mengambil data anggota: " . $e->getMessage();
}

// READ - Ambil Data Buku untuk dropdown
try {
    $stmt = $pdo->query("SELECT * FROM Buku ORDER BY id_buku");
    $bukuList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "⚠️ Gagal mengambil data buku: " . $e->getMessage();
}

// READ - Ambil Data Peminjaman dengan JOIN untuk tabel
try {
    $stmt = $pdo->query("SELECT p.id_peminjaman, p.id_anggota, p.id_buku, p.tgl_pinjam, p.tgl_kembali, a.nama_anggota, b.judul 
                         FROM peminjaman p 
                         LEFT JOIN Anggota a ON p.id_anggota = a.id_anggota 
                         LEFT JOIN Buku b ON p.id_buku = b.id_buku 
                         ORDER BY p.id_peminjaman DESC");
    $peminjaman = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "⚠️ Gagal mengambil data peminjaman: " . $e->getMessage();
    // Jika tabel peminjaman belum ada, buat tabel (asumsi kolom sesuai)
    if (strpos($e->getMessage(), 'peminjaman') !== false || strpos($e->getMessage(), 'table') !== false) {
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS peminjaman (
                id_peminjaman INT PRIMARY KEY,
                id_anggota INT NOT NULL,
                id_buku INT NOT NULL,
                tgl_pinjam DATE NOT NULL,
                tgl_kembali DATE NOT NULL,
                FOREIGN KEY (id_anggota) REFERENCES Anggota(id_anggota) ON DELETE CASCADE,
                FOREIGN KEY (id_buku) REFERENCES Buku(id_buku) ON DELETE CASCADE
            )");
            // Refresh query setelah buat tabel
            $stmt = $pdo->query("SELECT p.id_peminjaman, p.id_anggota, p.id_buku, p.tgl_pinjam, p.tgl_kembali, a.nama_anggota, b.judul 
                                 FROM peminjaman p 
                                 LEFT JOIN Anggota a ON p.id_anggota = a.id_anggota 
                                 LEFT JOIN Buku b ON p.id_buku = b.id_buku 
                                 ORDER BY p.id_peminjaman DESC");
            $peminjaman = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $create_e) {
            $error .= " Tidak bisa membuat tabel peminjaman: " . $create_e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>CRUD Peminjaman - Perpustakaan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; }
    .navbar { background: linear-gradient(90deg, #4e73df, #224abe); }
    .card { border-radius: 16px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .btn-primary { background: #4e73df; border: none; }
    .btn-primary:hover { background: #2e59d9; }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <a class="navbar-brand fw-bold" href="index.php">📚 Perpustakaan</a>
      <div class="navbar-nav ms-auto">
        <a class="nav-link" href="anggota.php">Anggota</a>
        <a class="nav-link" href="buku.php">Buku</a>
        <a class="nav-link active" href="peminjaman.php">Peminjaman</a>
      </div>
    </div>
  </nav>

  <div class="container mt-4">
    <h2 class="mb-4 fw-bold">Kelola Data Peminjaman</h2>

    <!-- Alert -->
    <?php if ($success): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Tombol Tambah -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalPeminjaman" onclick="resetForm()">➕ Tambah Peminjaman</button>

    <!-- Tabel Peminjaman -->
    <div class="card">
      <div class="card-body">
        <table class="table table-hover align-middle">
          <thead class="table-primary">
            <tr>
              <th>ID Peminjaman</th>
              <th>Nama Anggota</th>
              <th>Judul Buku</th>
              <th>Tanggal Pinjam</th>
              <th>Tanggal Kembali</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($peminjaman)): ?>
              <?php foreach ($peminjaman as $row): ?>
                <tr>
                  <td><?php echo htmlspecialchars($row['id_peminjaman']); ?></td>
                  <td><?php echo htmlspecialchars($row['nama_anggota'] ?? 'N/A'); ?></td>
                  <td><?php echo htmlspecialchars($row['judul'] ?? 'N/A'); ?></td>
                  <td><?php echo htmlspecialchars($row['tgl_pinjam']); ?></td>
                  <td><?php echo htmlspecialchars($row['tgl_kembali']); ?></td>
                  <td class="text-center">
                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalPeminjaman"
                      onclick="editPeminjaman('<?php echo $row['id_peminjaman']; ?>','<?php echo $row['id_anggota']; ?>','<?php echo $row['id_buku']; ?>','<?php echo $row['tgl_pinjam']; ?>','<?php echo $row['tgl_kembali']; ?>')">✏️ Edit</button>
                    <a href="peminjaman.php?delete=<?php echo $row['id_peminjaman']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">🗑️ Hapus</a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-center">Belum ada data peminjaman.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Tambah/Edit -->
  <div class="modal fade" id="modalPeminjaman" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" id="formPeminjaman">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Tambah Peminjaman</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Input ID Peminjaman Manual -->
            <div class="mb-3">
              <label for="id_peminjaman" class="form-label">ID Peminjaman</label>
              <input type="number" class="form-control" name="id_peminjaman" id="id_peminjaman" required>
            </div>

            <div class="mb-3">
              <label for="id_anggota" class="form-label">Anggota</label>
              <select class="form-select" name="id_anggota" id="id_anggota" required>
                <option value="">Pilih Anggota</option>
                <?php foreach ($anggotaList as $a): ?>
                  <option value="<?php echo htmlspecialchars($a['id_anggota']); ?>"><?php echo htmlspecialchars($a['id_anggota']." - ".$a['nama_anggota']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="mb-3">
              <label for="id_buku" class="form-label">Buku</label>
              <select class="form-select" name="id_buku" id="id_buku" required>
                <option value="">Pilih Buku</option>
                <?php foreach ($bukuList as $b): ?>
                  <option value="<?php echo htmlspecialchars($b['id_buku']); ?>"><?php echo htmlspecialchars($b['id_buku']." - ".$b['judul']); ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="mb-3">
              <label for="tgl_pinjam" class="form-label">Tanggal Pinjam</label>
              <input type="date" class="form-control" name="tgl_pinjam" id="tgl_pinjam" required>
            </div>

            <div class="mb-3">
              <label for="tgl_kembali" class="form-label">Tanggal Kembali</label>
              <input type="date" class="form-control" name="tgl_kembali" id="tgl_kembali" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" id="submitBtn" name="create" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Script -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function resetForm() {
      document.getElementById('id_peminjaman').value = '';
      document.getElementById('id_peminjaman').readOnly = false;
      document.getElementById('id_anggota').value = '';
      document.getElementById('id_buku').value = '';
      document.getElementById('tgl_pinjam').value = '';
      document.getElementById('tgl_kembali').value = '';
      document.getElementById('modalTitle').textContent = 'Tambah Peminjaman';
      document.getElementById('submitBtn').name = 'create';
    }

    function editPeminjaman(id, anggota, buku, tgl_pinjam, tgl_kembali) {
      document.getElementById('id_peminjaman').value = id;
      document.getElementById('id_peminjaman').readOnly = true; // ID tidak bisa diubah saat edit
      document.getElementById('id_anggota').value = anggota;
      document.getElementById('id_buku').value = buku;
      document.getElementById('tgl_pinjam').value = tgl_pinjam;
      document.getElementById('tgl_kembali').value = tgl_kembali;
      document.getElementById('modalTitle').textContent = 'Edit Peminjaman';
      document.getElementById('submitBtn').name = 'update';
    }
  </script>
</body>
</html>
