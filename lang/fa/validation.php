<?php

return [
    'required'  => 'فیلد :attribute الزامی است.',
    'string'    => 'فیلد :attribute باید متن باشد.',
    'integer'   => 'فیلد :attribute باید عدد صحیح باشد.',
    'numeric'   => 'فیلد :attribute باید عدد باشد.',
    'max'       => [
        'string' => 'فیلد :attribute نباید بیشتر از :max کاراکتر باشد.',
        'numeric'=> 'فیلد :attribute نباید بیشتر از :max باشد.',
        'array'  => 'تعداد :attribute نباید بیشتر از :max مورد باشد.',
    ],
    'min'       => [
        'string' => 'فیلد :attribute باید حداقل :min کاراکتر باشد.',
        'numeric'=> 'فیلد :attribute باید حداقل :min باشد.',
    ],
    'exists'    => ':attribute انتخاب‌شده معتبر نیست.',
    'unique'    => 'این :attribute قبلاً استفاده شده است.',
    'array'     => 'فیلد :attribute باید آرایه باشد.',
    'boolean'   => 'فیلد :attribute باید بله یا خیر باشد.',
    'date'      => 'فیلد :attribute باید تاریخ معتبر باشد.',
    'email'     => 'فیلد :attribute باید ایمیل معتبر باشد.',
    'image'     => 'فایل :attribute باید تصویر باشد.',
    'mimes'     => 'فرمت :attribute باید یکی از این‌ها باشد: :values.',
    'file'      => 'فیلد :attribute باید فایل باشد.',
    'size'      => [
        'file'   => 'حجم :attribute نباید بیشتر از :size کیلوبایت باشد.',
        'numeric'=> 'مقدار :attribute باید :size باشد.',
    ],
    'in'        => ':attribute انتخاب‌شده معتبر نیست.',
    'regex'     => 'قالب :attribute صحیح نیست.',

    'custom' => [
        'mobile' => [
            'regex' => 'شماره موبایل باید با ۰۹ شروع شود و ۱۱ رقم باشد.',
        ],
        'national_code' => [
            'regex' => 'کد ملی باید ۱۰ رقم عددی باشد.',
        ],
    ],

    'attributes' => [
        'dehyari_id'    => 'دهیاری',
        'fee_type_id'   => 'نوع عوارض',
        'taxpayer_name' => 'نام مؤدی',
        'national_code' => 'کد ملی',
        'mobile'        => 'شماره موبایل',
        'address'       => 'آدرس',
        'inputs'        => 'اطلاعات محاسبه',
        'bank_reference'=> 'شماره پیگیری بانکی',
        'paid_at'       => 'تاریخ واریز',
        'amount_paid'   => 'مبلغ واریزی',
        'receipt'       => 'عکس فیش',
    ],
];
