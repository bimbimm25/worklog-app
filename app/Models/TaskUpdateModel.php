<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskUpdateModel extends Model
{
    protected $table = 'task_updates';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['task_id', 'user_id', 'progress', 'status', 'note'];
}