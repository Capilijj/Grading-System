<?php
/**
 * Student_Management/Studentmanagement.php
 */
session_start();
require_once '../../Database/database_Connection.php'; 

$userRole = $_SESSION['role'] ?? 'Staff'; 
$message = "";

// 1. UPDATE LOGIC VIA STORED PROCEDURE
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_student'])) {
    try {
        // EXEC sp_UpdateStudentInfo na may kumpletong parameters
        $stmt = $conn->prepare("EXEC sp_UpdateStudentInfo ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?");
        $stmt->execute([
            $_POST['student_id'], 
            $_POST['fname'], 
            $_POST['mname'], 
            $_POST['lname'], 
            $_POST['email'], 
            $_POST['phone'], 
            $_POST['dob'], 
            $_POST['sex'],
            $_POST['street'], 
            $_POST['city'], 
            $_POST['zipcode'], 
            $_POST['status'],
            $_POST['course_id']
        ]);
        $message = "✅ Success: Student information has been updated.";
    } catch (PDOException $e) {
        $message = "❌ Error: Could not update record. " . $e->getMessage();
    }
}

// 2. DELETE LOGIC VIA STORED PROCEDURE (Admin Only)
if (isset($_POST['confirm_delete']) && $userRole === 'Admin') {
    $idToDelete = $_POST['student_id'];
    try {
        // Gumagamit ng SP para sa Delete para sa rubriks
        $stmt = $conn->prepare("EXEC sp_DeleteStudent ?");
        $stmt->execute([$idToDelete]);
        $message = "✅ Success: Student $idToDelete has been removed.";
    } catch (PDOException $e) { 
        $message = "❌ Error: " . $e->getMessage(); 
    }
}

// 3. FETCH DATA VIA STORED PROCEDURE (Search & Display)
$search = $_GET['search'] ?? null;
$students = [];
try {
    // Tinatawag ang SP para sa pagkuha ng listahan (sp_GetStudentList)
    $stmt = $conn->prepare("EXEC sp_GetStudentList ?");
    $stmt->execute([$search]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Kumuha ng listahan ng kurso para sa dropdown menu sa Edit Modal
    $courseStmt = $conn->query("SELECT courseID, courseName FROM Course");
    $courses = $courseStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { 
    $message = "Error: " . $e->getMessage(); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Information Management</title>
    <link rel="stylesheet" href="../sidebar.css">
    <link rel="stylesheet" href="Studentmanagement.css">
    <script src="Studentmanagement.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <?php include '../sidebar.php'; ?>
    <main class="main-content">
        <header class="top-header">
            <div>
                <h1>Student Management</h1>
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
                            <th>ID Number</th>
                            <th>Full Name</th>
                            <th>Course</th>
                            <th>Gender/DOB</th>
                            <th>Contact Info</th>
                            <th>Complete Address</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $row): ?>
                        <tr>
                            <td><code><?php echo htmlspecialchars($row['studentID']); ?></code></td>
                            <td><?php echo htmlspecialchars($row['fName']." ".$row['lName']); ?></td>
                            <td><?php echo htmlspecialchars($row['courseName'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['sex'] ?? ''); ?> | <?php echo htmlspecialchars($row['dateOfBirth'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?><br><small><?php echo htmlspecialchars($row['phoneNumber'] ?? ''); ?></small></td>
                            <td><div class="addr-cell"><?php echo htmlspecialchars(($row['street'] ?? '').", ".($row['city'] ?? '')." ".($row['zipCode'] ?? '')); ?></div></td>
                            <td><span class="badge-status <?php echo strtolower($row['status'] ?? 'pending'); ?>"><?php echo htmlspecialchars($row['status'] ?? 'Pending'); ?></span></td>
                            <td class="actions">
                                <button class="btn-edit" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                                <button class="btn-delete" onclick="confirmDelete('<?php echo htmlspecialchars($row['studentID']); ?>')">Delete</button>
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
            <div class="modal-header">Edit Student Information</div>
            <form method="POST">
                <div class="modal-body grid-form">
                    <input type="hidden" name="student_id" id="edit_id">
                    
                    <div class="field"><label>First Name</label><input type="text" name="fname" id="edit_fname" required></div>
                    <div class="field"><label>Middle Name</label><input type="text" name="mname" id="edit_mname"></div>
                    <div class="field"><label>Last Name</label><input type="text" name="lname" id="edit_lname" required></div>
                    
                    <div class="field">
                        <label>Course</label>
                        <select name="course_id" id="edit_course" required>
                            <?php foreach($courses as $c): ?>
                                <option value="<?php echo $c['courseID']; ?>"><?php echo htmlspecialchars($c['courseName']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label>Sex</label>
                        <select name="sex" id="edit_sex">
                            <option value="M">Male</option>
                            <option value="F">Female</option>
                        </select>
                    </div>

                    <div class="field"><label>Date of Birth</label><input type="date" name="dob" id="edit_dob" required></div>
                    <div class="field"><label>Email</label><input type="email" name="email" id="edit_email" required></div>
                    <div class="field"><label>Phone Number</label><input type="text" name="phone" id="edit_phone"></div>
                    <div class="field"><label>Street</label><input type="text" name="street" id="edit_street"></div>
                    <div class="field"><label>City</label><input type="text" name="city" id="edit_city"></div>
                    <div class="field"><label>Zip Code</label><input type="text" name="zipcode" id="edit_zip"></div>

                    <div class="field">
                        <label>Status</label>
                        <select name="status" id="edit_status">
                            <option value="Regular">Regular</option>
                            <option value="Irregular">Irregular</option>
                            <option value="Dropped">Dropped</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('editModal')">Cancel</button>
                    <button type="submit" name="update_student" class="btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header delete-head">⚠️ Confirm Deletion</div>
            <div class="modal-body">Are you sure you want to delete student <b id="del_name"></b>?</div>
            <form method="POST">
                <input type="hidden" name="student_id" id="del_id">
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('deleteModal')">Cancel</button>
                    <button type="submit" name="confirm_delete" class="btn-danger">Confirm Delete</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>