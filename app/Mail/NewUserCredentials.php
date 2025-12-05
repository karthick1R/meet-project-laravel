<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserCredentials extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public User $user,
        public string $plainPassword,
        public ?string $loginUrl = null
    ) {
    }

    public function build(): self
    {
        $this->loginUrl ??= route('login');

        return $this->subject('Your Meeting Room Account Is Ready')
            ->markdown('emails.new-user-credentials', [
                'user' => $this->user,
                'plainPassword' => $this->plainPassword,
                'loginUrl' => $this->loginUrl,
            ]);
    }
}

