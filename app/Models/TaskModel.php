<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model
{
    protected $table            = 'tasks';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = [
        'title', 'description', 'assigned_to', 'assigned_by',
        'priority', 'status', 'progress', 'deadline',
    ];
}