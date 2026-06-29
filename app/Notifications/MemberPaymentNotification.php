<?php

namespace App\Notifications;

use App\Models\Payment;
use App\Services\SettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberPaymentNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public string $url,
        public array $payload = []
    ) {
    }

    public static function created(Payment $payment): self
    {
        return new self(
            title: 'Payment recorded',
            message: sprintf('Your payment for %s has been recorded.', optional($payment->payment_month)->format('F Y') ?? 'the selected month'),
            url: route('payment-history.index'),
            payload: [
                'payment_id' => $payment->id,
                'status' => $payment->status,
            ],
        );
    }

    public static function approved(Payment $payment): self
    {
        return new self(
            title: 'Payment approved',
            message: sprintf('Your payment for %s has been approved.', optional($payment->payment_month)->format('F Y') ?? 'the selected month'),
            url: route('payment-history.index'),
            payload: [
                'payment_id' => $payment->id,
                'status' => $payment->status,
            ],
        );
    }

    public static function rejected(Payment $payment): self
    {
        return new self(
            title: 'Payment rejected',
            message: sprintf('Your payment for %s was rejected.', optional($payment->payment_month)->format('F Y') ?? 'the selected month'),
            url: route('payment-history.index'),
            payload: [
                'payment_id' => $payment->id,
                'status' => $payment->status,
            ],
        );
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];
        $settings = app(SettingsService::class);

        if ($settings->get('notification_channels', true) && $settings->get('email_enabled', true) && ! empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject($this->title)
            ->greeting('Hello '.$notifiable->name)
            ->line($this->message)
            ->action('Open payment history', $this->url);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'payload' => $this->payload,
        ];
    }
}
