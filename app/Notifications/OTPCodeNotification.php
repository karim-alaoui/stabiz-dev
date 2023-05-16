<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

/**
 * Send an email containing the 6 digit OTP code through
 * mail (as of now, only mail is supported)
 * Class OTPCodeNotification
 * @package App\Notifications
 */
class OTPCodeNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(public int $otp, public ?string $subject = null)
    {
        //TODO: restrict access to articles, videos if not paid for premium packages
        if (!$this->subject) $this->subject = __('notification.otp_mail');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via(): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return MailMessage
     */
    public function toMail(): MailMessage
    {
        $mailtext = "あなたのワンタイムパスワードは $this->otp <br/> <small><b>このワンタイムパスワードは5分間有効です。</b></small>";
        return (new MailMessage())
            ->subject($this->subject)
            ->greeting('登録用ワンタイムパスワード')
            ->line(new HtmlString($mailtext))
            ->line('ご登録いただきありがとうございます。STABIZのページに戻り本登録をお済ませください。');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [

        ];
    }
}
