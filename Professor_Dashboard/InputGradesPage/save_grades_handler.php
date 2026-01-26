<?php
/**
 * save_grades_handler.php - UPDATED VERSION
 * Fixed: Added Remarks logic and ayID passing to prevent NULL values
 */

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Enable error logging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Log file for debugging
$logFile = __DIR__ . '/grade_save_debug.log';

function debugLog($message) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

debugLog("=== NEW SAVE REQUEST ===");

// Database Connection
$connectionFile = __DIR__ . '/../../Database/database_Connection.php';
if (file_exists($connectionFile)) {
    require_once $connectionFile;
} else {
    debugLog("ERROR: Database connection file missing");
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Database connection file missing.']);
    exit;
}

header('Content-Type: application/json');

// Get JSON data
$input = file_get_contents('php://input');
debugLog("Raw Input: " . $input);

$data = json_decode($input, true);
debugLog("Decoded Data: " . print_r($data, true));

// Get Professor ID and ayID from session
$profID = null;
if (isset($_SESSION['professorID']) && !empty($_SESSION['professorID'])) {
    $profID = trim($_SESSION['professorID']);
} elseif (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $profID = trim($_SESSION['user_id']);
}

// Get Academic Year ID (ayID) from session, default to 1 if not set
$ayID = $_SESSION['ayID'] ?? 1; 

debugLog("Session Data: ProfessorID=$profID, ayID=$ayID");

// Validation
if (!$data || !is_array($data)) {
    debugLog("ERROR: Invalid data format");
    echo json_encode([
        'status' => 'error', 
        'message' => 'Invalid data format received.'
    ]);
    exit;
}

if (!$profID) {
    debugLog("ERROR: No professor ID in session");
    echo json_encode([
        'status' => 'error', 
        'message' => 'Session expired. Please login again.'
    ]);
    exit;
}

try {
    // Start Transaction
    $conn->beginTransaction();
    debugLog("Transaction started");

    // UPDATED: Prepare stored procedure with 6 parameters
    // Format: StudentID, ProfID, SubjectID, GradeValue, ayID, Remarks
    $sql = "{call sp_SaveStudentGrades(?, ?, ?, ?, ?, ?)}";
    $stmt = $conn->prepare($sql);

    $savedCount = 0;
    $errors = [];

    foreach ($data as $index => $row) {
        $studentID = isset($row['id']) ? trim($row['id']) : null;
        $gradeValue = isset($row['gradeValue']) ? strtoupper(trim($row['gradeValue'])) : null;
        $subjectID = isset($row['subjectID']) ? intval($row['subjectID']) : null;

        // --- NEW: REMARKS LOGIC ---
        $remarks = "";
        if (is_numeric($gradeValue)) {
            $numGrade = floatval($gradeValue);
            // logic: 1.0 to 3.0 is Passed, above 3.0 is Failed
            if ($numGrade >= 1.0 && $numGrade <= 3.0) {
                $remarks = "PASSED";
            } else {
                $remarks = "FAILED";
            }
        } elseif ($gradeValue === "INC") {
            $remarks = "INCOMPLETE";
        } elseif ($gradeValue === "W" || $gradeValue === "DROP") {
            $remarks = "DROPPED";
        }

        if ($studentID && $gradeValue !== null && $subjectID) {
            try {
                debugLog("Executing SP with: [$studentID, $profID, $subjectID, $gradeValue, $ayID, $remarks]");
                
                // I-execute gamit ang 6 na parameters
                $stmt->execute([
                    $studentID,
                    $profID,
                    $subjectID,
                    $gradeValue,
                    $ayID,
                    $remarks
                ]);
                
                $savedCount++;
                debugLog("SUCCESS Row $index: Saved");
                
            } catch (Exception $rowError) {
                $errorMsg = $rowError->getMessage();
                $errors[] = "Student $studentID: " . $errorMsg;
                debugLog("ERROR Row $index: " . $errorMsg);
            }
        }
    }

    // Commit
    $conn->commit();
    debugLog("Transaction committed. Saved count: $savedCount");

    echo json_encode([
        'status' => 'success',
        'message' => "Successfully saved $savedCount grade(s).",
        'saved_count' => $savedCount
    ]);

} catch (Exception $e) {
    if ($conn->inTransaction()) { $conn->rollBack(); }
    debugLog("FATAL ERROR: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

debugLog("=== REQUEST COMPLETE ===\n");