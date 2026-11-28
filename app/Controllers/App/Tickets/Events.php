<?php
declare(strict_types=1);
namespace App\Controllers\App\Tickets;
use App\Controllers\BaseController;
use App\Models\Tickets\EventModel;
use App\Models\Tickets\TicketTypeModel;
use App\Services\Tickets\EventPolicy;
use App\Services\Tickets\EventService;
use App\Services\Tickets\TicketAvailability;
use CodeIgniter\HTTP\ResponseInterface;
use InvalidArgumentException;
use Throwable;

final class Events extends BaseController
{
    private function guard(): ?ResponseInterface
    {
        $this->response->setHeader('Cache-Control','no-store, private, max-age=0')->setHeader('X-Robots-Tag','noindex, nofollow');
        if (!TicketAvailability::enabled()) return $this->response->setStatusCode(404)->setBody('Página no disponible.');
        $user=auth()->user();
        if (!$user || !$user->inGroup('institutional_admin') || !$user->can('tickets.access') || !$user->can('events.manage')) return $this->response->setStatusCode(403)->setBody('Acceso no autorizado.');
        return null;
    }
    public function index(): ResponseInterface
    {
        if ($denied=$this->guard()) return $denied;
        return $this->response->setBody(view('app/tickets/events',['events'=>(new EventModel())->orderBy('starts_at','DESC')->findAll(100)]));
    }
    public function show(int $id): ResponseInterface
    {
        if ($denied=$this->guard()) return $denied;
        $event=(new EventModel())->find($id);
        if (!$event) return $this->response->setStatusCode(404)->setBody('Evento inexistente.');
        return $this->response->setBody(view('app/tickets/event_detail',['event'=>$event,'types'=>(new TicketTypeModel())->where('event_id',$id)->findAll()]));
    }
    private function input(string $key): string
    {
        $value=$this->request->getPost($key);
        if (!is_string($value)) throw new InvalidArgumentException('Campo inválido: '.$key);
        return trim($value);
    }
    private function number(string $key,int $min,int $max): int
    {
        $v=filter_var($this->input($key),FILTER_VALIDATE_INT,['options'=>['min_range'=>$min,'max_range'=>$max]]);
        if ($v===false) throw new InvalidArgumentException('Número inválido: '.$key);
        return $v;
    }
    private function eventData(): array
    {
        $name=$this->input('name'); $slug=$this->input('slug'); $venue=$this->input('venue');
        if ($name==='' || mb_strlen($name)>150 || $venue==='' || mb_strlen($venue)>250 || !preg_match('~^[a-z0-9]+(?:-[a-z0-9]+)*$~D',$slug) || strlen($slug)>100) throw new InvalidArgumentException('Nombre, ubicación o identificador inválido.');
        $start=EventPolicy::utc($this->input('starts_at'));
        $from=EventPolicy::utc($this->input('access_from'));
        $to=EventPolicy::utc($this->input('access_to'));
        if ($from>=$to || $start<$from || $start>$to) throw new InvalidArgumentException('La ventana de acceso debe incluir el inicio del evento.');
        return ['name'=>$name,'slug'=>$slug,'venue'=>$venue,'starts_at'=>$start,'access_from'=>$from,'access_to'=>$to,'capacity'=>$this->number('capacity',1,100000)];
    }
    private function mutate(callable $work,string $fallback): ResponseInterface
    {
        if ($denied=$this->guard()) return $denied;
        try {
            $url=$work(new EventService(db_connect()),(int)auth()->id());
            return redirect()->to(site_url($url))->with('message','Cambios guardados.')->setHeader('Cache-Control','no-store, private');
        } catch (InvalidArgumentException $e) {
            return redirect()->to(site_url($fallback))->with('error',$e->getMessage());
        } catch (Throwable $e) {
            log_message('error','ARS Tickets event operation failed: {class}',['class'=>$e::class]);
            return redirect()->to(site_url($fallback))->with('error','No se pudo guardar. Verifica los datos y que el identificador no esté repetido.');
        }
    }
    public function create(): ResponseInterface
    {
        return $this->mutate(fn($svc,$actor)=>'tickets/eventos/'.$svc->save(null,$this->eventData(),$actor),'tickets/eventos');
    }
    public function update(int $id): ResponseInterface
    {
        return $this->mutate(fn($svc,$actor)=>'tickets/eventos/'.$svc->save($id,$this->eventData(),$actor),'tickets/eventos/'.$id);
    }
    public function addType(int $id): ResponseInterface
    {
        return $this->mutate(function($svc,$actor) use($id) {
            $name=$this->input('name'); $price=$this->input('price');
            if ($name==='' || mb_strlen($name)>100 || !preg_match('~^[0-9]{1,10}(?:\\.[0-9]{1,2})?$~D',$price)) throw new InvalidArgumentException('Nombre o precio inválido.');
            $svc->addType($id,['name'=>$name,'price'=>$price,'adult_qty'=>$this->number('adult_qty',0,100),'student_qty'=>$this->number('student_qty',0,100),'quota'=>$this->number('quota',1,100000)],$actor);
            return 'tickets/eventos/'.$id;
        },'tickets/eventos/'.$id);
    }
    public function changeStatus(int $id): ResponseInterface
    {
        return $this->mutate(function($svc,$actor) use($id) {
            $svc->changeStatus($id,$this->input('status'),$actor);
            return 'tickets/eventos/'.$id;
        },'tickets/eventos/'.$id);
    }
}
