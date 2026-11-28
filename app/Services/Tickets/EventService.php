<?php
declare(strict_types=1);
namespace App\Services\Tickets;

use CodeIgniter\Database\BaseConnection;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class EventService
{
    public function __construct(private BaseConnection $db) {}

    private function transaction(callable $work): mixed
    {
        $this->db->transBegin();
        try {
            $result = $work();
            if (!$this->db->transStatus() || !$this->db->transCommit()) {
                throw new RuntimeException('No se pudo guardar.');
            }
            return $result;
        } catch (Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    private function locked(int $id): array
    {
        $table = $this->db->protectIdentifiers($this->db->prefixTable('events'));
        $row = $this->db->query("SELECT * FROM {$table} WHERE id = ? FOR UPDATE", [$id])->getRowArray();
        if (!$row) throw new InvalidArgumentException('Evento inexistente.');
        return $row;
    }

    private function audit(int $actor, int $id, string $action): void
    {
        if (!$this->db->table('audit_logs')->insert([
            'actor_user_id'=>$actor,'action'=>$action,'subject_type'=>'ticket_event',
            'subject_id'=>(string)$id,'metadata_json'=>'{}','created_at'=>gmdate('Y-m-d H:i:s'),
        ])) throw new RuntimeException('No se pudo auditar.');
    }

    private function allocated(int $id): int
    {
        $row=$this->db->table('ticket_types')->select('SUM(quota * (adult_qty + student_qty)) AS seats',false)->where('event_id',$id)->get()->getRowArray();
        return (int)($row['seats'] ?? 0);
    }

    public function save(?int $id, array $data, int $actor): int
    {
        return $this->transaction(function () use ($id,$data,$actor) {
            $data['updated_at']=gmdate('Y-m-d H:i:s');
            if ($id===null) {
                $data['status']='DRAFT';
                $data['created_at']=$data['updated_at'];
                if (!$this->db->table('events')->insert($data)) throw new RuntimeException('No se pudo crear.');
                $id=(int)$this->db->insertID();
            } else {
                $event=$this->locked($id);
                if ($event['status']!=='DRAFT' || $this->allocated($id)>$data['capacity']) throw new InvalidArgumentException('Solo se editan borradores y la capacidad debe cubrir las cuotas.');
                if (!$this->db->table('events')->where('id',$id)->update($data)) throw new RuntimeException('No se pudo actualizar.');
            }
            $this->audit($actor,$id,'tickets.event.save');
            return $id;
        });
    }

    public function addType(int $id,array $data,int $actor): void
    {
        $this->transaction(function () use ($id,$data,$actor) {
            $event=$this->locked($id);
            $seats=EventPolicy::seats($data['quota'],$data['adult_qty'],$data['student_qty']);
            if ($event['status']!=='DRAFT' || $this->allocated($id)+$seats>(int)$event['capacity']) throw new InvalidArgumentException('Solo se configuran borradores; la cuota supera la capacidad o el evento no está en borrador.');
            $data['event_id']=$id;
            if (!$this->db->table('ticket_types')->insert($data)) throw new RuntimeException('No se pudo crear el tipo.');
            $this->audit($actor,$id,'tickets.type.create');
        });
    }

    public function changeStatus(int $id,string $to,int $actor): void
    {
        $this->transaction(function () use ($id,$to,$actor) {
            $event=$this->locked($id);
            if (!EventPolicy::transition($event['status'],$to) || ($to==='OPEN' && $this->allocated($id)<1)) throw new InvalidArgumentException('Transición no permitida o sin tipos de entrada.');
            if (!$this->db->table('events')->where('id',$id)->update(['status'=>$to,'updated_at'=>gmdate('Y-m-d H:i:s')])) throw new RuntimeException('No se pudo cambiar el estado.');
            $this->audit($actor,$id,'tickets.event.status.'.$to);
        });
    }
}
