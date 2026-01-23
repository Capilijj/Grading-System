<?php
session_start();

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check if user is logged in
if (!isset($_SESSION['role'])) {
    header("Location: ../../Login_FacultyPage/loginFaculty.php");
    exit();
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
                <p>Manual updates for school years, semesters, and enrollment status.</p>
            </div>
        </header>

        <section class="form-container">
            <div class="glass-card">
                <div class="card-header">
                    <h3>➕ Setup New School Year</h3>
                </div>
                <form class="manual-form">
                    <div class="form-grid">
                        <div class="input-field">
                            <label>Start Year</label>
                            <input type="number" value="2024" required>
                        </div>
                        <div class="input-field">
                            <label>End Year</label>
                            <input type="number" value="2025" required>
                        </div>
                        <div class="input-field">
                            <label>Default Semester</label>
                            <select>
                                <option>1st Semester</option>
                                <option>2nd Semester</option>
                                <option>Summer</option>
                            </select>
                        </div>
                        <div class="input-field">
                            <label>Enrollment Status</label>
                            <select>
                                <option>Open</option>
                                <option>Closed</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-footer">
                        <button type="submit" class="btn-update-system">Update System Records</button>
                    </div>
                </form>
            </div>
        </section>

        <section class="table-container">
            <div class="glass-card">
                <div class="card-header flex-header">
                    <h3>Academic Year History</h3>
                    <div class="search-box">
                        <input type="text" placeholder="Search S.Y...">
                    </div>
                </div>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>School Year</th>
                                <th>Status</th>
                                <th>Total Students</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>S.Y. 2023 - 2024</strong></td>
                                <td><span class="status-badge status-active">CURRENTLY ACTIVE</span></td>
                                <td>4,250</td>
                                <td class="action-btns">
                                    <button class="btn-icon">⚙️</button>
                                    <button class="btn-icon">📂</button>
                                </td>
                            </tr>
                            <tr>
                                <td>S.Y. 2022 - 2023</td>
                                <td><span class="status-badge status-archived">ARCHIVED</span></td>
                                <td>3,890</td>
                                <td class="action-btns">
                                    <button class="btn-icon">⚙️</button>
                                    <button class="btn-icon">👁️</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

    <script src="../sidebar.js"></script>
</body>
</html>