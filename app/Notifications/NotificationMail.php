<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Notification mail which uses the BaseTemplateNotification
 * Difference is that it puts the notification in a queue
 * Class NotificationMail
 * @package App\Notifications
 */
class NotificationMail extends BaseTemplateNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param EmailTemplate $template
     * @param string $subject
     * @param string $body
     */
    public function __construct(EmailTemplate $template, string $subject, string $body)
    {
        parent::__construct($template, $subject, $body);
    }
}
