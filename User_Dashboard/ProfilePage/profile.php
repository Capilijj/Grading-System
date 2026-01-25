<?php
session_start();
require_once __DIR__ . '/../../Database/database_Connection.php';

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../../Login_StudentPage/loginStudent.php");
    exit();
}

$studentID = $_SESSION['studentID'] ?? '';
$update_status = "";

/**
 * 1. UPDATE LOGIC (Using Stored Procedure)
 * Siguraduhin na ang SP ay tumatanggap ng hiwalay na City at ZipCode
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_update'])) {
    $new_phone = $_POST['phone'];
    $new_email = $_POST['email'];
    $new_street = $_POST['street'];
    $new_city = $_POST['city'];
    $new_zip = $_POST['zipCode'];

    try {
        // CALL sp_UpdateStudentProfile(ID, Phone, Email, Street, City, Zip)
        $sql_update = "{sp_UpdateStudentProfile(?, ?, ?, ?, ?, ?)}";
        $stmt_upd = $conn->prepare($sql_update);
        $stmt_upd->execute([$studentID, $new_phone, $new_email, $new_street, $new_city, $new_zip]);
        
        $update_status = "success";
    } catch (Exception $e) {
        $update_status = "error: " . $e->getMessage();
    }
}

/**
 * 2. FETCH DATA FOR DISPLAY
 */
try {
    $query = "SELECT s.*, c.courseName 
              FROM dbo.Student s
              LEFT JOIN dbo.Course c ON s.courseID = c.courseID 
              WHERE s.studentID = ?";
              
    $stmt = $conn->prepare($query);
    $stmt->execute([$studentID]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        die("Student records not found.");
    }

    $mInit = !empty($student['mName']) ? substr($student['mName'], 0, 1) . "." : "";
    $fullName = strtoupper($student['lName'] . ", " . $student['fName'] . " " . $mInit);
    $displayCourse = !empty($student['courseName']) ? $student['courseName'] : "N/A";

} catch (Exception $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile - ISCP</title>
    <link rel="stylesheet" href="../Header/header.css">
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="../Footer/FooterDashboard.css">
</head>
<body>

    <?php include '../Header/header.php'; ?>

    <main class="profile-page-container">
        <div class="profile-card">
            <div class="profile-header-banner">STUDENT OFFICIAL ACADEMIC RECORD</div>

            <div class="profile-content">
                
                <?php if ($update_status === "success"): ?>
                    <div class="alert alert-success">Profile updated successfully!</div>
                <?php elseif (strpos($update_status, "error") !== false): ?>
                    <div class="alert alert-danger"><?php echo $update_status; ?></div>
                <?php endif; ?>

                <div class="profile-summary">
                    <h2><?php echo $fullName; ?></h2>
                    <span class="id-badge"><?php echo htmlspecialchars($studentID); ?></span>
                </div>

                <hr class="section-divider">

                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    <div class="form-grid">
                        
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" value="<?php echo $fullName; ?>" readonly class="readonly-input">
                        </div>

                        <div class="form-group">
                            <label>Degree / Course</label>
                            <input type="text" value="<?php echo htmlspecialchars($displayCourse); ?>" readonly class="readonly-input">
                        </div>

                        <div class="form-group">
                            <label>Year Level</label>
                            <input type="text" value="Year <?php echo htmlspecialchars($student['yearLvl']); ?>" readonly class="readonly-input">
                        </div>

                        <div class="form-group">
                            <label>Gender</label>
                            <input type="text" value="<?php echo ($student['sex'] == 'M' ? 'Male' : 'Female'); ?>" readonly class="readonly-input">
                        </div>

                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="text" value="<?php echo date('F d, Y', strtotime($student['dateOfBirth'])); ?>" readonly class="readonly-input">
                        </div>

                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" value="<?php echo htmlspecialchars($student['country']); ?>" readonly class="readonly-input">
                        </div>

                        <div class="form-group">
                            <label>Mobile Number</label>
                            <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phoneNumber']); ?>" class="editable-input" required>
                        </div>

                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" class="editable-input" required>
                        </div>

                        <div class="form-group full-width">
                            <label>Street Address</label>
                            <input type="text" name="street" value="<?php echo htmlspecialchars($student['street']); ?>" class="editable-input" required>
                        </div>

                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" value="<?php echo htmlspecialchars($student['city']); ?>" class="editable-input" required>
                        </div>

                        <div class="form-group">
                            <label>Zipcode</label>
                            <input type="text" name="zipCode" value="<?php echo htmlspecialchars($student['zipCode']); ?>" class="editable-input" required>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="btn_update" class="btn-save">SAVE CHANGES</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include '../Footer/FooterDashboard.php'; ?>
    
    <script src="../Header/header.js"></script>
</body>
</html>