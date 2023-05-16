<?php


namespace App\Notifications;


use App\Models\EmailTemplate;

/**
 * Notification mail that is sent immediately.
 * Difference between NotificationMail and this one is that
 * NotificationMail is put into queue and this one is sent immediately
 */
class NotificationMailWithoutQueue extends BaseTemplateNotification
{
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
