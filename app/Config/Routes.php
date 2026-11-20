<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'AuthController::login');
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::attemptLogin');
$routes->post('logout', 'AuthController::logout', ['filter' => 'auth']);
$routes->post('notifications/read-all', 'NotificationController::readAll', ['filter' => 'auth']);

// Route Khusus ADMIN
$routes->group('admin', ['filter' => ['auth', 'role:admin']], static function ($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');

    // Master Data: Departemen
    $routes->get('departments', 'Admin\DepartmentController::index');
    $routes->post('departments/store', 'Admin\DepartmentController::store');
    $routes->post('departments/update/(:num)', 'Admin\DepartmentController::update/$1');
    $routes->post('departments/delete/(:num)', 'Admin\DepartmentController::delete/$1');

    // Master Data: Jabatan
    $routes->get('positions', 'Admin\PositionController::index');
    $routes->post('positions/store', 'Admin\PositionController::store');
    $routes->post('positions/update/(:num)', 'Admin\PositionController::update/$1');
    $routes->post('positions/delete/(:num)', 'Admin\PositionController::delete/$1');

    // Master Data: Jadwal Kerja
    $routes->get('schedules', 'Admin\ScheduleController::index');
    $routes->post('schedules/store', 'Admin\ScheduleController::store');
    $routes->post('schedules/update/(:num)', 'Admin\ScheduleController::update/$1');
    $routes->post('schedules/delete/(:num)', 'Admin\ScheduleController::delete/$1');

    // Master Data: Karyawan
    $routes->get('employees', 'Admin\EmployeeController::index');
    $routes->post('employees/store', 'Admin\EmployeeController::store');
    $routes->post('employees/update/(:num)', 'Admin\EmployeeController::update/$1');
    $routes->post('employees/delete/(:num)', 'Admin\EmployeeController::delete/$1');

    // Monitoring Absensi
    $routes->get('attendance', 'Admin\AttendanceController::index');

    // Monitoring Aktivitas & Persetujuan Laporan
    $routes->get('activities', 'Admin\ActivityController::index');
    $routes->get('activities/detail/(:num)', 'Admin\ActivityController::detail/$1');
    $routes->post('activities/approve/(:num)', 'Admin\ActivityController::approve/$1');
    $routes->post('activities/reject/(:num)', 'Admin\ActivityController::reject/$1');
    $routes->post('activities/comment/(:num)', 'Admin\ActivityController::comment/$1');

    // Manajemen Tugas
    $routes->get('tasks', 'Admin\TaskController::index');
    $routes->post('tasks/store', 'Admin\TaskController::store');
    $routes->post('tasks/update/(:num)', 'Admin\TaskController::update/$1');
    $routes->post('tasks/delete/(:num)', 'Admin\TaskController::delete/$1');

    // Izin & Cuti
    $routes->get('leaves', 'Admin\LeaveController::index');
    $routes->post('leaves/review/(:num)', 'Admin\LeaveController::review/$1');

    // Riwayat Aktivitas & Pengaturan
    $routes->get('logs', 'Admin\LogController::index');
    $routes->get('settings', 'Admin\SettingController::index');
    $routes->post('settings/update', 'Admin\SettingController::update');

    // Laporan & Export
    $routes->get('reports', 'Admin\ReportController::index');
    $routes->get('reports/export-pdf', 'Admin\ReportController::exportPdf');
    $routes->get('reports/export-excel', 'Admin\ReportController::exportExcel');
    
});

// Route Khusus KARYAWAN
$routes->group('employee', ['filter' => ['auth', 'role:employee']], static function ($routes) {
    $routes->get('dashboard', 'Employee\DashboardController::index');

    // Absensi Karyawan
    $routes->get('attendance', 'Employee\AttendanceController::index');
    $routes->post('attendance/check-in', 'Employee\AttendanceController::checkIn');
    $routes->post('attendance/check-out', 'Employee\AttendanceController::checkOut');

    // Aktivitas Harian Karyawan
    $routes->get('activities', 'Employee\ActivityController::index');
    $routes->post('activities/store', 'Employee\ActivityController::store');
    $routes->post('activities/update/(:num)', 'Employee\ActivityController::update/$1');
    $routes->post('activities/delete/(:num)', 'Employee\ActivityController::delete/$1');
    $routes->get('activities/detail/(:num)', 'Employee\ActivityController::detail/$1');
    $routes->post('activities/comment/(:num)', 'Employee\ActivityController::comment/$1');

    // Tugas karyawan
    $routes->get('tasks', 'Employee\TaskController::index');
    $routes->post('tasks/update-progress/(:num)', 'Employee\TaskController::updateProgress/$1');

    // Izin & Cuti
    $routes->get('leaves', 'Admin\LeaveController::index');
    $routes->post('leaves/review/(:num)', 'Admin\LeaveController::review/$1');

    // Profil Karyawan
    $routes->get('profile', 'Employee\ProfileController::index');
    $routes->post('profile/update', 'Employee\ProfileController::update');
    $routes->post('profile/password', 'Employee\ProfileController::updatePassword');

});