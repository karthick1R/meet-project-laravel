<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProfileUpdated extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public User $user,
        public string $originalEmail
    ) {
    }

    public function build(): self
    {
        return $this->subject('Your Profile Was Updated')
            ->markdown('emails.profile-updated', [
                'user' => $this->user,
                'originalEmail' => $this->originalEmail,
            ]);
    }
}


