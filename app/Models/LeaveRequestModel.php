<?php

namespace App\Models;

use CodeIgniter\Model;

class LeaveRequestModel extends Model
{
    protected $table = "leave_requests";
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = 'true';
    protected $allowedFields = ['employee_id', 'type', 'start_date', 'end_date', 'reason', 'attachment', 'status', 'reviewed_by', 'review_note', ];
}
