<?php
declare(strict_types=1);
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;
final class CreateTicketIssuance extends Migration
{
    public function up(): void
    {
        $id=['type'=>'INT','unsigned'=>true,'auto_increment'=>true];
        $fk=['type'=>'INT','unsigned'=>true];
        $time=['type'=>'DATETIME'];
        $this->forge->addField([
            'id'=>$id,'event_id'=>$fk,'type_id'=>$fk,'issued_by'=>$fk,
            'quantity'=>$fk,'first_serial'=>$fk,'last_serial'=>$fk,
            'request_id'=>['type'=>'CHAR','constraint'=>32],'created_at'=>$time,
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['event_id','issued_by','request_id']);
        $this->forge->addForeignKey('event_id','events','id','RESTRICT','RESTRICT');
        $this->forge->addForeignKey('type_id','ticket_types','id','RESTRICT','RESTRICT');
        $this->forge->addForeignKey('issued_by','users','id','RESTRICT','RESTRICT');
        $this->forge->createTable('ticket_batches',false,['ENGINE'=>'InnoDB']);
        $this->forge->addField([
            'id'=>$id,'event_id'=>$fk,'type_id'=>$fk,'batch_id'=>$fk,'serial_number'=>$fk,
            'token_hash'=>['type'=>'CHAR','constraint'=>64],
            'manual_hash'=>['type'=>'CHAR','constraint'=>64],
            'secret_envelope'=>['type'=>'VARCHAR','constraint'=>512],
            'status'=>['type'=>'VARCHAR','constraint'=>16,'default'=>'CREATED'],
            'created_at'=>$time,
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['event_id','serial_number']);
        $this->forge->addUniqueKey('token_hash');
        $this->forge->addUniqueKey('manual_hash');
        $this->forge->addKey(['event_id','type_id']);
        $this->forge->addKey(['event_id','status']);
        foreach (['event_id'=>'events','type_id'=>'ticket_types','batch_id'=>'ticket_batches'] as $field=>$table) {
            $this->forge->addForeignKey($field,$table,'id','RESTRICT','RESTRICT');
        }
        $this->forge->createTable('tickets',false,['ENGINE'=>'InnoDB']);
        $this->forge->addField([
            'id'=>$id,'ticket_id'=>$fk,'actor_id'=>$fk,
            'from_status'=>['type'=>'VARCHAR','constraint'=>16,'null'=>true],
            'to_status'=>['type'=>'VARCHAR','constraint'=>16],
            'reason'=>['type'=>'VARCHAR','constraint'=>100],'created_at'=>$time,
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('ticket_id','tickets','id','RESTRICT','RESTRICT');
        $this->forge->addForeignKey('actor_id','users','id','RESTRICT','RESTRICT');
        $this->forge->createTable('ticket_status_history',false,['ENGINE'=>'InnoDB']);
    }
    public function down(): void
    {
        foreach (['ticket_status_history','tickets','ticket_batches'] as $table) $this->forge->dropTable($table,true);
    }
}
