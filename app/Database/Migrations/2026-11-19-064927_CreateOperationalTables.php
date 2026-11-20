<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOperationalTables extends Migration
{
    public function up()
    {
        // 6. Tabel attendance
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'employee_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'date' => ['type' => 'DATE'],
            'check_in' => ['type' => 'DATETIME', 'null' => true],
            'check_out' => ['type' => 'DATETIME', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['present', 'late', 'absent', 'leave'], 'default' => 'present'],
            'work_duration_minutes' => ['type' => 'INT', 'default' => 0],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['employee_id', 'date']);
        $this->forge->addKey('date');
        $this->forge->addForeignKey('employee_id', 'employees', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('attendance', true, ['ENGINE' => 'InnoDB']);

        // 7. Tabel work_activities
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'employee_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 200],
            'description' => ['type' => 'TEXT'],
            'activity_date' => ['type' => 'DATE'],
            'start_time' => ['type' => 'TIME'],
            'end_time' => ['type' => 'TIME'],
            'progress' => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 0],
            'work_status' => ['type' => 'ENUM', 'constraint' => ['in_progress', 'completed', 'blocked'], 'default' => 'in_progress'],
            'approval_status' => ['type' => 'ENUM', 'constraint' => ['draft', 'pending', 'approved', 'rejected'], 'default' => 'draft'],
            'attachment' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'approved_by' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'approved_at' => ['type' => 'DATETIME', 'null' => true],
            'rejection_reason' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('activity_date');
        $this->forge->addKey('approval_status');
        $this->forge->addForeignKey('employee_id', 'employees', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('approved_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('work_activities', true, ['ENGINE' => 'InnoDB']);

        // 8. Tabel activity_comments
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'activity_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'comment' => ['type' => 'TEXT'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('activity_id', 'work_activities', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('activity_comments', true, ['ENGINE' => 'InnoDB']);

        // 9. Tabel tasks
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'title' => ['type' => 'VARCHAR', 'constraint' => 200],
            'description' => ['type' => 'TEXT', 'null' => true],
            'assigned_to' => ['type' => 'BIGINT', 'unsigned' => true],
            'assigned_by' => ['type' => 'BIGINT', 'unsigned' => true],
            'priority' => ['type' => 'ENUM', 'constraint' => ['low', 'medium', 'high', 'urgent'], 'default' => 'medium'],
            'status' => ['type' => 'ENUM', 'constraint' => ['todo', 'in_progress', 'review', 'completed'], 'default' => 'todo'],
            'progress' => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 0],
            'deadline' => ['type' => 'DATE'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addKey('deadline');
        $this->forge->addForeignKey('assigned_to', 'employees', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('assigned_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('tasks', true, ['ENGINE' => 'InnoDB']);

        // 10. Tabel task_updates
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'task_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'user_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'progress' => ['type' => 'TINYINT', 'unsigned' => true],
            'status' => ['type' => 'VARCHAR', 'constraint' => 30],
            'note' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('task_id', 'tasks', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('task_updates', true, ['ENGINE' => 'InnoDB']);

        // 11. Tabel leave_requests
        $this->forge->addField([
            'id' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'employee_id' => ['type' => 'BIGINT', 'unsigned' => true],
            'type' => ['type' => 'ENUM', 'constraint' => ['izin', 'sakit', 'cuti']],
            'start_date' => ['type' => 'DATE'],
            'end_date' => ['type' => 'DATE'],
            'reason' => ['type' => 'TEXT'],
            'attachment' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'rejected'], 'default' => 'pending'],
            'reviewed_by' => ['type' => 'BIGINT', 'unsigned' => true, 'null' => true],
            'review_note' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('status');
        $this->forge->addKey('start_date');
        $this->forge->addForeignKey('employee_id', 'employees', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('reviewed_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('leave_requests', true, ['ENGINE' => 'InnoDB']);
    }

    public function down()
    {
        $this->forge->dropTable('leave_requests', true);
        $this->forge->dropTable('task_updates', true);
        $this->forge->dropTable('tasks', true);
        $this->forge->dropTable('activity_comments', true);
        $this->forge->dropTable('work_activities', true);
        $this->forge->dropTable('attendance', true);
    }
}