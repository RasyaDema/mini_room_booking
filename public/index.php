<?php
// Front controller (simple)
session_start();
require_once __DIR__ . '/../app/config/constants.php';
require_once __DIR__ . '/../app/config/db.php';
require_once __DIR__ . '/../app/middleware/csrf.php';

// simple autoloader for models/controllers
spl_autoload_register(function ($class) {
    $paths = [__DIR__ . '/../app/models/', __DIR__ . '/../app/controllers/'];
    foreach ($paths as $p) {
        $file = $p . $class . '.php';
        if (file_exists($file)) require_once $file;
    }
});

// flash helper
function flash($key = null, $message = null)
{
    if ($key === null) return $_SESSION['flash'] ?? [];
    if ($message === null) {
        $m = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $m;
    }
    $_SESSION['flash'][$key] = $message;
}

$action = $_GET['action'] ?? 'home';

// Simple routing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'login') {
        $ctrl = new AuthController();
        $ctrl->login($_POST);
        exit;
    }
    if ($action === 'logout') {
        $ctrl = new AuthController();
        $ctrl->logout();
        exit;
    }
    if ($action === 'create_booking') {
        $ctrl = new BookingController();
        $ctrl->store($_POST);
        exit;
    }
    if ($action === 'booking_action') {
        $ctrl = new BookingController();
        $ctrl->adminAction($_POST);
        exit;
    }
    if ($action === 'timeslot_create') {
        $ctrl = new TimeslotController();
        $ctrl->store($_POST);
        exit;
    }
    if ($action === 'timeslot_update') {
        $ctrl = new TimeslotController();
        $ctrl->update($_POST);
        exit;
    }
    if ($action === 'timeslot_delete') {
        $ctrl = new TimeslotController();
        $ctrl->delete($_POST);
        exit;
    }
}

// pages
ob_start();
if ($action === 'login') {
    require __DIR__ . '/../app/views/login.php';
} elseif ($action === 'dashboard') {
    require __DIR__ . '/../app/views/dashboard.php';
} elseif ($action === 'rooms') {
    $ctrl = new RoomController();
    $ctrl->index();
} elseif ($action === 'bookings') {
    $ctrl = new BookingController();
    $ctrl->index();
} elseif ($action === 'timeslots') {
    $ctrl = new TimeslotController();
    $ctrl->index();
} else {
    require __DIR__ . '/../app/views/home.php';
}
$content = ob_get_clean();
require __DIR__ . '/../app/views/templates/layout.php';
