<?php
session_start();
require_once '../../Database/database_Connection.php'; 

$userRole = $_SESSION['role'] ?? 'Staff'; 
$message = "";

// UPDATE LOGIC
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_professor'])) {
    try {
        $stmt = $conn->prepare("EXEC sp_UpdateProfessorInfo ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?");
        $stmt->execute([
            $_POST['prof_id'], $_POST['fname'], $_POST['mname'], $_POST['lname'],
            $_POST['email'], $_POST['phone'], $_POST['dept'],
            $_POST['street'], $_POST['city'], $_POST['zipcode'], $_POST['status']
        ]);
        $message = "✅ Success: Professor information updated.";
    } catch (PDOException $e) { $message = "❌ Error: " . $e->getMessage(); }
}

// FETCH DATA
$search = $_GET['search'] ?? null;
try {
    $stmt = $conn->prepare("EXEC sp_GetProfessorList ?");
    $stmt->execute([$search]);
    $professors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { $message = "Error: " . $e->getMessage(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Professor Management | ISCP</title>
    <link rel="stylesheet" href="../sidebar.css">
    <link rel="stylesheet" href="Professormanagement.css">
    <script src="Professormanagement.js" defer></script>
</head>
<body>
    <?php include '../sidebar.php'; ?>
    <main class="main-content">
        <header class="top-header">
            <div>
                <h1>Professor Management</h1>
                <p>Role Access: <b><?php echo $userRole; ?></b></p>
            </div>
            <div class="search-bar">
                <form method="GET">
                    <input type="text" name="search" placeholder="Search ID or Name..." value="<?php echo htmlspecialchars($search ?? ''); ?>">
                    <button type="submit">Search</button>
                </form>
            </div>
        </header>

        <?php if($message): ?> <div class="status-alert"><?php echo $message; ?></div> <?php endif; ?>

        <div class="table-container-wide">
            <div class="glass-card">
                <table class="full-info-table">
                    <thead>
                        <tr>
                            <th>Prof ID</th>
                            <th>Full Name</th>
                            <th>Department</th>
                            <th>Contact Info</th>
                            <th>Complete Address</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($professors as $prof): ?>
                        <tr>
                            <td><code><?php echo $prof['professorID']; ?></code></td>
                            <td><?php echo $prof['fName']." ".$prof['lName']; ?></td>
                            <td><?php echo $prof['department']; ?></td>
                            <td><?php echo $prof['email']; ?><br><small><?php echo $prof['phoneNumber']; ?></small></td>
                            <td><div class="addr-cell"><?php echo $prof['street'].", ".$prof['city']." ".$prof['zipCode']; ?></div></td>
                            <td><span class="badge-status <?php echo strtolower($prof['employmentStatus'] ?? 'active'); ?>">
                                <?php echo $prof['employmentStatus'] ?? 'Active'; ?>
                            </span></td>
                            <td class="actions">
                                <button class="btn-edit" onclick='openEditModal(<?php echo json_encode($prof); ?>)'>Edit</button>
                                <button class="btn-delete">Delete</button>
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
                    <div class="field"><label>Street</label><input type="text" name="street" id="edit_street"></div>
                    <div class="field"><label>City</label><input type="text" name="city" id="edit_city"></div>
                    <div class="field"><label>Zip Code</label><input type="text" name="zipcode" id="edit_zip"></div>
                    <div class="field">
                        <label>Status</label>
                        <select name="status" id="edit_status">
                            <option value="Active">Active</option>
                            <option value="On-Leave">On-Leave</option>
                            <option value="Resigned">Resigned</option>
                        </select>
                    </div>
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