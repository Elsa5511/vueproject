<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordEmail extends Mailable {

    use Queueable,
        SerializesModels;

    private $user;
    private $password;
    private $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(\App\Models\User $user, $password, $url = "") {
        $this->user = $user;
        $this->url = $url;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {

        return $this->subject(__('auth.new_password'))
                        ->view('emails.password-email')->with(['user' => $this->user, 'password' => $this->password, 'url' => url(''), 'title' => __('auth.new_password')]);
    }

}
