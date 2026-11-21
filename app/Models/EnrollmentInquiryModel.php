<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

final class EnrollmentInquiryModel extends Model
{
    protected $table = 'enrollment_inquiries';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'contact_name', 'email', 'phone', 'category', 'message',
        'status', 'consented_at', 'created_at', 'updated_at',
        'contacted_by', 'contacted_at',
    ];
    protected $useTimestamps = false;
}
