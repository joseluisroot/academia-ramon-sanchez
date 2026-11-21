<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class AthleteGuardianModel extends Model
{
    protected $table = 'athlete_guardians';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['athlete_id', 'person_id', 'relationship', 'is_primary', 'verified_by', 'verified_at', 'created_at'];
    protected $useTimestamps = false;
}
