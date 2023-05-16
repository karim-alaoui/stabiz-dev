<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrganizerRegistrationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $confirmationCode;

    public function __construct($confirmationCode)
    {
        $this->confirmationCode = $confirmationCode;
    }

    public function build()
    {
        return $this->view('emails.organizer-registration-confirmation', [
            'confirmationCode' => $this->confirmationCode,
        ])
        ->subject('Confirm your email address');
    }
}
