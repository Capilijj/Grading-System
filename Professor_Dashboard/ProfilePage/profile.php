<?php
session_start();
require_once '../../Database/database_Connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Professor') {
    header("Location: ../../Login_FacultyPage/loginFaculty.php");
    exit();
}

$professorID = $_SESSION['professorID'] ?? '';
$message = "";

// --- 1. UPDATE LOGIC (Using sp_UpdateProfessorProfile) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    try {
        $stmt = $conn->prepare("{call sp_UpdateProfessorProfile(?, ?, ?, ?, ?, ?, ?)}");
        $stmt->execute([
            $professorID,
            $_POST['email'],
            $_POST['phone'],
            $_POST['street'],
            $_POST['city'],
            $_POST['country'],
            $_POST['zipcode']
        ]);
        $message = "<div class='alert success'>Profile updated successfully!</div>";
    } catch (Exception $e) {
        $message = "<div class='alert error'>Update failed: " . $e->getMessage() . "</div>";
    }
}

// --- 2. FETCH DATA (Using sp_GetProfessorDetails) ---
try {
    $stmt = $conn->prepare("{call sp_GetProfessorDetails(?)}");
    $stmt->execute([$professorID]);
    $prof = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt->closeCursor(); 
    if (!$prof) { $prof = []; }
} catch (Exception $e) {
    $prof = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Professor Profile - ISCP</title>
    <link rel="stylesheet" href="../Header/ProfessorHeader.css">
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="../../User_Dashboard/Footer/FooterDashboard.css">
</head>
<body>

    <?php include '../Header/ProfessorHeader.php'; ?>

    <main class="profile-page-container">
        <div class="profile-card">
            <div class="profile-header-banner">FACULTY PERSONAL DETAILS</div>
            <div class="profile-content">
                <?php echo $message; ?>
                
                <form method="POST">
                    <div class="form-grid">
                        
                        <div class="form-group">
                            <label>Professor ID</label>
                            <input type="text" value="<?= htmlspecialchars($prof['professorID'] ?? $professorID) ?>" readonly class="readonly-input">
                        </div>

                        <div class="form-group">
                            <label>Sex</label>
                            <input type="text" value="<?= htmlspecialchars($prof['sex'] ?? 'N/A') ?>" readonly class="readonly-input">
                        </div>

                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" value="<?= htmlspecialchars($prof['fname'] ?? '') ?>" readonly class="readonly-input">
                        </div>

                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text" value="<?= htmlspecialchars($prof['mname'] ?? '') ?>" readonly class="readonly-input">
                        </div>

                        <div class="form-group full-width">
                            <label>Last Name</label>
                            <input type="text" value="<?= htmlspecialchars($prof['LName'] ?? '') ?>" readonly class="readonly-input">
                        </div>

                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="text" value="<?= htmlspecialchars($prof['dateOfBirth'] ?? 'N/A') ?>" readonly class="readonly-input">
                        </div>

                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" value="<?= htmlspecialchars($prof['phoneNumber'] ?? '') ?>" class="editable-input">
                        </div>

                        <div class="form-group full-width">
                            <label>Gmail / Email Address</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($prof['email'] ?? '') ?>" class="editable-input">
                        </div>

                        <div class="form-group"><label>Street</label><input type="text" name="street" value="<?= htmlspecialchars($prof['street'] ?? '') ?>" class="editable-input"></div>
                        <div class="form-group"><label>City</label><input type="text" name="city" value="<?= htmlspecialchars($prof['city'] ?? '') ?>" class="editable-input"></div>
                        <div class="form-group"><label>Country</label><input type="text" name="country" value="<?= htmlspecialchars($prof['country'] ?? '') ?>" class="editable-input"></div>
                        <div class="form-group"><label>Zipcode</label><input type="text" name="zipcode" value="<?= htmlspecialchars($prof['zipcode'] ?? '') ?>" class="editable-input"></div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="update_profile" class="btn-save">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include '../../User_Dashboard/Footer/FooterDashboard.php'; ?>
    <script src="profile.js"></script>
</body>
</html>