<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyEmail extends Mailable {

    use Queueable,
        SerializesModels;

    private $user;
    private $url;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(\App\Models\User $user, $url = '') {
        $this->user = $user;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build() {

        return $this->subject(__('auth.verify_email'))
                        ->view('emails.verify-email')->with(['user' => $this->user, 'url' => $this->url, 'title' => __('auth.verify_email')]);
    }

}
