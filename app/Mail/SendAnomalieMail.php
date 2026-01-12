<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendAnomalieMail extends Mailable
{
    use Queueable, SerializesModels;

    public $anomalie;

    public function __construct($anomalie)
    {
        $this->anomalie = $anomalie;
    }

    public function build()
    {
        return $this->subject('🚨 Nouvelle anomalie signalée')
                    ->view('emails.anomalie-notification');
    }
}
