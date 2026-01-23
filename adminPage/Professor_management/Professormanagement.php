<?php
/**
 * Professor_Management/Professormanagement.php
 */
session_start();

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once '../../Database/database_Connection.php'; 

// Check if user is logged in
if (!isset($_SESSION['role'])) {
    header("Location: ../../Login_FacultyPage/loginFaculty.php");
    exit();
}

$userRole = $_SESSION['role'] ?? 'Staff'; 
$message = "";

// 1. UPDATE LOGIC (WITH PASSWORD HASHING)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_professor'])) {
    try {
        // --- STEP 1: HASH THE PASSWORD ---
        // Ginagamit ang BCRYPT para maging compatible sa password_verify() ng login
        $raw_password = $_POST['pass'];
        $hashed_password = password_hash($raw_password, PASSWORD_BCRYPT);
        // ---------------------------------

        // EXEC sp_UpdateProfessorInfo na may eksaktong 13 na '?'
        $stmt = $conn->prepare("EXEC sp_UpdateProfessorInfo ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?");
        $stmt->execute([
            $_POST['prof_id'],    // @ProfID
            $_POST['fname'],      // @FName
            $_POST['mname'],      // @MName
            $_POST['lname'],      // @LName
            $_POST['email'],      // @Email
            $_POST['phone'],      // @Phone
            $_POST['dept'],       // @Dept
            $_POST['street'],     // @Street
            $_POST['city'],       // @City
            $_POST['zipcode'],    // @ZipCode
            $_POST['status'],     // @Status
            $_POST['dob'],        // @DOB (Ika-12 na parameter)
            $hashed_password      // @Pass (Ika-13 na parameter - HASHED)
        ]);
        $message = "✅ Success: Professor information and encrypted password have been updated.";
    } catch (PDOException $e) { 
        $rawError = $e->getMessage();
        $message = "⚠️ " . trim(preg_replace('/^.*\]/', '', $rawError)); 
    }
}

// 2. DELETE LOGIC (Super Admin Only)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_professor'])) {
    if ($userRole !== 'Super Admin') {
        $message = "🚫 Error: Unauthorized action. Only Super Admin can delete.";
    } else {
        $idToDelete = $_POST['prof_id'];
        try {
            $stmt = $conn->prepare("EXEC sp_DeleteProfessor ?"); 
            $stmt->execute([$idToDelete]);
            $message = "🗑️ Success: Professor record has been deleted.";
        } catch (PDOException $e) { 
            $message = "❌ Error: " . trim(preg_replace('/^.*\]/', '', $e->getMessage()));
        }
    }
}

// 3. FETCH DATA
$search = $_GET['search'] ?? null;
$professors = [];
try {
    $stmt = $conn->prepare("EXEC sp_GetProfessorList ?");
    $stmt->execute([$search]);
    $professors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { 
    $message = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Professor Management | ISCP</title>
    <link rel="stylesheet" href="../sidebar.css">
    <link rel="stylesheet" href="Professormanagement.css">
    <script src="Professormanagement.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../sidebar.php'; ?>
    <main class="main-content">
        <header class="top-header">
            <div>
                <h1>Professor Management</h1>
                <p>Role Access: <b><?php echo htmlspecialchars($userRole); ?></b></p>
            </div>
            <div class="search-bar">
                <form method="GET">
                    <input type="text" name="search" placeholder="Search ID or Name..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                    <button type="submit">Search</button>
                </form>
            </div>
        </header>

        <?php if($message): ?> 
            <div class="alert-box">● <?php echo htmlspecialchars($message); ?></div> 
        <?php endif; ?>

        <div class="table-container-wide">
            <div class="glass-card">
                <table class="full-info-table">
                    <thead>
                        <tr>
                            <th>Prof ID</th>
                            <th>Full Name</th>
                            <th>Department</th>
                            <th>Contact Info</th>
                            <th>Employment Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($professors as $prof): ?>
                        <tr>
                            <td><code><?php echo htmlspecialchars($prof['professorID']); ?></code></td>
                            <td><?php echo htmlspecialchars($prof['fName']." ".$prof['lName']); ?></td>
                            <td><?php echo htmlspecialchars($prof['department']); ?></td>
                            <td><?php echo htmlspecialchars($prof['email']); ?><br><small><?php echo htmlspecialchars($prof['phoneNumber'] ?? 'No Phone'); ?></small></td>
                            <td>
                                <?php 
                                    $status = $prof['employmentStatus'] ?? 'Inactive';
                                    $statusClass = (strpos($status, 'Active') !== false) ? 'active' : 'inactive';
                                ?>
                                <span class="badge-status <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </td>
                            <td class="actions">
                                <button class="btn-edit" onclick='openEditModal(<?php echo json_encode($prof); ?>)'>Edit</button>

                                <?php if ($userRole === 'Super Admin'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="prof_id" value="<?php echo htmlspecialchars($prof['professorID']); ?>">
                                        <button type="submit" name="delete_professor" class="btn-delete" onclick="return confirm('Are you sure?')">Delete</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="editModal" class="modal-overlay">
        <div class="modal-box edit-box">
            <div class="modal-header">Edit Professor Profile</div>
            <form method="POST">
                <div class="modal-body grid-form">
                    <input type="hidden" name="prof_id" id="edit_id">
                    
                    <div class="field"><label>First Name</label><input type="text" name="fname" id="edit_fname" required></div>
                    <div class="field"><label>Middle Name</label><input type="text" name="mname" id="edit_mname"></div>
                    <div class="field"><label>Last Name</label><input type="text" name="lname" id="edit_lname" required></div>
                    
                    <div class="field"><label>Department</label><input type="text" name="dept" id="edit_dept"></div>
                    <div class="field"><label>Email</label><input type="email" name="email" id="edit_email" required></div>
                    <div class="field"><label>Phone</label><input type="text" name="phone" id="edit_phone"></div>

                    <div class="field">
                        <label>Date of Birth</label>
                        <input type="date" name="dob" id="edit_dob" required>
                    </div>

                    <div class="field">
                        <label>New Password (will be hashed)</label>
                        <input type="password" name="pass" placeholder="••••••••" required>
                    </div>
                    
                    <div class="field">
                        <label>Employment Status</label>
                        <select name="status" id="edit_status">
                            <option value="Inactive">Inactive (Pending)</option>
                            <option value="Active (Full-time)">Active (Full-time)</option>
                            <option value="Active (Part-time)">Active (Part-time)</option>
                            <option value="On-Leave">On-Leave</option>
                            <option value="Resigned">Resigned</option>
                        </select>
                    </div>

                    <div class="field"><label>Street</label><input type="text" name="street" id="edit_street"></div>
                    <div class="field"><label>City</label><input type="text" name="city" id="edit_city"></div>
                    <div class="field"><label>Zip Code</label><input type="text" name="zipcode" id="edit_zip"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('editModal')">Cancel</button>
                    <button type="submit" name="update_professor" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>