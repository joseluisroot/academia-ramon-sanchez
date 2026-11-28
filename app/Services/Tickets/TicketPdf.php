<?php
declare(strict_types=1);
namespace App\Services\Tickets;
use RuntimeException;
final class TicketPdf
{
    /** Renderer only. Official delivery must go through audited printing in TKT-003.3. */
    public function render(array $event,array $type,array $cards,string $format,bool $preview=true): string
    {
        if (!class_exists(\TCPDF::class)) throw new RuntimeException('Instala la dependencia PDF.');
        if (!$cards || count($cards)>500) throw new RuntimeException('Cantidad inválida.');
        [$w,$h,$per]=PrintLayout::page($format);
        $pdf=new \TCPDF('P','mm',[$w,$h],true,'UTF-8',false);
        $pdf->setPrintHeader(false);$pdf->setPrintFooter(false);
        $pdf->SetMargins(0,0,0);$pdf->SetAutoPageBreak(false,0);
        $pdf->SetTitle($preview?'ARS Tickets - Muestra':'ARS Tickets');
        foreach (array_values($cards) as $i=>$card) {
            [, $x,$y]=PrintLayout::position($format,$i);
            if ($i%$per===0) $pdf->AddPage('P',[$w,$h]);
            $this->draw($pdf,$event,$type,$card,$x,$y,$preview);
        }
        return $pdf->Output('','S');
    }
    private function draw(\TCPDF $p,array $event,array $type,array $card,float $x,float $y,bool $preview): void
    {
        $navy=[7,27,73];$gold=[177,137,57];
        $p->SetDrawColor(...$navy);$p->SetLineWidth(.35);$p->RoundedRect($x+5,$y+5,91.6,142.4,2);
        $p->SetDrawColor(...$gold);$p->SetLineWidth(.18);$p->Rect($x+7,$y+7,87.6,138.4);
        $logo=FCPATH.'assets/img/brand/escudo-oficial.svg';
        if (!is_file($logo)) throw new RuntimeException('Escudo oficial no disponible.');
        $p->ImageSVG($logo,$x+40.8,$y+9,20,23);
        $text=function(string $s,float $top,float $size,string $style='',array $color=[7,27,73],string $font='dejavusans') use($p,$x,$y):void {
            $p->SetFont($font,$style,$size);$p->SetTextColor(...$color);
            $p->SetXY($x+10,$y+$top);$p->Cell(81.6,5,$s,0,0,'C',false,'',1);
        };
        $text('ACADEMIA INTEGRAL DE FÚTBOL',33,8,'B');
        $text('PROFESOR RAMÓN SÁNCHEZ',38,10,'B');
        $p->Line($x+17,$y+45,$x+84.6,$y+45);
        // Small vector laurels rather than large ink-heavy fills.
        $p->SetFillColor(...$gold);
        for($j=0;$j<5;$j++){
            $p->Ellipse($x+13+$j*.5,$y+49+$j*3,1,2,35,0,360,'F');
            $p->Ellipse($x+88.6-$j*.5,$y+49+$j*3,1,2,-35,0,360,'F');
        }
        $name=(string)$event['name'];
        if (preg_match('/^Gala de Premios (\\d{4})$/iu',$name,$m)) {
            $text('GALA DE',47,14,'',$navy,'dejavuserif');
            $text('PREMIOS',54,21,'B',$navy,'dejavuserif');
            $text($m[1],62,15,'B',$gold,'dejavuserif');
        } else {
            $p->SetFont('dejavuserif','B',15);$p->SetTextColor(...$navy);
            $p->MultiCell(66,19,$name,0,'C',false,1,$x+17.8,$y+47,true,0,false,true,19,'M',true);
        }
        $text(str_replace('T',' · ',EventPolicy::local($event['starts_at'])),70,9,'B');
        $p->SetFont('dejavusans','',9);
        $p->MultiCell(81.6,13,(string)$event['venue'],0,'C',false,1,$x+10,$y+76,true,0,false,true,13,'M',true);
        $text('Tarjeta: $'.number_format((float)$type['price'],2),90,14,'B');
        $text((string)$type['name'],96,8,'B');
        $benefit=(int)$type['adult_qty'].' adulto(s) + '.(int)$type['student_qty'].' alumno(s)';
        if (preg_match('/^Gala de Premios \\d{4}$/iu',$name)) $benefit='Incluye cena: '.$benefit;
        $text($benefit,101,8);
        $p->SetDrawColor(...$gold);$p->Line($x+10,$y+108,$x+91.6,$y+108);
        $p->SetFont('dejavusans','B',13);$p->SetTextColor(...$navy);
        $p->SetXY($x+10,$y+113);$p->Cell(44,6,'N.º '.str_pad((string)$card['serial'],6,'0',STR_PAD_LEFT),0,0,'L',false,'',1);
        $p->SetFont('dejavusans','',7);
        $p->SetXY($x+10,$y+121);$p->Cell(44,4,$preview?'CÓDIGO DE MUESTRA':'Código manual',0,0,'L');
        $p->SetFont('dejavusans','B',8);
        $p->SetXY($x+10,$y+126);$p->Cell(44,4,$preview?'NO VÁLIDO':implode('-',str_split($card['manual'],4)),0,0,'L',false,'',1);
        // 29 mm matrix with white clearance; physical quiet zone and readability require QA.
        $p->SetFillColor(255,255,255);$p->Rect($x+54,$y+109,39,35,'F');
        $p->write2DBarcode($preview?'ARS-MUESTRA-NO-VALIDA':PrintLayout::qrPayload($card['token']),'QRCODE,M',$x+58,$y+111,29,29,['border'=>0,'padding'=>0,'fgcolor'=>[0,0,0],'bgcolor'=>false], 'N');
        $p->SetFont('dejavusans','',7);$p->SetTextColor(...$navy);
        $p->SetXY($x+10,$y+137);$p->Cell(44,4,$preview?'MUESTRA · SIN ACCESO':'Uso único · Validación digital',0,0,'L',false,'',1);
        if ($preview) $text('MUESTRA NO VÁLIDA',8,7,'B',[160,30,30]);
    }
}
