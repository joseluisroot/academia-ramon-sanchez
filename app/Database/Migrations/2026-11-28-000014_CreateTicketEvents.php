<?php
declare(strict_types=1);
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

final class CreateTicketEvents extends Migration
{
    public function up(): void
    {
        $id = ['type'=>'INT','unsigned'=>true,'auto_increment'=>true];
        $fk = ['type'=>'INT','unsigned'=>true];
        $this->forge->addField([
            'id'=>$id,'slug'=>['type'=>'VARCHAR','constraint'=>100],
            'name'=>['type'=>'VARCHAR','constraint'=>150],
            'venue'=>['type'=>'VARCHAR','constraint'=>250],
            'starts_at'=>['type'=>'DATETIME'],'access_from'=>['type'=>'DATETIME'],'access_to'=>['type'=>'DATETIME'],
            'capacity'=>$fk,'status'=>['type'=>'VARCHAR','constraint'=>16,'default'=>'DRAFT'],
            'print_template'=>['type'=>'VARCHAR','constraint'=>40,'default'=>'gala-economy-v1'],
            'created_at'=>['type'=>'DATETIME'],'updated_at'=>['type'=>'DATETIME'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('events', false, ['ENGINE'=>'InnoDB']);
        $this->forge->addField([
            'id'=>$id,'event_id'=>$fk,'name'=>['type'=>'VARCHAR','constraint'=>100],
            'price'=>['type'=>'DECIMAL','constraint'=>'12,2'],
            'adult_qty'=>['type'=>'INT','unsigned'=>true],
            'student_qty'=>['type'=>'INT','unsigned'=>true],
            'quota'=>$fk,
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['event_id','name']);
        $this->forge->addForeignKey('event_id','events','id','RESTRICT','RESTRICT');
        $this->forge->createTable('ticket_types', false, ['ENGINE'=>'InnoDB']);
        $this->forge->addField([
            'id'=>$id,'event_id'=>$fk,'user_id'=>$fk,
            'capability'=>['type'=>'VARCHAR','constraint'=>50],
            'revoked_at'=>['type'=>'DATETIME','null'=>true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['event_id','user_id','capability']);
        $this->forge->addForeignKey('event_id','events','id','RESTRICT','RESTRICT');
        $this->forge->addForeignKey('user_id','users','id','RESTRICT','RESTRICT');
        $this->forge->createTable('event_users', false, ['ENGINE'=>'InnoDB']);
        $this->forge->addField([
            'id'=>$id,'event_id'=>$fk,'name'=>['type'=>'VARCHAR','constraint'=>100],
            'active'=>['type'=>'TINYINT','constraint'=>1,'default'=>1],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['event_id','name']);
        $this->forge->addForeignKey('event_id','events','id','RESTRICT','RESTRICT');
        $this->forge->createTable('event_access_points', false, ['ENGINE'=>'InnoDB']);
    }

    public function down(): void
    {
        foreach (['event_access_points','event_users','ticket_types','events'] as $table) {
            $this->forge->dropTable($table, true);
        }
    }
}
