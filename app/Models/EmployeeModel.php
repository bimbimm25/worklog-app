<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table            = 'employees';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'user_id',
        'employee_code',
        'full_name',
        'phone',
        'avatar',
        'department_id',
        'position_id',
        'work_schedule_id',
        'join_date',
    ];
}