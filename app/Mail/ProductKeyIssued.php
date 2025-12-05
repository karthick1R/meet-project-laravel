<?php

namespace App\Mail;

use App\Models\ProductKey;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductKeyIssued extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public ProductKey $productKey,
        public ?string $registrationUrl = null,
        public ?string $loginUrl = null
    ) {
    }

    public function build(): self
    {
        $this->registrationUrl ??= route('register');
        $this->loginUrl ??= route('login');

        return $this->subject('Your Product Key & Next Steps')
            ->markdown('emails.product-key-issued', [
                'productKey' => $this->productKey,
                'registrationUrl' => $this->registrationUrl,
                'loginUrl' => $this->loginUrl,
            ]);
    }
}