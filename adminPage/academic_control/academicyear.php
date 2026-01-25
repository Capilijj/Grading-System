<?php
/**
 * Academic_Control/academicyear.php
 * Updated: fixed overwrite logic to support multiple semesters per school year.
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

$message = "";

// --- 1. UPDATE LOGIC (SETTING ACTIVE SEMESTER) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_academic'])) {
    try {
        $sy = $_POST['start_year'] . " - " . $_POST['end_year'];
        $semester = $_POST['semester'];
        $enrollStatus = $_POST['enrollment_status'];

        // STEP 1: I-set lahat ng records sa 'Archived' para isa lang ang Active sa buong system
        $conn->prepare("UPDATE AcademicYear SET status = 'Archived'")->execute();
        
        // STEP 2: I-check kung existing na ang eksaktong kombinasyon ng S.Y. AT Semester
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM AcademicYear WHERE schoolYear = ? AND semester = ?");
        $checkStmt->execute([$sy, $semester]);
        $exists = $checkStmt->fetchColumn();

        if ($exists) {
            // Kung may record na para sa Semestral cycle na ito, i-activate lang at i-update ang enrollment
            $stmt = $conn->prepare("UPDATE AcademicYear SET status = 'Active', enrollmentStatus = ? WHERE schoolYear = ? AND semester = ?");
            $stmt->execute([$enrollStatus, $sy, $semester]);
        } else {
            // Kung bago ang semester para sa taong ito, gagawa ng bagong row (magkakaroon ng sariling ayID)
            $stmt = $conn->prepare("INSERT INTO AcademicYear (schoolYear, semester, status, enrollmentStatus) VALUES (?, ?, 'Active', ?)");
            $stmt->execute([$sy, $semester, $enrollStatus]);
        }
        
        $message = "✅ System Updated: S.Y. $sy ($semester) is now ACTIVE.";
    } catch (PDOException $e) {
        $message = "❌ Database Error: " . $e->getMessage();
    }
}

// --- 2. FETCH HISTORY ---
$history = [];
try {
    $stmt = $conn->query("SELECT * FROM AcademicYear ORDER BY ayID DESC");
    $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "❌ Error fetching records: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Control - ISCP Admin</title>
    <link rel="stylesheet" href="../sidebar.css">
    <link rel="stylesheet" href="academicyear.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

    <?php include '../sidebar.php'; ?>

    <main class="main-content">
        <header class="top-header">
            <div class="header-left">
                <h1>Academic Year Management</h1>
                <p>Update the current school year and semester for the entire system.</p>
            </div>
        </header>

        <?php if($message): ?>
            <div class="alert-box" style="margin-bottom: 20px; padding: 15px; background: white; border-left: 5px solid #0c225e; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <b>Notice:</b> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <section class="form-container">
            <div class="glass-card">
                <div class="card-header">
                    <h3>➕ Setup Academic Cycle</h3>
                </div>
                <form method="POST" class="manual-form">
                    <div class="form-grid">
                        <div class="input-field">
                            <label>Start Year</label>
                            <input type="number" name="start_year" value="<?= date('Y') ?>" required>
                        </div>
                        <div class="input-field">
                            <label>End Year</label>
                            <input type="number" name="end_year" value="<?= date('Y') + 1 ?>" required>
                        </div>
                        <div class="input-field">
                            <label>Semester</label>
                            <select name="semester">
                                <option value="1st Semester">1st Semester</option>
                                <option value="2nd Semester">2nd Semester</option>
                                <option value="Summer">Summer</option>
                            </select>
                        </div>
                        <div class="input-field">
                            <label>Enrollment Status</label>
                            <select name="enrollment_status">
                                <option value="Open">Open</option>
                                <option value="Closed">Closed</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-footer">
                        <button type="submit" name="update_academic" class="btn-update-system">Update System Records</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="table-container">
            <div class="glass-card">
                <div class="card-header flex-header">
                    <h3>Academic Year History</h3>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>School Year</th>
                                <th>Semester</th>
                                <th>Status</th>
                                <th>Enrollment</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($history)): ?>
                                <?php foreach($history as $row): ?>
                                <tr>
                                    <td><strong>S.Y. <?= htmlspecialchars($row['schoolYear']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['semester']) ?></td>
                                    <td>
                                        <span class="status-badge <?= strtolower($row['status']) == 'active' ? 'status-active' : 'status-archived' ?>">
                                            <?= strtoupper($row['status']) ?>
                                        </span>
                                    </td>
                                    <td><b><?= htmlspecialchars($row['enrollmentStatus']) ?></b></td>
                                    <td class="action-btns">
                                        <button class="btn-icon" title="View Records">👁️</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" style="text-align:center;">No records found in table 'AcademicYear'.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <script src="../sidebar.js"></script>
    <script src="academicyear.js"></script>
</body>
</html>