<?php


namespace App\Notifications;


use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\App;
use Illuminate\Support\HtmlString;

/**
 * Notifications that are sent using the email templates
 * Each email template can define subject, body with some variables like first name, last name etc
 * Class BaseTemplateNotification
 * @package App\Notifications
 */
class BaseTemplateNotification extends Notification
{
    protected ?bool $excludeMailChannel = null;
    protected ?string $replyTo = null;

    /**
     * @param mixed $replyTo
     * @return BaseTemplateNotification
     */
    public function setReplyTo(string $replyTo = null): static
    {
        $this->replyTo = $replyTo;
        return $this;
    }

    /**
     * We use something like
     * env == 'local' to exclude real mail sending on local
     * @param bool $exclude
     * @return BaseTemplateNotification
     */
    public function excludeMailChannel(bool $exclude = true): static
    {
        $this->excludeMailChannel = $exclude;
        return $this;
    }

    /**
     * BaseTemplateNotification constructor.
     * @param $template
     * @param string $subject
     * @param string $body
     */
    public function __construct(public $template, public string $subject, public string $body)
    {
        /**
         * if we end up running it on local, testing, we don't want to send actual email
         * and we will just store what would be sent in
         * the mail in the database using database channel (defined in via method)
         */
        if (App::environment(['local', 'testing'])) {
            $this->excludeMailChannel = true;
        }
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via(): array
    {
        $via = ['mail'];
        if ($this->excludeMailChannel) $via = [];
        return array_merge($via, ['database']);
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return MailMessage
     */
    public function toMail(): MailMessage
    {
        $mail = (new MailMessage())
            ->subject($this->subject ?: $this->template->subject);
        $replyTo = $this->replyTo;
        if ($replyTo) {
            $mail = $mail->replyTo($replyTo);
        }
        /*if (App::environment() == 'production') {
            $mail = $mail->bcc(explode(',', config('other.bcc_email')));
        }*/
        return $mail
            ->line(new HtmlString($this->body));
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        // don't change this format
        return [
            'email_template_id' => $this->template?->id,
            'subject' => $this->subject,
            'body' => $this->body
        ];
    }
}
