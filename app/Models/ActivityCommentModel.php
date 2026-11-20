<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityCommentModel extends Model
{
    protected $table            = 'activity_comments';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $allowedFields    = ['activity_id', 'user_id', 'comment'];
}