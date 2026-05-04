<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Payment;

class PaymentStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $action;

    /**
     * Create a new message instance.
     */
    public function __construct(Payment $payment, $action = 'verified')
    {
        $this->payment = $payment;
        $this->action = $action; // 'verified', 'rejected'
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $actionText = match($this->action) {
            'verified' => 'Payment Verified',
            'rejected' => 'Payment Issue',
            default => 'Payment Update'
        };

        return new Envelope(
            subject: "{$actionText} - Icon Venue & Suites",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-status',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
