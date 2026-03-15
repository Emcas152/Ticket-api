<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Models\Entrada;

class GenerateTicketPdfAndSendEmail implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $entrada;

    public function __construct(Entrada $entrada)
    {
        $this->entrada = $entrada;
    }

    public function handle()
    {
        // Esqueleto: generar PDF con QR y enviar por correo
    }
}
