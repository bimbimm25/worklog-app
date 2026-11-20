<?php

namespace App\Models;
use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'email',
        'passsword_hash',
        'role',
        'is_active',
        'last_login'
    ];  

    public function findByEmailWithEmployee(string $email): ?array
    {
        return $this->select('users.*, employees.id as employee_id, employees.employee_code, employees.full_name, employees.avatar, employees.department_id, employees.position_id, employees.work_schedule_id')
        ->join('employees', 'employees.user_id = users.id', 'left')
        ->where('users.email', $email)
        ->first();
    }
}
?>