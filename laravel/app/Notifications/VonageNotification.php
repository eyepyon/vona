<?php

class VonageNotification extends Notification
{
    use Queueable;

    private string $message;

    public function __construct(string $message = '')
    {
        $this->message = $message;
    }

    public function via(object $notifiable): array
    {
        return ['vonage'];
    }

    public function toVonage(object $notifiable): VonageMessage
    {
        $from = 'Laravel App';  // 送信先で表示される送信者ID
        return (new VonageMessage)->from($from)->content($this->message);
    }
}

