<?php
session_start();
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

if (!isset($_SESSION['role'])) {
    header("Location: ../Login_FacultyPage/loginFaculty.php");
    exit();
}

$currentRole = $_SESSION['role']; 
require_once '../../Database/database_Connection.php'; 

$message = "";
$errorDetails = [];

// --- 1. FETCH DATA FOR DROPDOWNS (PURE STORED PROCEDURES) ---
try {
    // Active Academic Year
    $stmtAY = $conn->prepare("EXEC sp_GetActiveAY");
    $stmtAY->execute();
    $activeAY = $stmtAY->fetch(PDO::FETCH_ASSOC);
    $current_ayID = $activeAY['ayID'] ?? null;
    $displayAY = $activeAY ? $activeAY['schoolYear'] . " (" . $activeAY['semester'] . ")" : "Not Set";

    // Courses
    $stmtC = $conn->prepare("EXEC sp_GetCourses");
    $stmtC->execute();
    $courses = $stmtC->fetchAll(PDO::FETCH_ASSOC);

    // Sections
    $stmtS = $conn->prepare("EXEC sp_GetSections");
    $stmtS->execute();
    $sections = $stmtS->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $message = "❌ Initialization Error: " . $e->getMessage();
}

// --- 2. VALIDATION FUNCTION ---
function validateUserInput($data, &$errors) {
    $role = $data['role'] ?? '';
    
    // Required fields validation
    if (empty($data['id_number'])) {
        $errors[] = "ID Number is required";
    }
    
    if (empty($data['fName']) || empty($data['lName'])) {
        $errors[] = "First Name and Last Name are required";
    }
    
    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email address is required";
    }
    
    if (empty($data['password'])) {
        $errors[] = "Password is required";
    }
    
    if (empty($data['dob'])) {
        $errors[] = "Birth date is required";
    }
    
    // Role-specific validation
    if ($role === 'Student') {
        if (empty($data['course_id']) || $data['course_id'] == 0) {
            $errors[] = "Course selection is required for students";
        }
        if (empty($data['section_id']) || $data['section_id'] == 0) {
            $errors[] = "Section selection is required for students";
        }
        if (empty($data['yearLvl']) || $data['yearLvl'] < 1 || $data['yearLvl'] > 4) {
            $errors[] = "Valid year level (1-4) is required for students";
        }
    }
    
    if ($role === 'Professor') {
        if (empty($data['department'])) {
            $errors[] = "Department is required for professors";
        }
    }
    
    return empty($errors);
}

// --- 3. CHECK FOR DUPLICATE ID ---
function checkDuplicateID($conn, $role, $idNumber) {
    try {
        $table = ($role === 'Student') ? 'Student' : (($role === 'Professor') ? 'Professor' : 'Staff');
        $column = ($role === 'Student') ? 'studentID' : (($role === 'Professor') ? 'professorID' : 'staffID');
        
        $stmt = $conn->prepare("SELECT COUNT(*) FROM dbo.$table WHERE $column = ?");
        $stmt->execute([$idNumber]);
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

// --- 4. PROCESS FORM SUBMISSION ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_account'])) {
    
    // Collect all POST data
    $formData = [
        'role'      => $_POST['role'] ?? '',
        'id_number' => trim($_POST['id_number'] ?? ''),
        'email'     => $_POST['email'] ?? '',
        'password'  => $_POST['password'] ?? '',
        'account_status' => $_POST['account_status'] ?? 'Regular',
        'fName'  => $_POST['fName'] ?? '',
        'mName'  => $_POST['mName'] ?? '',
        'lName'  => $_POST['lName'] ?? '',
        'dob'    => $_POST['dob'] ?? null,
        'sex'    => $_POST['sex'] ?? 'Male',
        'phone'  => $_POST['phone'] ?? '',
        'street' => $_POST['street'] ?? '',
        'city'   => $_POST['city'] ?? '',
        'zip'    => $_POST['zip'] ?? '',
        'course_id' => (int)($_POST['course_id'] ?? 0),
        'section_id' => (int)($_POST['section_id'] ?? 0),
        'department' => $_POST['department'] ?? null,
        'yearLvl' => (int)($_POST['yearLvl'] ?? 1)
    ];
    
    // Validate input
    if (!validateUserInput($formData, $errorDetails)) {
        $message = "❌ Validation Failed:<br>" . implode("<br>", $errorDetails);
    }
    // Check for duplicate ID
    elseif (checkDuplicateID($conn, $formData['role'], $formData['id_number'])) {
        $message = "⚠️ Error: ID Number '{$formData['id_number']}' already exists in the system.";
    }
    // Check if Academic Year is active (for students only)
    elseif (!$current_ayID && $formData['role'] === 'Student') {
        $message = "⚠️ Error: No active Academic Year is set. Please activate an academic year first.";
    }
    // Proceed with registration
    else {
        try {
            $pwd = password_hash($formData['password'], PASSWORD_BCRYPT);
            $country = "Philippines";
            
            // ROLE-BASED ACCOUNT CREATION
            if ($formData['role'] === 'Student') {
                // Students: Use full procedure with course assignment
                $stmt = $conn->prepare("EXEC dbo.sp_UniversalActivate 
                    @Role=?, @AccountID=?, @Password=?, @TargetID=?, @SectionID=?, 
                    @fName=?, @mName=?, @lName=?, @dob=?, @sex=?, @email=?, @phone=?, 
                    @street=?, @city=?, @country=?, @zip=?, @dept=?, @yearLvl=?, @Status=?");
                
                $stmt->execute([
                    $formData['role'], 
                    $formData['id_number'], 
                    $pwd, 
                    $formData['course_id'], 
                    $formData['section_id'],
                    $formData['fName'], 
                    $formData['mName'], 
                    $formData['lName'], 
                    $formData['dob'], 
                    $formData['sex'], 
                    $formData['email'], 
                    $formData['phone'],
                    $formData['street'], 
                    $formData['city'], 
                    $country, 
                    $formData['zip'], 
                    null, 
                    $formData['yearLvl'], 
                    $formData['account_status']
                ]);

                // Auto-seed grades for student
                if ($current_ayID) {
                    $seedGrade = $conn->prepare("
                        INSERT INTO dbo.Grade (studentID, professorID, subjectID, finalGrade, ayID, dateUpdated, remarks)
                        SELECT DISTINCT ?, ps.professorID, sched.subjectID, '', sched.ayID, GETDATE(), 'ENROLLED'
                        FROM dbo.Schedule sched
                        JOIN dbo.ProfessorSubject ps ON sched.profSubID = ps.profSubID
                        WHERE sched.sectionID = ? AND sched.ayID = ?
                    ");
                    $seedGrade->execute([$formData['id_number'], $formData['section_id'], $current_ayID]);
                }
            }
            elseif ($formData['role'] === 'Professor') {
                // Professors: Create account only, NO subject assignment
                $stmt = $conn->prepare("EXEC dbo.sp_CreateProfessorAccount 
                    @ProfessorID=?, @Password=?, @fName=?, @mName=?, @lName=?, 
                    @dob=?, @sex=?, @email=?, @phone=?, @street=?, @city=?, 
                    @country=?, @zip=?, @dept=?, @Status=?");
                
                $stmt->execute([
                    $formData['id_number'], 
                    $pwd, 
                    $formData['fName'], 
                    $formData['mName'], 
                    $formData['lName'], 
                    $formData['dob'], 
                    $formData['sex'], 
                    $formData['email'], 
                    $formData['phone'],
                    $formData['street'], 
                    $formData['city'], 
                    $country, 
                    $formData['zip'], 
                    $formData['department'], 
                    $formData['account_status']
                ]);
            }
            elseif ($formData['role'] === 'Staff') {
                // Staff: Simple account creation
                $stmt = $conn->prepare("EXEC dbo.sp_UniversalActivate 
                    @Role=?, @AccountID=?, @Password=?, @TargetID=?, @SectionID=?, 
                    @fName=?, @mName=?, @lName=?, @dob=?, @sex=?, @email=?, @phone=?, 
                    @street=?, @city=?, @country=?, @zip=?, @dept=?, @yearLvl=?, @Status=?");
                
                $stmt->execute([
                    $formData['role'], 
                    $formData['id_number'], 
                    $pwd, 
                    0, 
                    0,
                    $formData['fName'], 
                    $formData['mName'], 
                    $formData['lName'], 
                    $formData['dob'], 
                    $formData['sex'], 
                    $formData['email'], 
                    $formData['phone'],
                    $formData['street'], 
                    $formData['city'], 
                    $country, 
                    $formData['zip'], 
                    null, 
                    0, 
                    $formData['account_status']
                ]);
            }

            $message = "✅ Success: {$formData['role']} account created successfully! (ID: {$formData['id_number']})";
            
            // Clear form by redirecting (PRG pattern)
            header("Location: " . $_SERVER['PHP_SELF'] . "?success=1");
            exit();
            
        } catch (PDOException $e) {
            $errorMsg = $e->getMessage();
            
            // Parse specific SQL errors
            if (strpos($errorMsg, 'FK_Student_Section') !== false) {
                $message = "⚠️ Error: Invalid Section selected. Please choose a valid section.";
            } elseif (strpos($errorMsg, 'FK_Student_Course') !== false) {
                $message = "⚠️ Error: Invalid Course selected. Please choose a valid course.";
            } elseif (strpos($errorMsg, 'UNIQUE') !== false || strpos($errorMsg, 'duplicate') !== false) {
                $message = "⚠️ Error: This ID number or email already exists.";
            } else {
                $message = "⚠️ Error: " . trim(preg_replace('/^.*\]/', '', $errorMsg));
            }
        }
    }
}

// Show success message from redirect
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = "✅ Account created successfully! You can create another account.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management - ISCP</title>
    <link rel="stylesheet" href="../sidebar.css">
    <link rel="stylesheet" href="Usermanagement.css">
    <script src="Usermanagement.js" defer></script>
</head>
<body>
    <?php include '../sidebar.php'; ?>
    <main class="main-content">
        <header class="top-header">
            <div class="header-left">
                <h1>User Registration</h1>
                <p>Role: <span class="role-badge"><?= htmlspecialchars($currentRole) ?></span> | AY: <strong style="color:#2ecc71;"><?= htmlspecialchars($displayAY) ?></strong></p>
            </div>
        </header>

        <?php if($message): ?>
            <div class="alert-box <?= (strpos($message, '✅') !== false) ? 'success' : 'error' ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="central-form-wrapper">
            <div class="glass-card">
                <form method="POST" id="registrationForm">
                    <div class="form-section">
                        <h4>ACCOUNT ROLE & ID</h4>
                        <div class="form-grid">
                            <div class="field">
                                <label>ROLE <span class="required">*</span></label>
                                <select name="role" id="roleSelect" required>
                                    <option value="">-- Choose --</option>
                                    <option value="Student">Student</option>
                                    <option value="Professor">Professor</option>
                                    <option value="Staff">Staff</option>
                                </select>
                            </div>
                            <div class="field">
                                <label id="idLabel">ID NUMBER <span class="required">*</span></label>
                                <input type="text" name="id_number" id="idNumberInput" required>
                                <small class="helper-text">Enter unique ID number</small>
                            </div>
                            <div class="field">
                                <label>ACCOUNT STATUS <span class="required">*</span></label>
                                <select name="account_status" id="statusSelect" required></select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>PERSONAL INFORMATION</h4>
                        <div class="form-grid">
                            <div class="field">
                                <label>FIRST NAME <span class="required">*</span></label>
                                <input type="text" name="fName" required>
                            </div>
                            <div class="field">
                                <label>MIDDLE NAME</label>
                                <input type="text" name="mName">
                            </div>
                            <div class="field">
                                <label>LAST NAME <span class="required">*</span></label>
                                <input type="text" name="lName" required>
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="field">
                                <label>BIRTH DATE <span class="required">*</span></label>
                                <input type="date" name="dob" required>
                            </div>
                            <div class="field">
                                <label>SEX <span class="required">*</span></label>
                                <select name="sex" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>CONTACT & ADDRESS</h4>
                        <div class="form-grid">
                            <div class="field">
                                <label>EMAIL <span class="required">*</span></label>
                                <input type="email" name="email" required>
                            </div>
                            <div class="field">
                                <label>PHONE</label>
                                <input type="text" name="phone" placeholder="+63 9XX XXX XXXX">
                            </div>
                        </div>
                        <div class="form-grid">
                            <div class="field">
                                <label>STREET</label>
                                <input type="text" name="street">
                            </div>
                            <div class="field">
                                <label>CITY</label>
                                <input type="text" name="city">
                            </div>
                            <div class="field">
                                <label>ZIP CODE</label>
                                <input type="text" name="zip">
                            </div>
                        </div>
                    </div>

                    <div class="form-section" id="academicSection">
                        <h4>ACADEMIC ASSIGNMENT</h4>
                        <div class="form-grid">
                            <div id="studentSpecific" style="display:none;">
                                <div class="field">
                                    <label>COURSE <span class="required">*</span></label>
                                    <select name="course_id" id="courseSelect">
                                        <option value="">-- Choose Course --</option>
                                        <?php foreach ($courses as $c): ?>
                                            <option value="<?= $c['courseID'] ?>"><?= $c['courseName'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="field">
                                    <label>YEAR LEVEL <span class="required">*</span></label>
                                    <select name="yearLvl">
                                        <option value="1">1st Year</option>
                                        <option value="2">2nd Year</option>
                                        <option value="3">3rd Year</option>
                                        <option value="4">4th Year</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label>SECTION <span class="required">*</span></label>
                                    <select name="section_id" id="sectionSelect">
                                        <option value="">-- Choose Section --</option>
                                        <?php foreach ($sections as $sec): ?>
                                            <option value="<?= $sec['sectionID'] ?>"><?= $sec['sectionName'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div id="profDept" class="field" style="display:none;">
                                <label>DEPARTMENT <span class="required">*</span></label>
                                <input type="text" name="department" placeholder="e.g. IT, CS, or ED">
                                <small class="helper-text">Subject and section assignments will be done via Class Scheduling</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>SECURITY SETTINGS</h4>
                        <div class="field">
                            <label>SET PASSWORD <span class="required">*</span></label>
                            <input type="password" name="password" id="passwordInput" required placeholder="Enter password">
                            <small class="helper-text">Any length is accepted</small>
                        </div>
                    </div>

                    <button type="submit" name="create_account" class="btn-primary">CREATE ACCOUNT</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>