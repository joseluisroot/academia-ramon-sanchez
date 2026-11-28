<?php
declare(strict_types=1);
namespace App\Services\Tickets;
use CodeIgniter\Database\BaseConnection;
use InvalidArgumentException;
use RuntimeException;
use Throwable;
final class IssuanceService
{
    public function __construct(private BaseConnection $db,private TicketSecrets $secrets) {}
    public function issue(int $eventId,int $typeId,int $quantity,int $actor,string $requestId): int
    {
        if (!preg_match('/^[a-f0-9]{32}$/D',$requestId)) throw new InvalidArgumentException('Solicitud inválida.');
        if (!$this->db->transBegin()) throw new RuntimeException('No se pudo iniciar la transacción.');
        try {
            $table=$this->db->protectIdentifiers($this->db->prefixTable('events'));
            $event=$this->db->query("SELECT * FROM {$table} WHERE id=? FOR UPDATE",[$eventId])->getRowArray();
            if (!$event) throw new InvalidArgumentException('Evento inexistente.');
            $previous=$this->db->table('ticket_batches')->where(['event_id'=>$eventId,'issued_by'=>$actor,'request_id'=>$requestId])->get()->getRowArray();
            if ($previous) {
                if ((int)$previous['type_id']!==$typeId || (int)$previous['quantity']!==$quantity) throw new InvalidArgumentException('Solicitud repetida con datos distintos.');
                $batch=(int)$previous['id'];
            } else {
                $type=$this->db->table('ticket_types')->where(['id'=>$typeId,'event_id'=>$eventId])->get()->getRowArray();
                if (!$type) throw new InvalidArgumentException('Tipo de entrada inexistente en este evento.');
                $issued=$this->db->table('tickets')->where(['event_id'=>$eventId,'type_id'=>$typeId])->countAllResults();
                IssuancePolicy::assertAllowed($event['status'],(int)$type['quota'],$issued,$quantity);
                $max=$this->db->table('tickets')->selectMax('serial_number','last_serial')->where('event_id',$eventId)->get()->getRowArray();
                $first=(int)($max['last_serial']??0)+1;$last=$first+$quantity-1;
                $now=gmdate('Y-m-d H:i:s');
                $this->insert('ticket_batches',['event_id'=>$eventId,'type_id'=>$typeId,'issued_by'=>$actor,'quantity'=>$quantity,'first_serial'=>$first,'last_serial'=>$last,'request_id'=>$requestId,'created_at'=>$now]);
                $batch=(int)$this->db->insertID();
                for ($serial=$first;$serial<=$last;$serial++) {
                    $secret=$this->secrets->generate(IssuancePolicy::context($eventId,$typeId,$batch,$serial));
                    $this->insert('tickets',['event_id'=>$eventId,'type_id'=>$typeId,'batch_id'=>$batch,'serial_number'=>$serial,'status'=>'CREATED','created_at'=>$now]+$secret);
                    $ticket=(int)$this->db->insertID();
                    $this->insert('ticket_status_history',['ticket_id'=>$ticket,'actor_id'=>$actor,'from_status'=>null,'to_status'=>'CREATED','reason'=>'ISSUED','created_at'=>$now]);
                }
                $this->insert('audit_logs',['actor_user_id'=>$actor,'action'=>'tickets.batch.issue','subject_type'=>'ticket_batch','subject_id'=>(string)$batch,'metadata_json'=>'{}','created_at'=>$now]);
            }
            if (!$this->db->transStatus() || !$this->db->transCommit()) throw new RuntimeException('No se pudo confirmar la emisión.');
            return $batch;
        } catch (Throwable $e) {
            $this->db->transRollback();throw $e;
        }
    }
    private function insert(string $table,array $data): void
    {
        if (!$this->db->table($table)->insert($data)) throw new RuntimeException('No se pudo registrar la emisión.');
    }
}
