<?php

namespace App\Notifications;

use App\Models\CheckoutRequest;
use App\Services\SettingsService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MemberCheckoutRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public string $url,
        public array $payload = []
    ) {
    }

    public static function submitted(CheckoutRequest $request): self
    {
        return new self(
            title: 'Checkout request submitted',
            message: 'Your checkout request has been submitted and is awaiting review.',
            url: route('checkout-requests.index'),
            payload: [
                'checkout_request_id' => $request->id,
                'status' => $request->status,
            ],
        );
    }

    public static function updated(CheckoutRequest $request): self
    {
        return new self(
            title: 'Checkout request updated',
            message: sprintf('Your checkout request is now %s.', $request->status),
            url: route('checkout-requests.index'),
            payload: [
                'checkout_request_id' => $request->id,
                'status' => $request->status,
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
            ->action('Open checkout requests', $this->url);
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
