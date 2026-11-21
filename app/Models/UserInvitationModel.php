<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class UserInvitationModel extends Model
{
    protected $table = 'user_invitations';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'email', 'role', 'token_hash', 'status',
        'invited_by', 'created_at', 'expires_at', 'consumed_at',
    ];
    protected $useTimestamps = false;
    protected $protectFields = true;
}
