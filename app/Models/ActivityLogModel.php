<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table            = 'activity_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = ''; // Tabel activity_logs tidak memiliki kolom updated_at
    protected $allowedFields    = [
        'user_id',
        'action',
        'module',
        'description',
        'ip_address',
        'user_agent',
    ];
}