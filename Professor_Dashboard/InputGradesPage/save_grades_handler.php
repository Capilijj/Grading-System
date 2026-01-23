<?php
session_start();
require_once '../../Database/database_Connection.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$profID = $_SESSION['professorID'] ?? $_SESSION['user_id'];

if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'No data received']);
    exit;
}

try {
    $conn->beginTransaction();
    foreach ($data as $grade) {
        $stmt = $conn->prepare("{call sp_SaveStudentGrades(?, ?, ?, ?, ?)}");
        $stmt->execute([
            $grade['id'],
            $profID,
            $grade['prelim'],
            $grade['midterm'],
            $grade['finals']
        ]);
    }
    $conn->commit();
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}