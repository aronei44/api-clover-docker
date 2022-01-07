<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $details;
    public $view;
    public function __construct($details, $view)
    {
        $this->details = $details;
        $this->view = $view;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if($this->view == 'welcome'){
            return $this->subject('Admin Clover')->view('mail.index');
        }else if($this->view == 'change_password'){
            return $this->subject('Admin Clover')->view('mail.password');
        }else if($this->view == 'forgot_password'){
            return $this->subject('Admin Clover')->view('mail.forgot');
        }
    }
}
