<?php
/**
 * Student_Management/Studentmanagement.php 
 * Fixed: Update arguments & Direct Path to GradeMasterlist
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

// --- 0. FETCH ACTIVE ACADEMIC YEAR ---
$activeAY = $conn->query("SELECT ayID, schoolYear, semester FROM AcademicYear WHERE status = 'Active'")->fetch(PDO::FETCH_ASSOC);
$current_ayID = $activeAY['ayID'] ?? null;
$displaySemYear = $activeAY ? $activeAY['schoolYear'] . " (" . $activeAY['semester'] . ")" : "No Active Semester Set";

// --- 1. UPDATE STUDENT INFO LOGIC ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_student'])) {
    try {
        $hashed_password = !empty($_POST['pass']) ? password_hash($_POST['pass'], PASSWORD_BCRYPT) : null;

        // Siguraduhin na 15 ang ? dito para tumugma sa pinapadala mong data
        $stmt = $conn->prepare("EXEC sp_UpdateStudentInfo ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?");
        $stmt->execute([
            $_POST['student_id'], 
            $_POST['fname'] ?: null, 
            $_POST['mname'] ?: null, 
            $_POST['lname'] ?: null, 
            $_POST['email'] ?: null, 
            $_POST['phone'] ?: null, 
            $_POST['dob'] ?: null, 
            $_POST['sex'] ?: null,
            $_POST['street'] ?: null, 
            $_POST['city'] ?: null, 
            $_POST['zipcode'] ?: null, 
            $_POST['status'] ?: null,
            $_POST['course_id'] ?: null,
            $_POST['section_id'] ?: null, // Ito ang ika-14 argument
            $hashed_password              // Ito ang ika-15 argument
        ]);
        $message = "✅ Success: Student profile updated.";
    } catch (PDOException $e) {
        $message = "❌ Error: " . $e->getMessage();
    }
}

// --- 2. DELETE LOGIC ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_student'])) {
    if ($userRole === 'Super Admin') {
        try {
            $stmt = $conn->prepare("EXEC sp_DeleteStudent ?");
            $stmt->execute([$_POST['student_id']]);
            $message = "🗑️ Success: Student record deleted.";
        } catch (PDOException $e) { 
            $message = "❌ Error: " . $e->getMessage(); 
        }
    }
}

// --- 3. FETCH DATA ---
$search = $_GET['search'] ?? null;
$students = [];
try {
    $sql = "SELECT s.*, c.courseName, sec.sectionName
            FROM dbo.Student s
            LEFT JOIN dbo.Course c ON s.courseID = c.courseID
            LEFT JOIN dbo.Section sec ON s.sectionID = sec.sectionID";
    
    if ($search) {
        $sql .= " WHERE (s.studentID LIKE ? OR s.fName LIKE ? OR s.lName LIKE ?)";
        $stmt = $conn->prepare($sql);
        $searchTerm = "%$search%";
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    } else {
        $stmt = $conn->prepare($sql);
        $stmt->execute();
    }
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $courseStmt = $conn->query("SELECT courseID, courseName FROM Course");
    $courses = $courseStmt->fetchAll(PDO::FETCH_ASSOC);
    
    $sectionStmt = $conn->query("SELECT sectionID, sectionName FROM Section");
    $sections = $sectionStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { 
    $message = "Error: " . $e->getMessage(); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Management System</title>
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
                <p>Logged in as: <b><?php echo htmlspecialchars($userRole); ?></b> 
                    | Active: <b style="color:#2ecc71;"><?= htmlspecialchars($displaySemYear); ?></b></p>
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
                            <th>Course & Section</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $row): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($row['studentID']); ?></code></td>
                            <td><?= htmlspecialchars($row['fName']." ".$row['lName']); ?></td>
                            <td><?= htmlspecialchars($row['courseName'] ?? 'N/A'); ?> - <?= htmlspecialchars($row['sectionName'] ?? 'N/A'); ?></td>
                            <td><span class="badge-status <?= strtolower($row['status'] ?? 'pending'); ?>"><?= htmlspecialchars($row['status'] ?? 'Pending'); ?></span></td>
                            <td class="actions">
                                <button class="btn-edit" onclick='openEditModal(<?= json_encode($row); ?>)'>Profile</button>
                                
                                <button class="btn-grade" onclick="window.location.href='GradeMasterlist.php?search=<?= urlencode($row['studentID']); ?>'">Grades</button>
                                
                                <?php if($userRole === 'Super Admin'): ?>
                                <button type="button" class="btn-delete" onclick="confirmDelete('<?= $row['studentID']; ?>')">Delete</button>
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
            <div class="modal-header">
                <span>Update Student Profile</span>
                <button type="button" class="close-btn" onclick="closeModal('editModal')">×</button>
            </div>
            <form method="POST">
                <div class="modal-body grid-form">
                    <input type="hidden" name="student_id" id="edit_id">
                    <div class="field"><label>First Name</label><input type="text" name="fname" id="edit_fname"></div>
                    <div class="field"><label>Middle Name</label><input type="text" name="mname" id="edit_mname"></div>
                    <div class="field"><label>Last Name</label><input type="text" name="lname" id="edit_lname"></div>
                    <div class="field">
                        <label>Course</label>
                        <select name="course_id" id="edit_course">
                            <?php foreach($courses as $c): ?>
                                <option value="<?= $c['courseID'] ?>"><?= htmlspecialchars($c['courseName']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label>Section</label>
                        <select name="section_id" id="edit_section">
                            <?php foreach($sections as $sec): ?>
                                <option value="<?= $sec['sectionID'] ?>"><?= htmlspecialchars($sec['sectionName']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field"><label>Sex</label>
                        <select name="sex" id="edit_sex">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="field"><label>Birthday</label><input type="date" name="dob" id="edit_dob"></div>
                    <div class="field"><label>Email</label><input type="email" name="email" id="edit_email"></div>
                    <div class="field"><label>Phone</label><input type="text" name="phone" id="edit_phone"></div>
                    <div class="field"><label>New Password (Optional)</label><input type="password" name="pass" placeholder="Blank = No change"></div>
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
</body>
</html>