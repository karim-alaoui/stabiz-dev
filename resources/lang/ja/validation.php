<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */
    'accepted' => ':attribute は受け付けられました。',
    'active_url' => ':attribute は無効なURLです。',
    'after' => ':attribute は :date 以降である必要があります。',
    'after_or_equal' => ':attribute は :date の日付と同じかそれ以降である必要があります。',
    'alpha' => ':attribute には文字が入力されている可能性があります。',
    'alpha_dash' => ':attribute は文字、数字、ダッシュ、アンダースコアのみを入力することができます。',
    'alpha_num' => ':attribute は、文字と数字のみを含むことができます。',
    'array' => ':attribute は配列である必要があります。',
    'before' => ':attribute は :date 以降を入力する必要があります。',
    'before_or_equal' => ':attribute は :date の日付より同じか後である必要があります。',
    'between' => [
        'numeric' => ':attribute は :min 〜 :max の間で設定する必要があります。',
        'file' => ':attribute は　 :min 〜 :max キロバイトの間である必要があります。',
        'string' => ':attribute は　:min 〜 :max 文字である必要があります。',
        'array' => ':attribute は :min 〜 :max 個の間である必要があります。',
    ],
    'boolean' => ':attribute フィールド は　true か falseである必要があります。 ',
    'confirmed' => ':attribute の情報が一致しません。',
    'date' => ':attribute は有効な日付ではありません。',
    'date_equals' => ':attribute は :date と同じ日付である必要があります。',
    'date_format' => ':attribute は :format と一致しません。',
    'different' => ':attribute と :other 別の値または不一致である必要があります。',
    'digits' => ':attribute は　:digits 桁である必要があります。 ',
    'digits_between' => ':attribute は :min 〜 :max 桁である必要があります。 ',
    'dimensions' => ':attribute は無効な画像の比率です。',
    'distinct' => ':attribute のフィールドが重複する値を持っています。',
    'email' => ':attribute は有効なメールアドレスである必要があります。',
    'ends_with' => ':attribute following: :values で終わる必要があります。',
    'exists' => '選択された :attribute は無効です。',
    'file' => ':attribute はファイルである必要があります。',
    'filled' => ':attribute フィールドは値を入力する必要があります。',
    'gt' => [
        'numeric' => ':attribute は :value 以上の値である必要があります。 ',
        'file' => ':attribute は :value キロバイト以上である必要があります。 ',
        'string' => ':attribute は :value 文字数以上である必要があります。',
        'array' => ':attribute は :value 個以上である必要があります。',
    ],
    'gte' => [
        'numeric' => ':attribute は :value は同じかそれ以上の値の入力が必要です。',
        'file' => ':attribute は :value キロバイト以上である必要があります。',
        'string' => ':attribute は :value 文字以上である必要があります。',
        'array' => ':attribute　は :value 個以上である必要があります。',
    ],
    'image' => ':attribute は 画像である必要があります。',
    'in' => '選択された :attribute は無効です。',
    'in_array' => ':attribute フィールドは :other に存在しません。',
    'integer' => ':attribute は整数である必要があります。',
    'ip' => ':attribute は有効なIPアドレスである必要があります。 ',
    'ipv4' => ':attribute は有効なIPv4アドレスである必要があります。',
    'ipv6' => ':attribute は有効なIPv6アドレスである必要があります。',
    'json' => ':attribute は有効なjsonである必要があります。',
    'lt' => [
        'numeric' => ':attribute は :value より少ない数である必要があります。',
        'file' => ':attribute は :value より少ないキロバイト数である必要があります。 ',
        'string' => ':attribute は :value より少ない文字数である必要があります。 ',
        'array' => ':attribute は :value より少ない数である必要があります。',
    ],
    'lte' => [
        'numeric' => ':attribute は :value 以下である必要があります。',
        'file' => ':attribute は :value キロバイト以下である必要があります。 ',
        'string' => ':attribute は :value 文字以下である必要があります。',
        'array' => ':attribute は :value 以下である必要があります。',
    ],
    'max' => [
        'numeric' => ':attribute は :max より大きい値を入力できません。',
        'file' => ':attribute :max キロバイトより大きい値を入力できません。',
        'string' => ':attribute  :max 文字より大きい値を入力できません。',
        'array' => ':attribute  :max 個より大きい値を入力できません。',
    ],
    'mimes' => ':attribute は ファイルの type: が :valuesである必要があります。',
    'mimetypes' => ':attribute は ファイルの type: が :values である必要があります。',
    'min' => [
        'numeric' => ':attribute は最低 :min である必要があります。',
        'file' => ':attribute は最低 :min キロバイトである必要があります。',
        'string' => ':attribute は最低 :min 文字である必要があります。',
        'array' => ':attribute は最低 :min 個である必要があります。',
    ],
    'not_in' => '選択された :attribute は無効です。',
    'not_regex' => ':attribute フォーマットは無効です。',
    'numeric' => ':attribute は数字である必要があります。',
    'password' => 'password が正しくありません。',
    'present' => ':attribute フィールドは入力されている必要があります。',
    'regex' => ':attribute フォーマットは無効です。',
    'required' => ':attribute フィールドは必須です。',
    'required_if' => ':attribute フィールドは :other が :value の場合は必須です。',
    'required_unless' => ':attribute フィールドは :other が :values で無い限り必須です。',
    'required_with' => ':attribute フィールドは :values が存在する場合、必須です。',
    'required_with_all' => ':attribute フィールドは :values が入力されている場合は必須になります。',
    'required_without' => ':attribute フィールドは :values が入力されていない場合は必須になります。',
    'required_without_all' => ':attribute フィールドは :values が存在しない場合は必須です。',
    'same' => ':attribute は :other 一致している必要があります。',
    'size' => [
        'numeric' => ':attribute は :size である必要があります。',
        'file' => ':attribute は:size kilobytes である必要があります。 ',
        'string' => ':attribute は :size characters である必要があります。',
        'array' => ':attribute :size 個含まれている必要があります。',
    ],
    'starts_with' => ':attribute は　following: :values　以下のいずれかで始まる必要があります。',
    'string' => ':attribute は整数である必要があります。',
    'timezone' => ':attribute は有効な範囲である必要があります。',
    'unique' => ':attribute はすでに利用されています。',
    'uploaded' => ':attribute はアップロードに失敗しました。',
    'url' => ':attribute フォーマットは無効です。',
    'uuid' => ':attribute は有効なUUIDである必要があります。',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],
    'credit_card' => [
        'card_length_invalid' => '無効なクレジットカード番号です。',
        'card_cvc_invalid' => '無効なCVC番号です。',
        'card_expiration_year_invalid' => 'クレジットカードの有効期限 年 が無効です。',
        'card_expiration_month_invalid' => 'クレジットカードの有効期限 月 が無効です。',
        'card_invalid' => '無効なクレジットカード番号です'
    ],
    'max_file' => 'アップロードファイルの最大容量は :size です',
    'valid_date_formats' => '有効な :attribute フォーマットは -YYYY-MM-DD または YYY-MM-D です',
    'validation_error' => 'バリデーションエラー',
    'no_permission' => '操作に必要な許可が付与されていません',
    'unauthorized' => 'この操作は許可されていません',
    'both_cant_be' => ':attribute と :attribute2 は同じ :boolean の値を入力できません。',
    'valid_date' => ':attribute は有効な日付である必要があります。',
    'valid_datetime' => ':attribute は有効な日時である必要があります。',
    'age_lowerlimit' => ':attribute は :lower 以上である必要があります。',
    'age_upperlimit' => ':attribute は :upper 以下である必要があります。',
    'age_limit_no_match' => ':attribute は :lower 以上 :upper 以下である必要があります。',
    'empty_html' => ':attribute はコンテンツが存在しません。',
    'match_either_format' => '有能なフォーマットは :formats です。'
];
