<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class SportsCategoryModel extends Model
{
    protected $table = 'sports_categories';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['code', 'name', 'age_min', 'age_max', 'active'];
    protected $useTimestamps = false;
}
