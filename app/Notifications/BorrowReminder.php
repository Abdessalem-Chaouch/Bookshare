<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BorrowReminder extends Notification
{
    use Queueable;

    protected $message;
    protected $borrow_id;
    protected $type;

    public function __construct($message, $borrow_id, $type)
    {
        $this->message = $message;
        $this->borrow_id = $borrow_id;
        $this->type = $type; // 'expire_2_days' ou 'expired_today'
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'borrow_id' => $this->borrow_id,
            'type' => $this->type,
        ];
    }
}
