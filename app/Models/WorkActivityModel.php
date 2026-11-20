<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkActivityModel extends Model
{
    protected $table            = 'work_activities';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'employee_id', 'title', 'description', 'activity_date',
        'start_time', 'end_time', 'progress', 'work_status',
        'approval_status', 'attachment', 'approved_by',
        'approved_at', 'rejection_reason',
    ];
}