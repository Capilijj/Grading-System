<?php
session_start();

// Prevent caching to secure logout
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// 1. PROTECTION LOGIC
if (!isset($_SESSION['role'])) {
    header("Location: ../Login_FacultyPage/loginFaculty.php");
    exit();
}

$currentRole = $_SESSION['role']; 
require_once '../../Database/database_Connection.php'; 

$message = "";
$search = $_GET['search'] ?? null;

// 2. ACTIVATION LOGIC (WITH AUTO-REFRESH FIX)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['activate_account'])) {
    if ($currentRole === 'Staff') {
        $message = "⚠️ Error: Staff accounts are not authorized to activate users.";
    } else {
        $role     = $_POST['role'];
        $idNumber = trim($_POST['id_number']); 
        $raw_password = $_POST['password'];

        // ENCRYPTION PROCESS - Consistent sa Prof Management mo
        $hashed_password = password_hash($raw_password, PASSWORD_BCRYPT);

        try {
            $stmt = $conn->prepare("EXEC sp_ActivateByEmail ?, ?, ?");
            $stmt->execute([$role, $idNumber, $hashed_password]);
            
            // FIX: Redirect back to same page to refresh the 'User List' table
            header("Location: Usermanagement.php?status=success&id=" . urlencode($idNumber));
            exit();
        } catch (PDOException $e) {
            $rawError = $e->getMessage();
            $cleanError = preg_replace('/^.*\]/', '', $rawError); 
            $message = "⚠️ " . trim($cleanError); 
        }
    }
}

// Success message handler
if (isset($_GET['status']) && $_GET['status'] === 'success') {
    $message = "✅ Success: Account " . htmlspecialchars($_GET['id']) . " has been activated and is now visible.";
}

// 3. FETCH DIRECTORY DATA
$allUsers = [];
try {
    // Siguraduhin na ang sp_GetAllUsers ay kumukuha sa Student, Professor, at Admin tables
    $stmtFetch = $conn->prepare("EXEC sp_GetAllUsers ?");
    $stmtFetch->execute([$search]);
    $allUsers = $stmtFetch->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { 
    $message = "❌ DB Error: " . trim(preg_replace('/^.*\]/', '', $e->getMessage())); 
    $allUsers = []; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - ISCP Admin</title>
    <link rel="stylesheet" href="../sidebar.css">
    <link rel="stylesheet" href="Usermanagement.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../sidebar.php'; ?>

    <main class="main-content">
        <header class="top-header">
            <div class="header-left">
                <h1>System User Directory</h1>
                <p>Access Level: <strong style="color: #e67e22;"><?php echo htmlspecialchars($currentRole); ?></strong></p>
                
                <?php if($message): ?>
                    <div class="alert-box" style="background: #fff; padding: 15px; border-left: 5px solid #2ecc71; margin: 10px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>
            </div>
        </header>

        <div class="admin-grid">
            <aside class="form-container">
                <div class="glass-card <?php echo ($currentRole === 'Staff') ? 'read-only' : ''; ?>">
                    <h3 style="margin-top:0; color:#0c225e;">Account Activation</h3>
                    
                    <?php if ($currentRole !== 'Staff'): ?>
                        <form method="POST">
                            <div class="field">
                                <label>ROLE TO ACTIVATE</label>
                                <select name="role" required>
                                    <option value="Student">Student</option>
                                    <option value="Professor">Professor</option>
                                    <option value="Admin">Admin/Staff</option>
                                </select>
                            </div>

                            <div class="field">
                                <label>ACCOUNT ID NUMBER</label>
                                <input type="text" name="id_number" placeholder="e.g. 2026-0001-STU" required>
                            </div>

                            <div class="field">
                                <label>SET PASSWORD</label>
                                <input type="password" name="password" placeholder="••••••••" required>
                            </div>

                            <button type="submit" name="activate_account" class="btn-primary" style="width:100%; padding:12px; background:#0c225e; color:white; border:none; border-radius:5px; cursor:pointer; font-weight:bold;">
                                ACTIVATE ACCESS
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="staff-notice">
                            <p>Staff accounts (View Only Mode)</p>
                        </div>
                    <?php endif; ?>
                </div>
            </aside>

            <section class="table-container">
                <div class="glass-card">
                    <div class="table-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                        <h3 style="margin:0; color:#0c225e;">User List</h3>
                        <form method="GET" class="search-wrapper">
                            <input type="text" name="search" placeholder="Search ID or Name..." value="<?php echo htmlspecialchars($search ?? ''); ?>" style="padding:8px; border:1px solid #ddd; border-radius:4px;">
                            <button type="submit" class="btn-filter" style="padding:8px 15px; background:#0c225e; color:white; border:none; border-radius:4px; cursor:pointer;">Search</button>
                        </form>
                    </div>
                    
                    <table class="modern-table" style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f8f9fa; text-align:left;">
                                <th style="padding:12px; border-bottom:2px solid #dee2e6;">Name / Email</th>
                                <th style="padding:12px; border-bottom:2px solid #dee2e6;">Role</th>
                                <th style="padding:12px; border-bottom:2px solid #dee2e6;">Account ID</th>
                                <th style="padding:12px; border-bottom:2px solid #dee2e6;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($allUsers)): foreach ($allUsers as $user): ?>
                                <tr>
                                    <td style="padding:12px; border-bottom:1px solid #eee;">
                                        <strong><?php echo htmlspecialchars($user['fName'] . " " . $user['lName']); ?></strong><br>
                                        <small style="color:#666;"><?php echo htmlspecialchars($user['email']); ?></small>
                                    </td>
                                    <td style="padding:12px; border-bottom:1px solid #eee;">
                                        <span class="role-badge <?php echo strtolower($user['Role']); ?>" style="padding:4px 8px; border-radius:12px; font-size:11px; font-weight:bold; background:#e1f5fe; color:#01579b;">
                                            <?php echo htmlspecialchars($user['Role']); ?>
                                        </span>
                                    </td>
                                    <td style="padding:12px; border-bottom:1px solid #eee;"><code><?php echo htmlspecialchars($user['AccountID']); ?></code></td>
                                    <td style="padding:12px; border-bottom:1px solid #eee;">
                                        <?php 
                                            // Status logic
                                            $isPending = (empty($user['password']) || $user['password'] === 'PENDING');
                                        ?>
                                        <span class="status-indicator <?php echo !$isPending ? 'active' : 'pending'; ?>" style="color: <?php echo !$isPending ? '#2ecc71' : '#e67e22'; ?>; font-weight:bold;">
                                            ● <?php echo !$isPending ? 'Active' : 'Pending'; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="4" style="text-align:center; padding:20px;">No records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </main>
</body>
</html>