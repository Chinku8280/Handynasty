<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class forgotMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
   
     public $userDetails;

     public function __construct($userDetails)
     {
         $this->userDetails = $userDetails;
     }
 
     public function build()
     {
         return $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                     ->subject('Your OTP Code')
                     ->view('admin.emails.forget_mail')
                     ->with('userDetails', $this->userDetails);
     }
}
