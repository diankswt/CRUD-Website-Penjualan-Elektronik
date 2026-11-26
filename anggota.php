<?php
include 'config.php';

// CREATE
if (isset($_POST['create'])) {
    $id = $_POST['id_anggota'];
    $nama = $_POST['nama_anggota'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];
    try {
        $stmt = $pdo->prepare("INSERT INTO Anggota (id_anggota, nama_anggota, no_hp, alamat) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id, $nama, $no_hp, $alamat]);
        $success = "✅ Data anggota berhasil ditambahkan!";
    } catch (PDOException $e) {
        $error = "⚠️ Gagal tambah: " . $e->getMessage();
    }
}

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id_anggota'];
    $nama = $_POST['nama_anggota'];
    $no_hp = $_POST['no_hp'];
    $alamat = $_POST['alamat'];
    try {
        $stmt = $pdo->prepare("UPDATE Anggota SET nama_anggota=?, no_hp=?, alamat=? WHERE id_anggota=?");
        $stmt->execute([$nama, $no_hp, $alamat, $id]);
        $success = "✅ Data anggota berhasil diperbarui!";
    } catch (PDOException $e) {
        $error = "⚠️ Gagal update: " . $e->getMessage();
    }
}

// DELETE
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM Anggota WHERE id_anggota=?");
        $stmt->execute([$id]);
        $success = "✅ Data anggota berhasil dihapus!";
    } catch (PDOException $e) {
        $error = "⚠️ Gagal hapus: " . $e->getMessage();
    }
}

// READ
$stmt = $pdo->query("SELECT * FROM Anggota ORDER BY id_anggota");
$anggota = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Generate ID anggota terbaru - DITEMPATKAN DI SINI
$nextId = 'A001'; // Default
try {
    $stmt = $pdo->query("SELECT id_anggota FROM Anggota ORDER BY id_anggota DESC LIMIT 1");
    $lastId = $stmt->fetchColumn();
    if ($lastId && preg_match('/^A(\d+)$/', $lastId, $matches)) {
        $num = (int) $matches[1];
        $nextId = 'A' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);
    }
} catch (PDOException $e) {
    // Fallback ke A001 jika error
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>CRUD Anggota - Perpustakaan</title>
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
        <a class="nav-link active" href="anggota.php">Anggota</a>
        <a class="nav-link" href="buku.php">Buku</a>
        <a class="nav-link" href="peminjaman.php">Peminjaman</a>
      </div>
    </div>
  </nav>

  <div class="container mt-4">
    <h2 class="mb-4 fw-bold">Kelola Data Anggota</h2>

    <!-- Debug: Hapus setelah test -->
    <?php if (isset($success)): ?>
      <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
      <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Tombol Tambah -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalAnggota" onclick="resetForm()">➕ Tambah Anggota</button>

    <!-- Tabel Anggota -->
    <div class="card">
      <div class="card-body">
        <table class="table table-hover align-middle">
          <thead class="table-primary">
            <tr>
              <th>ID Anggota</th>
              <th>Nama</th>
              <th>No HP</th>
              <th>Alamat</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($anggota as $row): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['id_anggota']); ?></td>
                <td><?php echo htmlspecialchars($row['nama_anggota']); ?></td>
                <td><?php echo htmlspecialchars($row['no_hp']); ?></td>
                <td><?php echo htmlspecialchars($row['alamat']); ?></td>
                <td class="text-center">
                  <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalAnggota"
                    onclick="editAnggota('<?php echo $row['id_anggota']; ?>', '<?php echo htmlspecialchars($row['nama_anggota']); ?>', '<?php echo $row['no_hp']; ?>', '<?php echo htmlspecialchars($row['alamat']); ?>')">✏️ Edit</button>
                  <a href="anggota.php?delete=<?php echo $row['id_anggota']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">🗑️ Hapus</a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($anggota)): ?>
              <tr><td colspan="5" class="text-center">Belum ada data anggota.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal Tambah/Edit -->
  <div class="modal fade" id="modalAnggota" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST">
          <div class="modal-header">
            <h5 class="modal-title">Tambah Anggota</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id_anggota" id="id_hidden">
            <div class="mb-3">
              <label class="form-label">ID Anggota</label>
              <input type="text" class="form-control" name="id_anggota" id="id_input" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Nama Anggota</label>
              <input type="text" class="form-control" name="nama_anggota" id="nama_input" required>
            </div>
            <div class="mb-3">
              <label class="form-label">No HP</label>
              <input type="text" class="form-control" name="no_hp" id="hp_input" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Alamat</label>
              <input type="text" class="form-control" name="alamat" id="alamat_input" required>
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
  // Reset form saat tambah - dengan auto ID
  function resetForm() {
    document.querySelector('.modal-title').textContent = 'Tambah Anggota';
    document.getElementById('id_input').value = '<?php echo $nextId; ?>';
    document.getElementById('id_input').readOnly = false;
    document.getElementById('nama_input').value = '';
    document.getElementById('hp_input').value = '';
    document.getElementById('alamat_input').value = '';
    document.getElementById('btnSubmit').name = 'create';
  }

  // Edit data
  function editAnggota(id, nama, hp, alamat) {
    document.querySelector('.modal-title').textContent = 'Edit Anggota';
    document.getElementById('id_input').value = id;
    document.getElementById('id_input').readOnly = true;
    document.getElementById('nama_input').value = nama;
    document.getElementById('hp_input').value = hp;
    document.getElementById('alamat_input').value = alamat;
    document.getElementById('btnSubmit').name = 'update';
  }
</script>
</body>
</html>
