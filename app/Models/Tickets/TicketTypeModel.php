<?php
declare(strict_types=1);
namespace App\Models\Tickets;
use CodeIgniter\Model;
final class TicketTypeModel extends Model
{
    protected $table = 'ticket_types';
    protected $allowedFields = ['event_id','name','price','adult_qty','student_qty','quota'];
    protected $returnType = 'array';
}
