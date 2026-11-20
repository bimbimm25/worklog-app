<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkScheduleModel extends Model
{
    protected $table            = 'work_schedules';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = ['name', 'start_time', 'end_time', 'late_tolerance_minutes'];
}