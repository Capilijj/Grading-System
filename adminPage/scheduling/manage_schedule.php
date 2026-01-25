<?php
session_start();
require_once '../../Database/database_Connection.php'; 

// 1. Fetch Active Academic Year
try {
    $ayQuery = $conn->query("SELECT ayID, schoolYear, semester FROM AcademicYear WHERE status = 'Active'");
    $activeAY = $ayQuery->fetch(PDO::FETCH_ASSOC);
    $current_ayID = $activeAY ? (int)$activeAY['ayID'] : null;
} catch (PDOException $e) { 
    $current_ayID = null; 
}

// 2. Fetch Dropdowns (ProfessorID as NVARCHAR/String)
try {
    $profs = $conn->query("SELECT professorID, fName, lName FROM dbo.Professor ORDER BY lName ASC")->fetchAll(PDO::FETCH_ASSOC);
    $sections = $conn->query("SELECT sectionID, sectionName FROM dbo.Section")->fetchAll(PDO::FETCH_ASSOC);
    $subjects = $conn->query("SELECT subjectID, subjectCode, subjectName FROM dbo.Subject ORDER BY subjectCode ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $profs = $sections = $subjects = [];
}

// 3. Fetch All Schedules using Stored Procedure
try {
    // Tinitiyak na ang SP ay nagpapatakbo ng JOIN query sa loob ng SQL
    $stmt = $conn->prepare("EXEC dbo.sp_GetAllSchedules ?");
    $stmt->execute([$current_ayID]);
    $all_schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { 
    $all_schedules = []; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class Scheduling - ISCP</title>
    <link rel="stylesheet" href="../sidebar.css">
    <link rel="stylesheet" href="manage_schedule.css">
</head>
<body>
    <?php include '../sidebar.php'; ?>
    <main class="main-content">
        <header class="top-header">
            <div class="header-left">
                <h1>Class Scheduling</h1>
                <p>Academic Year: <b><?= $activeAY ? htmlspecialchars($activeAY['schoolYear'] . " (" . $activeAY['semester'] . ")") : "No Active AY" ?></b></p>
            </div>
        </header>

        <div class="form-grid">
            <div class="glass-card">
                <h3 class="card-title">Deploy New Schedule</h3>
                <form action="save_schedule.php" method="POST">
                    <input type="hidden" name="ayID" value="<?= $current_ayID ?>">
                    
                    <div class="field">
                        <label>PROFESSOR</label>
                        <select name="professorID" required>
                            <option value="">-- Select Professor --</option>
                            <?php foreach($profs as $p): ?>
                                <option value="<?= htmlspecialchars($p['professorID']) ?>">
                                    <?= htmlspecialchars($p['lName'] . ", " . $p['fName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label>COURSE (SUBJECT NAME)</label>
                        <select name="subjectID" required>
                            <option value="">-- Select Course --</option>
                            <?php foreach($subjects as $s): ?>
                                <option value="<?= $s['subjectID'] ?>">
                                    <?= htmlspecialchars($s['subjectCode'] . " - " . $s['subjectName']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label>SECTION</label>
                        <select name="section_id" required>
                            <option value="">-- Select --</option>
                            <?php foreach($sections as $sec): ?>
                                <option value="<?= $sec['sectionID'] ?>"><?= htmlspecialchars($sec['sectionName']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                        <div class="field">
                            <label>DAY</label>
                            <select name="dayOfWeek">
                                <option>Monday</option><option>Tuesday</option>
                                <option>Wednesday</option><option>Thursday</option>
                                <option>Friday</option><option>Saturday</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>ROOM</label>
                            <input type="text" name="room" placeholder="e.g. Rm 101" required>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                        <div class="field"><label>START</label><input type="time" name="startTime" required></div>
                        <div class="field"><label>END</label><input type="time" name="endTime" required></div>
                    </div>

                    <button type="submit" name="deploy_schedule" class="btn-primary" style="width:100%; margin-top:20px;">DEPLOY SCHEDULE</button>
                </form>
            </div>

            <div class="glass-card">
                <div class="field" style="margin-bottom: 20px;">
                    <label>🔍 SEARCH FILTER</label>
                    <input type="text" id="schedSearch" placeholder="Search by ID, Professor, or Course...">
                </div>

                <div class="table-container">
                    <table id="schedTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>PROFESSOR</th>
                                <th>COURSE & SECTION</th>
                                <th class="center-text">SCHEDULE</th>
                                <th class="center-text">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($all_schedules)): foreach($all_schedules as $row): ?>
                            <tr>
                                <td style="font-weight: 800; color: #7f8c8d;">#<?= $row['scheduleID'] ?></td>
                                <td><b class="prof-name"><?= htmlspecialchars($row['lName'] . ", " . $row['fName']) ?></b></td>
                                <td>
                                    <span class="code-badge"><?= htmlspecialchars($row['subjectCode']) ?></span>
                                    <div style="font-weight: 800; font-size: 0.8rem; margin-top:5px; color:#0c225e;">
                                        <?= htmlspecialchars($row['subjectName']) ?>
                                    </div>
                                    <div style="font-size:0.7rem; color:#7f8c8d;">Section: <?= htmlspecialchars($row['sectionName']) ?></div>
                                </td>
                                <td class="center-text">
                                    <div style="font-weight:700; color:#0c225e;"><?= $row['dayOfWeek'] ?></div>
                                    <div style="font-size:0.8rem;">
                                        <?= date("g:i A", strtotime($row['startTime'])) ?> - <?= date("g:i A", strtotime($row['endTime'])) ?>
                                    </div>
                                    <div class="room-tag">📍 <?= htmlspecialchars($row['room']) ?></div>
                                </td>
                                <td class="center-text">
                                    <form action="save_schedule.php" method="POST" onsubmit="return confirm('Delete schedule #<?= $row['scheduleID'] ?>?')">
                                        <input type="hidden" name="schedule_id" value="<?= $row['scheduleID'] ?>">
                                        <button type="submit" name="delete_schedule" style="border:none; background:none; cursor:pointer; font-size:1.2rem;">🗑️</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr><td colspan="5" style="text-align:center; padding: 20px;">No schedules found for this Academic Year.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    <script src="manage_schedule.js"></script>
</body>
</html>