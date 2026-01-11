<?php
/**
 * Profile.php
 * UI Prototype para sa Profile Page.
 * * --- INSTRUCTION PARA KAY KEN ---
 * Ken, kapag may database na tayo:
 * 1. Mag-session_start() ka para makuha ang ID ng student.
 * 2. Gamitin ang ID para mag-SELECT sa table ng students.
 * 3. I-assign mo ang results sa variables (halimbawa: $db_name, $db_email).
 * 4. Palitan mo yung mga "HARDCODED" na value sa echo sa baba.
 */

// Placeholders (Ken: Palitan mo ito ng data galing DB query mo soon)
$placeholder_name = "CAPILI, JUSTINE JAMES RAZO";
$placeholder_id = "2023-00075-CM-0";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile - ISCP</title>
    
    <link rel="stylesheet" href="../Header_Dashboard/header.css">
    <link rel="stylesheet" href="profile.css">
    <link rel="stylesheet" href="../Footer_Dashboard/FooterDashboard.css">
</head>
<body>

    <?php include '../Header_Dashboard/header.php'; ?>

    <main class="profile-page-container">
        <div class="white-content-container profile-card">
            
            <div class="profile-header-banner">
                <?php echo $placeholder_name; ?> (<?php echo $placeholder_id; ?>)
            </div>

            <div class="profile-layout">
                <div class="profile-left">
                    <div class="avatar-container">
                        <img src="../../image/default-avatar.png" alt="Profile Picture" id="profileDisplay">
                    </div>
                    <label for="fileInput" class="btn-upload">Change Photo</label>
                    <input type="file" id="fileInput" hidden accept="image/*">
                    <small>Allowed: JPG, PNG</small>
                </div>

                <div class="profile-right">
                    <form action="#" method="POST">
                        <div class="form-grid">
                            
                            <div class="form-group">
                                <label>Student Number</label>
                                <input type="text" value="2023-00075-CM-0" readonly class="readonly-input">
                            </div>
                            
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" value="CAPILI, JUSTINE JAMES RAZO" readonly class="readonly-input">
                            </div>

                            <div class="form-group">
                                <label>Gender</label>
                                <input type="text" name="gender" value="Male" class="editable-input">
                            </div>

                            <div class="form-group">
                                <label>Date of Birth</label>
                                <input type="text" name="dob" value="June 11, 2005" class="editable-input">
                            </div>

                            <div class="form-group">
                                <label>Place of Birth</label>
                                <input type="text" name="pob" value="QUEZON CITY, Philippines" class="editable-input">
                            </div>

                            <div class="form-group">
                                <label>Phone no.</label>
                                <input type="text" name="phone" value="09674533109" class="editable-input">
                            </div>

                            <div class="form-group full-width">
                                <label>Email Address</label>
                                <input type="email" name="email" value="Justinecapili92@gmail.com" class="editable-input">
                            </div>

                            <div class="form-group full-width">
                                <label>Residential Address (Current Stay)</label>
                                <textarea name="address" class="editable-input">#08 duhat Street, Quezon city, Brgy. Commonwealth</textarea>
                            </div>

                            <div class="form-group full-width">
                                <label>Permanent Address</label>
                                <textarea name="perm_address" class="editable-input" placeholder="Enter permanent address if different..."></textarea>
                            </div>

                            <div class="form-group full-width">
                                <label>Name of Spouse (if married)</label>
                                <input type="text" name="spouse" value="N/A" class="editable-input">
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-save">Save Profile Information</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include '../Footer_Dashboard/FooterDashboard.php'; ?>

    <script src="../Header_Dashboard/header.js"></script>
    <script src="profile.js"></script>
</body>
</html>