<?php
declare(strict_types=1);
namespace App\Services\Tickets;
use RuntimeException;
final class TicketSecrets
{
    private string $key;
    public function __construct(string $hexKey)
    {
        if (!preg_match('/^[a-fA-F0-9]{64}$/D',$hexKey) || !extension_loaded('openssl')) throw new RuntimeException('Clave de emisión no configurada.');
        $this->key=hex2bin($hexKey);
    }
    public static function manualCode(): string
    {
        $alphabet='0123456789ABCDEFGHJKMNPQRSTVWXYZ';
        $bits=0;$buffer=0;$out='';
        foreach (str_split(random_bytes(10)) as $byte) {
            $buffer=($buffer<<8)|ord($byte);$bits+=8;
            while ($bits>=5) {
                $bits-=5;$out.=$alphabet[($buffer>>$bits)&31];
            }
            $buffer &= (1<<$bits)-1;
        }
        return $out;
    }
    public function generate(string $context): array
    {
        $token=bin2hex(random_bytes(32));
        $manual=self::manualCode();
        $iv=random_bytes(12);$tag='';
        $cipher=openssl_encrypt(json_encode(['token'=>$token,'manual'=>$manual],JSON_THROW_ON_ERROR),'aes-256-gcm',$this->key,OPENSSL_RAW_DATA,$iv,$tag,$context,16);
        if ($cipher===false || strlen($tag)!==16) throw new RuntimeException('Error de cifrado.');
        return ['token_hash'=>hash('sha256',$token),'manual_hash'=>hash('sha256',$manual),'secret_envelope'=>'v1.'.base64_encode($iv.$tag.$cipher)];
    }
    public function decrypt(string $envelope,string $context): array
    {
        $raw=str_starts_with($envelope,'v1.')?base64_decode(substr($envelope,3),true):false;
        if ($raw===false || strlen($raw)<=28) throw new RuntimeException('Sobre inválido.');
        $plain=openssl_decrypt(substr($raw,28),'aes-256-gcm',$this->key,OPENSSL_RAW_DATA,substr($raw,0,12),substr($raw,12,16),$context);
        if ($plain===false) throw new RuntimeException('No se pudo autenticar el sobre.');
        return json_decode($plain,true,512,JSON_THROW_ON_ERROR);
    }
}
