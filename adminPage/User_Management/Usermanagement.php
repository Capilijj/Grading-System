<?php
/**
 * Studentmanagement.php - WITH STAFF ROLE PLACEHOLDER
 */
require_once '../../Database/database_Connection.php'; 

$message = "";
$search = $_GET['search'] ?? null;

// 1. ACCOUNT ACTIVATION LOGIC
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['activate_account'])) {
    $role     = $_POST['role'];
    $idNumber = trim($_POST['id_number']); 
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("EXEC sp_ActivateByEmail ?, ?, ?");
        $stmt->execute([$role, $idNumber, $password]);
        
        $message = "✅ Success: $role $idNumber has been successfully activated.";
    } catch (PDOException $e) {
        $rawError = $e->getMessage();
        $errorParts = explode(']', $rawError);
        $message = "⚠️ " . trim(end($errorParts)); 
    }
}

// 2. FETCH DIRECTORY DATA
$allUsers = [];
try {
    $stmtFetch = $conn->prepare("EXEC sp_GetAllUsers ?");
    $stmtFetch->execute([$search]);
    $allUsers = $stmtFetch->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $allUsers = []; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Activation - ISCP Admin</title>
    <link rel="stylesheet" href="../sidebar.css">
    <link rel="stylesheet" href="Usermanagement.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../sidebar.php'; ?>

    <main class="main-content">
        <header class="top-header">
            <div class="header-left">
                <h1>User Activation</h1>
                <p>Manage access for Students, Professors, and Staff members.</p>
                <?php if($message): ?>
                    <div class="alert-box">● <?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
            </div>
        </header>

        <div class="admin-grid">
            <aside class="form-container">
                <div class="glass-card">
                    <h3 style="margin-top:0; color:#0c225e;">Activate Access</h3>
                    <form method="POST">
                        <div class="field">
                            <label>SELECT ROLE</label>
                            <select name="role" required>
                                <option value="Student">Student</option>
                                <option value="Professor">Professor</option>
                                <option value="Staff">Staff</option> </select>
                        </div>

                        <div class="field">
                            <label>ACCOUNT ID NUMBER</label>
                            <input type="text" name="id_number" placeholder="Enter ID (e.g. 2026-0001-STF)" required>
                        </div>

                        <div class="field">
                            <label>SET PASSWORD</label>
                            <input type="password" name="password" placeholder="••••••••" required>
                        </div>

                        <button type="submit" name="activate_account" class="btn-primary">ACTIVATE ACCOUNT</button>
                    </form>
                </div>
            </aside>

            <section class="table-container">
                <div class="glass-card">
                    <div class="table-header">
                        <h3 style="margin:0; color:#0c225e;">System Directory</h3>
                        <form method="GET" class="search-wrapper">
                            <div class="search-box">
                                <span class="search-icon">🔍</span>
                                <input type="text" name="search" placeholder="Search ID or Name..." value="<?php echo htmlspecialchars($search); ?>">
                            </div>
                            <button type="submit" class="btn-filter">Search</button>
                        </form>
                    </div>
                    
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Name / Email</th>
                                <th>Role</th>
                                <th>Account ID (PK)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($allUsers)): foreach ($allUsers as $user): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($user['fName'] . " " . $user['lName']); ?></strong><br>
                                        <small><?php echo htmlspecialchars($user['email']); ?></small>
                                    </td>
                                    <td><span class="role-badge <?php echo strtolower($user['Role']); ?>"><?php echo $user['Role']; ?></span></td>
                                    <td><code><?php echo htmlspecialchars($user['AccountID']); ?></code></td>
                                    <td>
                                        <?php 
                                            $isPending = ($user['password'] === 'PENDING');
                                        ?>
                                        <span class="status-indicator <?php echo !$isPending ? 'active' : 'pending'; ?>">
                                            <?php echo !$isPending ? 'Active' : 'Pending'; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="4" style="text-align:center;">No records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
    <script src="Usermanagement.js" defer></script>
</body>
</html>