<?php
declare(strict_types=1);
namespace App\Controllers\App\Tickets;
use App\Controllers\BaseController;
use App\Services\Tickets\{TicketAvailability,TicketPdf,PrintLayout};
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;
final class TicketPreview extends BaseController
{
    public function create(int $id): ResponseInterface
    {
        $response=$this->response->setHeader('Cache-Control','no-store, private, max-age=0')->setHeader('X-Robots-Tag','noindex, nofollow')->setHeader('X-Content-Type-Options','nosniff');
        if (!TicketAvailability::enabled()) return $response->setStatusCode(404)->setBody('Página no disponible.');
        $user=auth()->user();
        if (!$user || !$user->inGroup('institutional_admin') || !$user->can('tickets.access') || !$user->can('tickets.print')) return $response->setStatusCode(403)->setBody('Acceso no autorizado.');
        $typeId=filter_var($this->request->getPost('type_id'),FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
        $format=$this->request->getPost('format');
        if (!$typeId || !is_string($format) || !in_array($format,['single','sheet'],true)) return $response->setStatusCode(400)->setBody('Selección inválida.');
        $db=db_connect();
        $event=$db->table('events')->where('id',$id)->get()->getRowArray();
        $type=$db->table('ticket_types')->where(['id'=>$typeId,'event_id'=>$id])->get()->getRowArray();
        if (!$event || !$type) return $response->setStatusCode(404)->setBody('Evento o tipo inexistente.');
        try {
            [,,$count]=PrintLayout::page($format);
            $cards=[];for($i=1;$i<=$count;$i++) $cards[]=['serial'=>$i];
            $pdf=(new TicketPdf())->render($event,$type,$cards,$format,true);
            return $response->setContentType('application/pdf')->setHeader('Content-Disposition','attachment; filename="ARS-muestra-'.$format.'.pdf"')->setBody($pdf);
        } catch(Throwable $e) {
            log_message('error','ARS Tickets PDF preview failed: {class}',['class'=>$e::class]);
            return $response->setStatusCode(503)->setBody('No se pudo generar la muestra. Verifica TCPDF y el escudo oficial.');
        }
    }
}
