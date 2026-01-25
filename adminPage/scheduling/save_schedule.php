<?php
session_start();
require_once '../../Database/database_Connection.php';

// --- DEPLOY SCHEDULE LOGIC ---
if (isset($_POST['deploy_schedule'])) {
    $ayID = (int)$_POST['ayID'];
    $profID = $_POST['professorID']; 
    $subID = (int)$_POST['subjectID'];
    $secID = (int)$_POST['section_id'];
    $day = $_POST['dayOfWeek'];
    $room = $_POST['room'];
    $start = $_POST['startTime'];
    $end = $_POST['endTime'];

    // Kunin ang schoolYear para sa record assignment
    $ayQuery = $conn->prepare("SELECT schoolYear FROM AcademicYear WHERE ayID = ?");
    $ayQuery->execute([$ayID]);
    $schoolYear = $ayQuery->fetchColumn();

    try {
        $conn->beginTransaction();

        // 1. Check or Insert sa ProfessorSubject (Assignment Table)
        $stmtCheck = $conn->prepare("SELECT profSubID FROM dbo.ProfessorSubject 
                                     WHERE professorID = ? AND subjectID = ? AND sectionID = ? AND ayID = ?");
        $stmtCheck->execute([$profID, $subID, $secID, $ayID]);
        $psRow = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($psRow) {
            $profSubID = $psRow['profSubID'];
        } else {
            // INSERT dahil bagong assignment ito
            $stmtInsert = $conn->prepare("INSERT INTO dbo.ProfessorSubject 
                (professorID, subjectID, sectionID, academicYear, ayID, assignedDate) 
                VALUES (?, ?, ?, ?, ?, GETDATE())");
            $stmtInsert->execute([$profID, $subID, $secID, $schoolYear, $ayID]);
            
            // Pagkuha ng Identity ID pagkatapos ng insert
            $profSubID = $conn->lastInsertId();
            if (!$profSubID) {
                $profSubID = $conn->query("SELECT CAST(SCOPE_IDENTITY() AS INT)")->fetchColumn();
            }
        }

        // Siguraduhin na hindi NULL ang ID bago tumakbo ang Stored Procedure
        if (!$profSubID || $profSubID == 0) {
            throw new Exception("Critical Error: Could not retrieve Assignment ID (profSubID). Ensure column is IDENTITY.");
        }

        // 2. Execute Stored Procedure para sa Schedule entry
        $stmt = $conn->prepare("EXEC dbo.sp_ManageSchedule ?, ?, ?, ?, ?, ?, ?, ?");
        $stmt->execute([$ayID, $profSubID, $subID, $secID, $day, $room, $start, $end]);
        
        $conn->commit();
        echo "<script>alert('Schedule successfully deployed!'); window.location.href='manage_schedule.php';</script>";

    } catch (Exception $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}

// --- DELETE SCHEDULE LOGIC (With Hard Delete for ProfessorSubject) ---
if (isset($_POST['delete_schedule'])) {
    $schedID = (int)$_POST['schedule_id'];
    
    try {
        $conn->beginTransaction();

        // 1. Kunin muna ang profSubID na nakakabit sa schedule na buburahin
        $stmtGet = $conn->prepare("SELECT profSubID FROM dbo.Schedule WHERE scheduleID = ?");
        $stmtGet->execute([$schedID]);
        $profSubID = $stmtGet->fetchColumn();

        // 2. Burahin ang record sa Schedule table
        $stmtDelSched = $conn->prepare("DELETE FROM dbo.Schedule WHERE scheduleID = ?");
        $stmtDelSched->execute([$schedID]);

        // 3. (Optional Cleanup) Burahin ang assignment kung wala na itong ibang schedule na ginagamit
        if ($profSubID) {
            $stmtCheckRemaining = $conn->prepare("SELECT COUNT(*) FROM dbo.Schedule WHERE profSubID = ?");
            $stmtCheckRemaining->execute([$profSubID]);
            $count = $stmtCheckRemaining->fetchColumn();

            if ($count == 0) {
                // Kung wala nang ibang schedules, safe nang burahin ang assignment record
                $stmtDelProfSub = $conn->prepare("DELETE FROM dbo.ProfessorSubject WHERE profSubID = ?");
                $stmtDelProfSub->execute([$profSubID]);
            }
        }

        $conn->commit();
        echo "<script>alert('Schedule and linked assignment cleaned up!'); window.location.href='manage_schedule.php';</script>";

    } catch (PDOException $e) {
        if ($conn->inTransaction()) $conn->rollBack();
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}
?>