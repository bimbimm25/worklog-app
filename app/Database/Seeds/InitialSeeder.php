<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // 1. Departments
        $this->db->table('departments')->insertBatch([
            ['name' => 'Information Technology', 'code' => 'IT', 'description' => 'Pengembangan dan infrastruktur sistem', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Human Resources', 'code' => 'HR', 'description' => 'Pengelolaan SDM dan operasional karyawan', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Finance & Accounting', 'code' => 'FIN', 'description' => 'Keuangan dan pembukuan perusahaan', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 2. Positions
        $this->db->table('positions')->insertBatch([
            ['name' => 'Software Engineer', 'description' => 'Pengembang aplikasi web dan backend', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'HR Specialist', 'description' => 'Staf rekrutmen dan administrasi karyawan', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Finance Staff', 'description' => 'Staf administrasi keuangan', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 3. Work Schedules
        $this->db->table('work_schedules')->insertBatch([
            ['name' => 'Regular Office (08:00 - 17:00)', 'start_time' => '08:00:00', 'end_time' => '17:00:00', 'late_tolerance_minutes' => 15, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Flexible Tech (09:00 - 18:00)', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'late_tolerance_minutes' => 15, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 4. Users (Admin & 1 Karyawan untuk testing)
        $this->db->table('users')->insert([
            'email'         => 'admin@worklog.com',
            'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
            'role'          => 'admin',
            'is_active'     => 1,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);
        $adminId = $this->db->insertID();

        $this->db->table('users')->insert([
            'email'         => 'karyawan@worklog.com',
            'password_hash' => password_hash('karyawan123', PASSWORD_DEFAULT),
            'role'          => 'employee',
            'is_active'     => 1,
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);
        $empUserId = $this->db->insertID();

        // 5. Employees Profile
        $this->db->table('employees')->insertBatch([
            [
                'user_id'          => $adminId,
                'employee_code'    => 'EMP-0001',
                'full_name'        => 'Administrator WORKLOG',
                'phone'            => '081200000001',
                'department_id'    => 2,
                'position_id'      => 2,
                'work_schedule_id' => 1,
                'join_date'        => date('Y-m-d'),
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'user_id'          => $empUserId,
                'employee_code'    => 'EMP-0002',
                'full_name'        => 'Budi Santoso',
                'phone'            => '081200000002',
                'department_id'    => 1,
                'position_id'      => 1,
                'work_schedule_id' => 1,
                'join_date'        => date('Y-m-d'),
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
        ]);

        // 6. System Settings
        $this->db->table('settings')->insertBatch([
            ['setting_key' => 'company_name', 'setting_value' => 'PT Worklog Indonesia', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'company_address', 'setting_value' => 'Jl. Teknologi No. 10, Surabaya', 'created_at' => $now, 'updated_at' => $now],
            ['setting_key' => 'default_late_tolerance', 'setting_value' => '15', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}