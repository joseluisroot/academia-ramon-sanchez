<?php
declare(strict_types=1);
namespace App\Models\Tickets;
use CodeIgniter\Model;
final class EventModel extends Model
{
    protected $table = 'events';
    protected $allowedFields = ['slug','name','venue','starts_at','access_from','access_to','capacity','status','print_template','created_at','updated_at'];
    protected $returnType = 'array';
}
