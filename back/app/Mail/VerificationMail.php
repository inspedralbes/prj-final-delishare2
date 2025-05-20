<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $username;
    public $messageContent;

    public function __construct($email, $username, $messageContent)
    {
        $this->email = $email;
        $this->username = $username;
        $this->messageContent = $messageContent;
    }

    public function build()
    {
        return $this->subject('Nuevo mensaje de verificación')
            ->view('emails.verification');
    }
}
