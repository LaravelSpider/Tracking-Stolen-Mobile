<?php

namespace App\Notifications;

use App\Models\StolenDevice;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeviceMatchFound extends Notification
{
    use Queueable;

    protected $device;

    public function __construct(StolenDevice $device)
    {
        $this->device = $device;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Device Found - ' . $this->device->brand . ' ' . $this->device->model)
            ->line('Great news! Your reported stolen device has been found.')
            ->line('Device: ' . $this->device->brand . ' ' . $this->device->model)
            ->line('IMEI: ' . $this->device->imei)
            ->line('Please contact the authorities for further information.')
            ->action('View Details', url('/devices/' . $this->device->id));
    }

    public function toArray($notifiable)
    {
        return [
            'device_id' => $this->device->id,
            'message' => 'Your stolen device has been found: ' . $this->device->brand . ' ' . $this->device->model,
            'imei' => $this->device->imei,
        ];
    }
}
