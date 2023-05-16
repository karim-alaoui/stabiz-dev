<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

/**
 * Class EmailTemplateSeeder
 * @package Database\Seeders
 */
class EmailTemplateSeeder extends Seeder
{
    private function userAppliedNotifyStaffTemplate(): array
    {
        $body = <<<BODY
##applied_by_user_type## ##applied_by_first_name## ##applied_by_last_name## さんが ##applied_to_user_type## ##applied_to_first_name## ##applied_to_last_name## さんに紹介の希望をだしています。
プロフィールを確認して紹介のオペレーションを実行してください。
BODY;

        return [
            'subject' => '【StaBiz】ユーザーのエントリーがあります。',
            'name' => EmailTemplate::USER_APPLIED_NOTIFY_STAFF,
            'body' => $body
        ];
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $templates = [
            [
                'subject' => 'Subscription started',
                'name' => EmailTemplate::SUB_START,
                'body' => <<<BODY
Hello world
BODY

            ],
            [
                'subject' => 'Subscription Cancelled',
                'name' => EmailTemplate::SUB_CANCEL,
                'body' => <<<BODY
Hello world
BODY
            ],
            [
                'subject' => 'OTP',
                'name' => EmailTemplate::SEND_OTP,
                'body' => <<<BODY
Hello world
BODY
            ],
            [
                'subject' => '【STABIZ】アップロードした書類が承認されました',
                'name' => EmailTemplate::APPL_ACCEPTED,
                'body' => <<<BODY
アップロードしていただいた ##applied_to_first_name## が承認されました。
データのご提出誠にありがとうございます。頂いた情報を元により良いアントレプレナーの紹介
いたします。
BODY
            ],
            [
                'subject' => '【STABIZ】アップロードした書類が差し戻されました',
                'name' => EmailTemplate::DOC_REJECTED,
                'body' => <<<BODY
アップロードしていただいた ##doc_name## が差し戻されました。再度アップロードをお試しください。
BODY

            ]
        ];

        $templates[] = $this->userAppliedNotifyStaffTemplate();

        foreach ($templates as $template) {
            $mailtemplate = EmailTemplate::where('name', 'ilike', $template['name'])->first();
            if ($mailtemplate) {
                $mailtemplate->subject = $template['subject'];
                $mailtemplate->body = $template['body'];
                $mailtemplate->save();
            } else {
                EmailTemplate::create($template);
            }
        }
    }
}
