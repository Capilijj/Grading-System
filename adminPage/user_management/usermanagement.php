<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - ISCP Admin</title>
    <link rel="stylesheet" href="../sidebar.css">
    <link rel="stylesheet" href="usermanagement.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

    <?php include '../sidebar.php'; ?>

    <main class="main-content">
        <header class="top-header">
            <div class="header-left">
                <h1>User Management</h1>
                <p>Manage and monitor system access for students and faculty.</p>
            </div>
            <div class="admin-profile">
                <div class="profile-text">
                    <span class="name">Super Admin</span>
                    <span class="role">System Administrator</span>
                </div>
                <div class="avatar">👤</div>
            </div>
        </header>

        <div class="admin-grid">
            <aside class="form-container">
                <div class="glass-card">
                    <div class="card-title">
                        <h3>Create New Account</h3>
                    </div>
                    <form class="styled-form">
                        <div class="field">
                            <label>ID / STUDENT NUMBER</label>
                            <input type="text" placeholder="Enter ID (e.g. 2023-0001)" required>
                        </div>
                        <div class="field">
                            <label>INITIAL PASSWORD</label>
                            <input type="password" value="password123" required>
                        </div>
                        <div class="field">
                            <label>ASSIGN ROLE</label>
                            <select>
                                <option>Student</option>
                                <option>Faculty</option>
                                <option>Admin</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary">CREATE USER</button>
                    </form>
                </div>
            </aside>

            <section class="table-container">
                <div class="glass-card">
                    <div class="table-header">
                        <div class="header-main">
                            <h3>Recent User Accounts</h3>
                        </div>
                        <div class="search-wrapper">
                            <div class="search-box">
                                <span class="search-icon">🔍</span>
                                <input type="text" id="userSearch" placeholder="Search by ID, Name or Role...">
                            </div>
                            <button class="btn-filter"><span>⚙️</span> Filter</button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th>ID Number</th>
                                    <th>Account Role</th>
                                    <th>Account Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>2023-1001</strong></td>
                                    <td><span class="role-badge">Student</span></td>
                                    <td><span class="status-indicator online">Active</span></td>
                                    <td class="action-cell">
                                        <button class="btn-icon" title="Reset Password">🔑</button>
                                        <button class="btn-icon" title="Manage Account">👤</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>2021-0552</strong></td>
                                    <td><span class="role-badge faculty">Faculty</span></td>
                                    <td><span class="status-indicator offline">Suspended</span></td>
                                    <td class="action-cell">
                                        <button class="btn-icon" title="Reset Password">🔑</button>
                                        <button class="btn-icon" title="Manage Account">👤</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script src="../sidebar.js"></script>
</body>
</html>