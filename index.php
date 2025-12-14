<?php
// Main entry point for the app. Shows a small chooser and
// redirects to specific pages when requested.

$page = $_GET['page'] ?? '';
if ($page === 'splash') {
	header('Location: login_splash/login_splash.php');
	exit;
}
if ($page === 'login') {
	header('Location: LoginPage/login.php');
	exit;
}

?>
<!doctype html>
<?php
// Immediately redirect to the splash page 
header('Location: login_splash/login_splash.php');
exit;

