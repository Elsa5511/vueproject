<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordResetEmail extends Mailable {

    use Queueable,
        SerializesModels;

    private $user;
    private $reset;
    private $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(\App\Models\User $user, \App\Models\PasswordReset $reset, $url="") {
        $this->user = $user;
        $this->url = $url;
        $this->reset = $reset;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {

        return $this->subject(__('auth.reset_password'))
                        ->view('emails.reset-password-email')->with(['user' => $this->user, 'reset' => $this->reset, 'url' => url(''), 'title' => __('auth.reset_password')]);
    }

}
