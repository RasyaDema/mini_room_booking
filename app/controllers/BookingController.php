<?php
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Room.php';
require_once __DIR__ . '/../middleware/csrf.php';

class BookingController
{
    public function index()
    {
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            $_SESSION['flash']['error'] = 'Please login to view bookings';
            header('Location: ?action=login');
            return;
        }
        // if admin, show all bookings
        $is_admin = (!empty($_SESSION['role_id']) && $_SESSION['role_id'] == 1);
        if ($is_admin) {
            $bookings = Booking::all();
        } else {
            $bookings = Booking::allByUser($user_id);
        }
        require __DIR__ . '/../views/bookings/index.php';
    }

    public function store($data)
    {
        if (!validate_csrf($data['_csrf'] ?? '')) {
            $_SESSION['flash']['error'] = 'Invalid CSRF token.';
            header('Location: ?action=rooms');
            return;
        }
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            $_SESSION['flash']['error'] = 'Please login';
            header('Location: ?action=login');
            return;
        }
        // sanitize minimal
        $room_id = (int)($data['room_id'] ?? 0);
        $start = trim($data['start_time'] ?? '');
        $end = trim($data['end_time'] ?? '');
        if (!$room_id || !$start || !$end) {
            $_SESSION['flash']['error'] = 'All fields are required';
            header('Location: ?action=rooms');
            return;
        }
        // conflict check
        $conflict = Booking::findConflicts($room_id, $start, $end);
        if ($conflict) {
            $_SESSION['flash']['error'] = 'Room already booked for the selected time.';
            header('Location: ?action=rooms');
            return;
        }
        Booking::create([
            'user_id' => $user_id,
            'room_id' => $room_id,
            'start_time' => $start,
            'end_time' => $end,
            'status' => 'pending'
        ]);
        $_SESSION['flash']['success'] = 'Booking created and pending approval.';
        header('Location: ?action=bookings');
    }

    public function adminAction($data)
    {
        // admin only endpoint to approve/reject bookings
        if (!validate_csrf($data['_csrf'] ?? '')) {
            $_SESSION['flash']['error'] = 'Invalid CSRF token.';
            header('Location: ?action=bookings');
            return;
        }
        if (empty($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
            $_SESSION['flash']['error'] = 'Unauthorized';
            header('Location: ?action=bookings');
            return;
        }
        $id = (int)($data['id'] ?? 0);
        $op = ($data['op'] ?? '');
        if (!$id || !in_array($op, ['approve','reject'])) {
            $_SESSION['flash']['error'] = 'Invalid request';
            header('Location: ?action=bookings');
            return;
        }
        $booking = Booking::findById($id);
        if (!$booking) {
            $_SESSION['flash']['error'] = 'Booking not found';
            header('Location: ?action=bookings');
            return;
        }
        if ($op === 'approve') {
            // conflict check before approving
            $conflict = Booking::findConflicts($booking['room_id'], $booking['start_time'], $booking['end_time']);
            if ($conflict) {
                $_SESSION['flash']['error'] = 'Cannot approve: conflicting approved booking exists.';
                header('Location: ?action=bookings');
                return;
            }
            Booking::updateStatus($id, 'approved');
            $_SESSION['flash']['success'] = 'Booking approved.';
        } else {
            Booking::updateStatus($id, 'rejected');
            $_SESSION['flash']['success'] = 'Booking rejected.';
        }
        header('Location: ?action=bookings');
    }
}
