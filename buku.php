<?php
include 'config.php';

// CREATE
if (isset($_POST['create'])) {
    $id = $_POST['id_buku'];
    $judul = $_POST['judul'];
    $pengarang = $_POST['pengarang'];
    try {
        $stmt = $pdo->prepare("INSERT INTO Buku (id_buku, judul, pengarang) VALUES (?, ?, ?)");
        $stmt->execute([$id, $judul, $pengarang]);
        $success = "✅ Data buku berhasil ditambahkan!";
    } catch (PDOException $e) {
        $error = "⚠️ Gagal tambah: " . $e->getMessage();
    }
}

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id_buku'];
    $judul = $_POST['judul'];
    $pengarang = $_POST['pengarang'];
    try {
        $stmt = $pdo->prepare("UPDATE Buku SET judul=?, pengarang=? WHERE id_buku=?");
        $stmt->execute([$judul, $pengarang, $id]);
        $success = "✅ Data buku berhasil diperbarui!";
    } catch (PDOException $e) {
        $error = "⚠️ Gagal update: " . $e->getMessage();
    }
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM Buku WHERE id_buku=?");
        $stmt->execute([$id]);
        $success = "✅ Data buku berhasil dihapus!";
    } catch (PDOException $e) {
        $error = "⚠️ Gagal hapus: " . $e->getMessage();
    }
}

// READ
$stmt = $pdo->query("SELECT * FROM Buku ORDER BY id_buku");
$buku = $stmt->fetchAll(PDO::FETCH_ASSOC);

// BARU: Generate ID buku terbaru (misal B001, B002, dst)
$nextId = 'B001'; // Default
try {
    $stmt = $pdo->query("SELECT id_buku FROM Buku ORDER BY id_buku DESC LIMIT 1");
    $lastId = $stmt->fetchColumn();
    if ($lastId && preg_match('/^B(\d+)$/', $lastId, $matches)) {
        $num = (int) $matches[1];
        $nextId = 'B' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
    }
} catch (PDOException $e) {
    // Fallback ke B001 jika error
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>CRUD Buku - Perpustakaan</title>
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
        <a class="nav-link active" href="buku.php">Buku</a>
        <a class="nav-link" href="peminjaman.php">Peminjaman</a>
      </div>
    </div>
  </nav>

  <div class="container mt-4">
    <h2 class="mb-4 fw-bold">Kelola Data Buku</h2>

    <!-- BARU: Debug sementara - Hapus setelah test -->
    <?php if (isset($success)): ?>
      <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Tombol Tambah -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalBuku" onclick="resetForm()">➕ Tambah Buku</button>

    <!-- Tabel Buku -->
    <div class="card">
      <div class="card-body">
        <table class="table table-hover align-middle">
          <thead class="table-primary">
            <tr>
              <th>ID Buku</th>
              <th>Judul</th>
              <th>Pengarang</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($buku as $row): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['id_buku']); ?></td>
                <td><?php echo htmlspecialchars($row['judul']); ?></td>
                <td><?php echo htmlspecialchars($row['pengarang']); ?></td>
                <td class="text-center">
                  <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalBuku"
                    onclick="editBuku('<?php echo $row['id_buku']; ?>', '<?php echo htmlspecialchars($row['judul']); ?>', '<?php echo htmlspecialchars($row['pengarang']); ?>')">✏️ Edit</button>
                  <a href="buku.php?delete=<?php echo $row['id_buku']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">🗑️ Hapus</a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($buku)): ?>
              <tr><td colspan="4" class="text-center">Belum ada data buku.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Tambah/Edit Buku -->
  <div class="modal fade" id="modalBuku" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST">
          <div class="modal-header">
            <h5 class="modal-title">Tambah Buku</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">ID Buku</label>
              <input type="text" class="form-control" name="id_buku" id="id_input" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Judul</label>
              <input type="text" class="form-control" name="judul" id="judul_input" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Pengarang</label>
              <input type="text" class="form-control" name="pengarang" id="pengarang_input" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" name="create" id="btnSubmit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // BARU: Reset form tambah - dengan auto ID buku
  function resetForm() {
    document.querySelector('.modal-title').textContent = 'Tambah Buku';
    document.getElementById('id_input').value = '<?php echo $nextId; ?>';
    document.getElementById('id_input').readOnly = false;
    document.getElementById('judul_input').value = '';
    document.getElementById('pengarang_input').value = '';
    document.getElementById('btnSubmit').name = 'create';
  }

  // Edit data buku
  function editBuku(id, judul, pengarang) {
    document.querySelector('.modal-title').textContent = 'Edit Buku';
    document.getElementById('id_input').value = id;
    document.getElementById('id_input').readOnly = true;
    document.getElementById('judul_input').value = judul;
    document.getElementById('pengarang_input').value = pengarang;
    document.getElementById('btnSubmit').name = 'update';
  }
</script>
</body>
</html>
