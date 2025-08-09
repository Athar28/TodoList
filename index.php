<?php
session_start();

// Inisialisasi data
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

/**
 * Tambah tugas baru
 */
function addTask($taskName) {
    $taskName = trim($taskName);
    if ($taskName !== '') {
        $_SESSION['tasks'][] = [
            'name' => $taskName,
            'done' => false
        ];
    }
}

/**
 * Toggle status selesai
 */
function toggleTask($index) {
    if (isset($_SESSION['tasks'][$index])) {
        $_SESSION['tasks'][$index]['done'] = !$_SESSION['tasks'][$index]['done'];
    }
}

/**
 * Hapus tugas
 */
function deleteTask($index) {
    if (isset($_SESSION['tasks'][$index])) {
        array_splice($_SESSION['tasks'], $index, 1);
    }
}

/**
 * Edit tugas
 */
function editTask($index, $newName) {
    if (isset($_SESSION['tasks'][$index]) && trim($newName) !== '') {
        $_SESSION['tasks'][$index]['name'] = trim($newName);
    }
}

// Proses request POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['task'])) {
        addTask($_POST['task']);
    }
    if (isset($_POST['toggle'])) {
        toggleTask($_POST['toggle']);
    }
    if (isset($_POST['delete'])) {
        deleteTask($_POST['delete']);
    }
    if (isset($_POST['edit_index']) && isset($_POST['edit_name'])) {
        editTask($_POST['edit_index'], $_POST['edit_name']);
    }

    // Redirect agar refresh tidak mengulang POST
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Todo List dengan Edit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h1 class="mb-4 text-center">📝 Todo List</h1>

    <!-- Form tambah tugas -->
    <form method="POST" class="d-flex mb-4">
        <input type="text" name="task" class="form-control me-2" placeholder="Tulis tugas baru..." required>
        <button type="submit" class="btn btn-primary">Tambah</button>
    </form>

    <!-- Daftar tugas -->
    <ul class="list-group">
        <?php if (empty($_SESSION['tasks'])): ?>
            <li class="list-group-item text-muted">Tidak ada tugas</li>
        <?php else: ?>
            <?php foreach ($_SESSION['tasks'] as $index => $task): ?>
                <li class="list-group-item d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <!-- Checkbox -->
                        <form method="POST" class="me-2">
                            <input type="hidden" name="toggle" value="<?= $index ?>">
                            <input class="form-check-input" type="checkbox" onChange="this.form.submit()" <?= $task['done'] ? 'checked' : '' ?>>
                        </form>

                        <!-- Nama tugas / Form edit -->
                        <?php if (isset($_GET['edit']) && $_GET['edit'] == $index): ?>
                            <form method="POST" class="d-flex">
                                <input type="hidden" name="edit_index" value="<?= $index ?>">
                                <input type="text" name="edit_name" class="form-control form-control-sm me-2" value="<?= htmlspecialchars($task['name']) ?>" required>
                                <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                            </form>
                        <?php else: ?>
                            <span class="<?= $task['done'] ? 'text-decoration-line-through text-muted' : '' ?>">
                                <?= htmlspecialchars($task['name']) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Tombol aksi -->
                    <div class="d-flex">
                        <?php if (!isset($_GET['edit']) || $_GET['edit'] != $index): ?>
                            <a href="?edit=<?= $index ?>" class="btn btn-warning btn-sm me-2">Edit</a>
                        <?php endif; ?>
                        <form method="POST" class="m-0">
                            <input type="hidden" name="delete" value="<?= $index ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

</body>
</html>
