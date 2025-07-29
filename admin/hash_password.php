<?php
$hash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $hash = password_hash($password, PASSWORD_DEFAULT);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Password Hash Generator</title>
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2>Admin Password Hash Generator</h2>
    <form method="post">
        <div class="mb-3">
            <label>Password:</label>
            <input type="text" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Generate Hash</button>
    </form>
    <?php if (!empty($hash)): ?>
        <div class="alert alert-success mt-4">
            <strong>Hashed Password:</strong> <code><?= htmlspecialchars($hash) ?></code>
        </div>
    <?php endif; ?>
</div>
</body>
</html> 