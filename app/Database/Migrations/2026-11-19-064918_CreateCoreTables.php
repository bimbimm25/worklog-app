<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoreTables extends Migration
{
    public function up()
    {
        // 1. Tabel users
        $this->forge->addField([
            'id'            => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'email'         => ['type' => 'VARCHAR', 'constraint' => 150, 'unique' => true],
            'password_hash' => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'          => ['type' => 'ENUM', 'constraint' => ['admin', 'employee'], 'default' => 'employee'],
            'is_active'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'last_login'    => ['type' => 'DATETIME', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('role');
        $this->forge->createTable('users', true, ['ENGINE' => 'InnoDB']);

        // 2. Tabel departments
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'code'        => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('departments', true, ['ENGINE' => 'InnoDB']);

        // 3. Tabel positions
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'description' => ['type' => 'TEXT', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('positions', true, ['ENGINE' => 'InnoDB']);

        // 4. Tabel work_schedules
        $this->forge->addField([
            'id'                     => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'                   => ['type' => 'VARCHAR', 'constraint' => 100],
            'start_time'             => ['type' => 'TIME'],
            'end_time'               => ['type' => 'TIME'],
            'late_tolerance_minutes' => ['type' => 'INT', 'default' => 15],
            'created_at'             => ['type' => 'DATETIME', 'null' => true],
            'updated_at'             => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('work_schedules', true, ['ENGINE' => 'InnoDB']);

        // 5. Tabel employees
        $this->forge->addField([
            'id'               => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'          => ['type' => 'BIGINT', 'unsigned' => true, 'unique' => true],
            'employee_code'    => ['type' => 'VARCHAR', 'constraint' => 30, 'unique' => true],
            'full_name'        => ['type' => 'VARCHAR', 'constraint' => 150],
            'phone'            => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'avatar'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'department_id'    => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'position_id'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'work_schedule_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'join_date'        => ['type' => 'DATE', 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('department_id', 'departments', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('position_id', 'positions', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('work_schedule_id', 'work_schedules', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('employees', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('employees', true);
        $this->forge->dropTable('work_schedules', true);
        $this->forge->dropTable('positions', true);
        $this->forge->dropTable('departments', true);
        $this->forge->dropTable('users', true);
    }
}