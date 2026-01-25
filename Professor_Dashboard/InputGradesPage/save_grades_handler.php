<?php
/**
 * save_grades_handler.php - Saves or updates student grades via Stored Procedure.
 */
session_start();

// 1. Database Connection
$connectionFile = __DIR__ . '/../../Database/database_Connection.php';
if (file_exists($connectionFile)) {
    include $connectionFile;
} else {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database connection file missing.']);
    exit;
}

header('Content-Type: application/json');

// 2. Get JSON data from AJAX request
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Get Professor ID from session
$profID = $_SESSION['professorID'] ?? $_SESSION['user_id'];

// 3. Initial Validation
if (!$data || !is_array($data)) {
    echo json_encode(['status' => 'error', 'message' => 'No valid data received.']);
    exit;
}

if (!$profID) {
    echo json_encode(['status' => 'error', 'message' => 'Session expired. Please login again.']);
    exit;
}

try {
    // Start Transaction to ensure data integrity
    $conn->beginTransaction();

    // Prepare the Stored Procedure call
    // Expects: studentID, professorID, subjectID, gradeValue
    $sql = "{call sp_SaveStudentGrades(?, ?, ?, ?)}";
    $stmt = $conn->prepare($sql);

    $savedCount = 0;

    foreach ($data as $row) {
        $studentID = $row['id'] ?? null;
        $gradeValue = $row['gradeValue'] ?? null;
        // Kuhanin ang subjectID mula sa JS, kung wala, fallback sa default (1)
        $subjectID = isset($row['subjectID']) ? intval($row['subjectID']) : 1;

        if ($studentID && $gradeValue !== null) {
            $stmt->execute([
                $studentID,
                $profID,
                $subjectID,
                $gradeValue
            ]);
            $savedCount++;
        }
    }

    // Commit all changes to the database
    $conn->commit();

    echo json_encode([
        'status' => 'success', 
        'message' => 'Successfully saved ' . $savedCount . ' record(s).'
    ]);

} catch (Exception $e) {
    // Rollback if something goes wrong
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }

    echo json_encode([
        'status' => 'error', 
        'message' => 'Database Error: ' . $e->getMessage()
    ]);
}