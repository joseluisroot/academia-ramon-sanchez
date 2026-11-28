<?php
declare(strict_types=1);
namespace App\Controllers\App\Tickets;
use App\Controllers\BaseController;
use App\Services\Tickets\{TicketAvailability,TicketSecrets,IssuanceService,IssuancePolicy};
use CodeIgniter\HTTP\ResponseInterface;
use InvalidArgumentException;
use Throwable;
final class Issuance extends BaseController
{
    private function guard(): ?ResponseInterface
    {
        $this->response->setHeader('Cache-Control','no-store, private, max-age=0')->setHeader('X-Robots-Tag','noindex, nofollow');
        if (!TicketAvailability::enabled()) return $this->response->setStatusCode(404)->setBody('Página no disponible.');
        $user=auth()->user();
        if (!$user || !$user->inGroup('institutional_admin') || !$user->can('tickets.access') || !$user->can('tickets.issue')) return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        return null;
    }
    public function index(int $id): ResponseInterface
    {
        if ($denied=$this->guard()) return $denied;
        $db=db_connect();
        $event=$db->table('events')->where('id',$id)->get()->getRowArray();
        if (!$event) return $this->response->setStatusCode(404)->setBody('Evento inexistente.');
        $types=$db->table('ticket_types')->where('event_id',$id)->get()->getResultArray();
        foreach ($types as &$type) {
            $type['issued']=$db->table('tickets')->where(['event_id'=>$id,'type_id'=>$type['id']])->countAllResults();
            $type['available']=(int)$type['quota']-$type['issued'];
        }
        unset($type);
        $batches=$db->table('ticket_batches')->select('ticket_batches.id, ticket_batches.quantity, ticket_batches.first_serial, ticket_batches.last_serial, ticket_batches.created_at, ticket_types.name')->join('ticket_types','ticket_types.id=ticket_batches.type_id')->where('ticket_batches.event_id',$id)->orderBy('ticket_batches.id','DESC')->limit(100)->get()->getResultArray();
        $ready=false;
        try {new TicketSecrets((string)env('ARS_TICKETS_KEY',''));$ready=true;} catch(Throwable) {}
        return $this->response->setBody(view('app/tickets/issuance',compact('event','types','batches','ready')+['requestId'=>bin2hex(random_bytes(16))]));
    }
    public function create(int $id): ResponseInterface
    {
        if ($denied=$this->guard()) return $denied;
        $url=site_url('tickets/eventos/'.$id.'/emision');
        try {
            $type=filter_var($this->request->getPost('type_id'),FILTER_VALIDATE_INT,['options'=>['min_range'=>1]]);
            $quantity=filter_var($this->request->getPost('quantity'),FILTER_VALIDATE_INT,['options'=>['min_range'=>1,'max_range'=>IssuancePolicy::MAX_BATCH]]);
            $request=$this->request->getPost('request_id');
            if (!$type || !$quantity || !is_string($request) || $this->request->getPost('confirmed')!=='yes') throw new InvalidArgumentException('Completa y confirma la solicitud.');
            $service=new IssuanceService(db_connect(),new TicketSecrets((string)env('ARS_TICKETS_KEY','')));
            $batch=$service->issue($id,$type,$quantity,(int)auth()->id(),$request);
            return redirect()->to($url)->with('message','Lote #'.$batch.' registrado.')->setHeader('Cache-Control','no-store, private');
        } catch (InvalidArgumentException $e) {
            return redirect()->to($url)->with('error',$e->getMessage())->setHeader('Cache-Control','no-store, private');
        } catch (Throwable $e) {
            log_message('error','ARS Tickets issuance failed: {class}',['class'=>$e::class]);
            return redirect()->to($url)->with('error','No se pudo emitir. Verifica configuración y base de datos.')->setHeader('Cache-Control','no-store, private');
        }
    }
}
