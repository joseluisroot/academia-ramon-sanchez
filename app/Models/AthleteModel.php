<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class AthleteModel extends Model
{
    protected $table = 'athletes';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['first_name', 'last_name', 'birth_date', 'joined_on', 'sports_category_id', 'status', 'created_by', 'created_at', 'updated_at'];
    protected $useTimestamps = false;
}
