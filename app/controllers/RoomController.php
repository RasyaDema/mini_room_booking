<?php
require_once __DIR__ . '/../models/Room.php';

class RoomController
{
    public function index()
    {
        // read query params
        $q = trim($_GET['q'] ?? '');
        $page = (int)($_GET['page'] ?? 1);
        if ($page < 1) $page = 1;
        $perPage = 9;
        $total = Room::count($q ?: null);
        $totalPages = max(1, (int)ceil($total / $perPage));
        if ($page > $totalPages) $page = $totalPages;
        $rooms = Room::paginate($page, $perPage, $q ?: null);
        require __DIR__ . '/../views/rooms/index.php';
    }
}
