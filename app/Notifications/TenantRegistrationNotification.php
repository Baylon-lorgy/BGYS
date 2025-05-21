<?php

namespace App\Notifications;

use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TenantRegistrationNotification extends Notification
{
    use Queueable;

    protected $tenant;
    protected $password;

    /**
     * Create a new notification instance.
     */
    public function __construct(Tenant $tenant, $password = null)
    {
        $this->tenant = $tenant;
        $this->password = $password;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('MasterBaker Registration Approved')
            ->greeting('Hello ' . $this->tenant->name . '!')
            ->line('Your bakery registration has been approved!')
            ->line('Bakery Name: ' . $this->tenant->bakery_name)
            ->line('Domain: ' . $this->tenant->domain_name)
            ->line('You can now access your dashboard using your credentials.');

        if ($this->password) {
            $message->line('Your temporary password is: ' . $this->password)
                   ->line('Please change your password after logging in.');
        }

        $message->action('Login to Dashboard', url('/tenant/login'))
                ->line('Thank you for choosing our platform!');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tenant_id' => $this->tenant->id,
            'bakery_name' => $this->tenant->bakery_name,
            'status' => 'approved'
        ];
    }
} 