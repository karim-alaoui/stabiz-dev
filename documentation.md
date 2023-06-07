### DOCUMENTATION

## no token required:

- post('register/organizer')
    `REQUEST BODY`
        {
            "email": ['required', 'email', 'unique:organizers,email'],
            "password": ['required', Password::min(8)],
            "professional_corporation_name": ['required', 'string'],
            "name_of_person_in_charge": ['required', 'string'],
            "phone_number": ['required', 'string'],
            "square_one_members_id": ['nullable', 'string'],
        }
    `POSSIBLE RESPONSES`
- post('resend-confirmation-code')
    `REQUEST BODY`
        {
            "email": ['required' ],
        }
    `POSSIBLE RESPONSES`
- post('verify-confirmation-code')
    `REQUEST BODY`
        {
            "email": ['required' ],
            "confirmation_code": ['required' ],
        }
    `POSSIBLE RESPONSES`
- post('organizer/login')
    `REQUEST BODY`
        {
            "email": ['required' ],
            "password": ['required' ],
        }
    `POSSIBLE RESPONSES`
        {
            "accessToken": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiI5M2FkZDZjYy00ZWJhLTQ1MDItOTFkZC1hYmFmNTM5ZWYwZWMiLsdfgsry5436WUxNjcyZDFmMmIzYzcwYmNiM2IyOTBiY2RmMTRjNTUzZDk4OWZkYTJkN2ExMzQ5YTk5MzUxY2E3ZWEzNTU0N2ZiMyIsImlhdCI6MTY4NTYwNDEwMasdasdasdasdYwNDEwMy44MTY1MSwiZXhwIjoxNzAxNDE1MzAzLjgxMjQzMywic3ViIjoiMTAwMDAiLCJzY29wZXMiOltdfQ.UFcepZGTZJSRH5QOTFd4qsQkprYwyYH-ccLMLd9-VgcQZFyunBq7Lq1Zt-H0SJW556_Z-9NHiBRNHA7p-hdCSm7y2LbEIs05AiVq3mpkEttwpy19NXAPWQ0JBKrmeKEwrrlpjLff9IPqTB2Rr1iW1G4hCHLRcqGLjIjbO0FyIZYKehA2HD-2Em7_ZhGKwsdfsdr2345lllj-vjQ40Q_5248RxOEMPGf7oNtrFMa3JfwqqvRxVeTU48xtGiajrPNJRr9KL0ognHAhwnN4WzIdMwdVcN-UV8DOL2NJh4tqFyfXjvZPOsaq0WUQj6BMVJ3x3TECEcqgxG2LR7_pDxHGQSJ6v8zQnRgLV311YueKN8mJBqcQ8kIGvZP3nA_Ge2IH5PGuyrP7gGZ345345dfghdfghZuZrPvtBMS2Ks9bDprZvRf_H8-a2YnSaHkIs9kZHQ1S0WNS3Lh1LTeoxPn_2aWJ32E9-w59tv-FgWNwonYKwmSKTWyDocDxM_XQMuKClVIkaYG5kiO43vJ1LDmC5zCHDxTAF85h-m5oG0nf1fSnSe84eUSSQb28cPIfYvVfKxx95vsizxTkZUojJRnKztlkwuEkRv6rnw3REAA925IFEqxf6RXWXnsd2ILYCf-WOdgQD2Mrlcy8rk-wF4aKQe7Kk",
            "token": {
                "id": "551d9cd21ed29ae1672d1f2b3c70bcb3b290bcdf14c553d989fda2d7a1349a99351ca7ea35547fb3",
                "user_id": 10000,
                "client_id": "93add6cc-4eba-4502-91dd-abaf539ef0ec",
                "name": "login",
                "scopes": [],
                "revoked": false,
                "created_at": "2023-06-01 07:21:43",
                "updated_at": "2023-06-01 07:21:43",
                "expires_at": "2023-12-01T07:21:43.000000Z"
            }
        }
- post('organizer/forget-password')
    `REQUEST BODY`
        {
            "email": ['required' ],
        }
    `POSSIBLE RESPONSES`
- post('organizer/reset-password')
    `REQUEST BODY`
        {
            "email": ['required' ],
            "code": ['required' ],
            "password": ['required', Password::min(8)],
        }
    `POSSIBLE RESPONSES`

## organizer token required:

- get('organizer/profile')
    `REQUEST BODY`
    `POSSIBLE RESPONSES`
        {
            "id": 10000,
            "professional_corporation_name": "Acme Inc.",
            "name_of_person_in_charge": "Jane Doe",
            "email": "karim3alaoui@gmail.com",
            "phone_number": "1234567890",
            "square_one_members_id": "abc123",
            "created_at": "2023-05-08T06:06:04.000000Z",
            "updated_at": "2023-05-30T08:21:21.000000Z",
            "email_verified_at": null
        }
- put('organizer/profile')
    `REQUEST BODY`
        (at least one of the following) 
        {
            "founder_id",
            "first_name",
            "last_name",
            "email", 
            "password",
            "role"
        }
    `POSSIBLE RESPONSES`
- post('organizer/founder-users')
    `REQUEST BODY`
        {
            "founder_id": ['required' ],
            "first_name": ['required' ],
            "last_name": ['required' ],
            "email": ['required' ],
            "password": ['required', Password::min(8)],
            "role": (one of the following)['readwrite', 'readonly', 'expired'],
        }
    `POSSIBLE RESPONSES`
- get('organizer/founder-users/{userId}')
    `REQUEST BODY`
    `POSSIBLE RESPONSES`
        {
            "first_name": "john",
            "last_name": "Doee",
            "email": "johndoe@exampleeee.fr",
            "role": "readonly"
        }
- put('organizer/founder-users/{userId}')
    `REQUEST BODY`
        (at least one of the following)
        {
            "founder_id": ['required' ],
            "first_name": ['required' ],
            "last_name": ['required' ],
            "email": ['required' ],
            "password": ['required', Password::min(8)],
            "role": (one of the following)['readwrite', 'readonly', 'expired'],
        }
    `POSSIBLE RESPONSES`
- get('organizer/founder-profiles/{id}/founder-users')
    `REQUEST BODY`
    `POSSIBLE RESPONSES`
        {
            "founder_users": [
                {
                    "id": 9,
                    "role": "readonly",
                    "first_name": "John",
                    "last_name": "Doe",
                    "email": "johndoe@example.com"
                },
                {
                    "id": 12,
                    "role": "readwrite",
                    "first_name": "John",
                    "last_name": "Doe",
                    "email": "johndoe@examplee.com"
                },
                {
                    "id": 13,
                    "role": "readwrite",
                    "first_name": "John",
                    "last_name": "Doe",
                    "email": "johndoe@exampleee.com"
                }
            ]
        }
- get('organizer/founder-profiles')
    `REQUEST BODY`
    `POSSIBLE RESPONSES`
        [
            {
                "id": 4,
                "user_id": 10000,
                "company_name": "電通株式会社",
                "is_listed_company": true,
                "area_id": 5,
                "prefecture_id": 1,
                "no_of_employees": 33,
                "capital": 500,
                "last_year_sales": 10000,
                "established_on": "2021-01-01",
                "business_partner_company": "デロイトトーマツコンサルティング",
                "major_bank": "三菱UFJ銀行",
                "company_features": "一つのプロジェクトを成功させるために何でもします",
                "job_description": "・広告運用・企画",
                "application_conditions": "楽しく働ける方",
                "employee_benefits": "通信教育、財形貯蓄、退職金、確定給付型企業年金、次世代育成支援金、グループ表彰、持ち株会、LTD（長期所得補償保険）、永年勤続表彰（旅行招待）、慶弔見舞金、単身者用借上社宅制度",
                "created_at": "2021-12-08T13:25:58.000000Z",
                "updated_at": "2021-12-08T13:29:23.000000Z",
                "offered_income_range_id": 4,
                "work_start_date_4_entr": null,
                "company_logo_path": null,
                "company_banner_img_path": "logos/cU9KWiLkcwGlHVCw6d3LcSxokELmqlZfKKVnESAc.jpg",
                "company_logo_disk": null,
                "company_banner_disk": "s3",
                "area": {
                    "id": 5,
                    "name_ja": "北海道",
                    "deleted_at": null,
                    "sort_order": 3
                },
                "prefecture": {
                    "id": 1,
                    "name_ja": "北海道",
                    "area_id": 5,
                    "deleted_at": null,
                    "in_area_sort_order": 1,
                    "sort_order": 1
                },
                "offered_income": {
                    "id": 4,
                    "lower_limit": 701,
                    "upper_limit": 800,
                    "unit": "ten thousand",
                    "currency": "jpy",
                    "is_lowest_limit": null,
                    "is_highest_limit": null
                },
                "industries": [
                    {
                        "id": 4,
                        "name": "Machine parts/molds",
                        "industry_category_id": 1,
                        "pivot": {
                            "founder_profile_id": 4,
                            "industry_id": 4
                        }
                    },
                    {
                        "id": 32,
                        "name": "Pet related",
                        "industry_category_id": 3,
                        "pivot": {
                            "founder_profile_id": 4,
                            "industry_id": 32
                        }
                    },
                    {
                        "id": 80,
                        "name": "Welfare/long-term care related services",
                        "industry_category_id": 5,
                        "pivot": {
                            "founder_profile_id": 4,
                            "industry_id": 80
                        }
                    }
                ]
            },
            {
                "id": 6,
                "user_id": 10000,
                "company_name": "sdasd",
                "is_listed_company": true,
                "area_id": 3,
                "prefecture_id": 25,
                "no_of_employees": 2319,
                "capital": 21321,
                "last_year_sales": 21321,
                "established_on": "2021-02-01",
                "business_partner_company": "sadas",
                "major_bank": "asdas",
                "company_features": "sadsa",
                "job_description": "asdas",
                "application_conditions": "adas",
                "employee_benefits": "asdas",
                "created_at": "2021-12-08T13:42:21.000000Z",
                "updated_at": "2021-12-08T13:42:21.000000Z",
                "offered_income_range_id": 1,
                "work_start_date_4_entr": null,
                "company_logo_path": null,
                "company_banner_img_path": null,
                "company_logo_disk": null,
                "company_banner_disk": null,
                "area": {
                    "id": 3,
                    "name_ja": "関西",
                    "deleted_at": null,
                    "sort_order": 2
                },
                "prefecture": {
                    "id": 25,
                    "name_ja": "滋賀県",
                    "area_id": 3,
                    "deleted_at": null,
                    "in_area_sort_order": 1,
                    "sort_order": 25
                },
                "offered_income": {
                    "id": 1,
                    "lower_limit": null,
                    "upper_limit": 500,
                    "unit": "ten thousand",
                    "currency": "jpy",
                    "is_lowest_limit": true,
                    "is_highest_limit": null
                },
                "industries": [
                    {
                        "id": 1,
                        "name": "General electronics manufacturer",
                        "industry_category_id": 1,
                        "pivot": {
                            "founder_profile_id": 6,
                            "industry_id": 1
                        }
                    }
                ]
            }
        ]
- get('organizer/founder-profiles/{id}')
    `REQUEST BODY`
    `POSSIBLE RESPONSES`
        {
            "id": 24,
            "user_id": 10000,
            "company_name": "Example Company",
            "is_listed_company": false,
            "area_id": 3,
            "prefecture_id": 28,
            "no_of_employees": 100,
            "capital": 1000000,
            "last_year_sales": 500000,
            "established_on": "2000-01-01",
            "business_partner_company": "Example Business Partner",
            "major_bank": "Example Bank",
            "company_features": "Lorem ipsum dolor sit amet",
            "job_description": "Lorem ipsum dolor sit amet",
            "application_conditions": "Lorem ipsum dolor sit amet",
            "employee_benefits": "Lorem ipsum dolor sit amet",
            "created_at": "2023-05-13T22:19:00.000000Z",
            "updated_at": "2023-05-13T22:19:00.000000Z",
            "offered_income_range_id": 2,
            "work_start_date_4_entr": "2023-01-01",
            "company_logo_path": null,
            "company_banner_img_path": null,
            "company_logo_disk": null,
            "company_banner_disk": null,
            "area": {
                "id": 3,
                "name_ja": "関西",
                "deleted_at": null,
                "sort_order": 2
            },
            "prefecture": {
                "id": 28,
                "name_ja": "兵庫県",
                "area_id": 3,
                "deleted_at": null,
                "in_area_sort_order": 4,
                "sort_order": 28
            },
            "offered_income": {
                "id": 2,
                "lower_limit": 501,
                "upper_limit": 600,
                "unit": "ten thousand",
                "currency": "jpy",
                "is_lowest_limit": null,
                "is_highest_limit": null
            }
        }
- post('organizer/founder-profiles')
    `REQUEST BODY`
        (at least one of the following) 
        {
            "company_name": "Example Company",
            "area_id": 3,
            "prefecture_id": 28,
            "is_listed_company": false,
            "no_of_employees": 100,
            "capital": 1000000,
            "last_year_sales": 500000,
            "established_on": "2000-01-01",
            "business_partner_company": "Example Business Partner",
            "major_bank": "Example Bank",
            "company_features": "Lorem ipsum dolor sit amet",
            "job_description": "Lorem ipsum dolor sit amet",
            "application_conditions": "Lorem ipsum dolor sit amet",
            "employee_benefits": "Lorem ipsum dolor sit amet",
            "offered_income_range_id": 2,
            "work_start_date_4_entr": "2023-01-01"
        }
    `POSSIBLE RESPONSES`

## founder or entrepreneur token required

- get('entrepreneurs')
    `REQUEST BODY`
    `POSSIBLE RESPONSES`
        [
            {
                "id": 100019,
                "first_name": null,
                "upper_limit": null,
                "age": null,
                "entr_profile": {
                    "user_id": 100019
                }
            }
        ]
- get('founders')
    `REQUEST BODY`
    `POSSIBLE RESPONSES`
        [
            {
                "id": 24,
                "company_name": "Example Company",
                "no_of_employees": 100,
                "is_listed_company": false,
                "area_id": 3,
                "area": {
                    "id": 3,
                    "name_ja": "関西"
                },
                "industries": []
            },
            {
                "id": 21,
                "company_name": "test",
                "no_of_employees": 1,
                "is_listed_company": true,
                "area_id": 2,
                "area": {
                    "id": 2,
                    "name_ja": "関東"
                },
                "industries": [
                    {
                        "id": 3,
                        "name": "Industrial equipment (machine tools, semiconductor manufacturing equipment, robots, etc.)",
                        "pivot": {
                            "founder_profile_id": 21,
                            "industry_id": 3
                        }
                    }
                ]
            },
            {
                "id": 20,
                "company_name": "a-ku",
                "no_of_employees": 4,
                "is_listed_company": true,
                "area_id": 3,
                "area": {
                    "id": 3,
                    "name_ja": "関西"
                },
                "industries": [
                    {
                        "id": 1,
                        "name": "General electronics manufacturer",
                        "pivot": {
                            "founder_profile_id": 20,
                            "industry_id": 1
                        }
                    }
                ]
            }
        ]
- get('entrepreneurs/{id}')
    `REQUEST BODY`
    `POSSIBLE RESPONSES`
        {
            "data": {
                "id": 100019,
                "first_name": null,
                "last_name": null,
                "first_name_cana": null,
                "last_name_cana": null,
                "gender": null,
                "email": "dannydanay@example.com",
                "email_verified_at": null,
                "dob": null,
                "age": null,
                "income_range_id": null,
                "income": null,
                "avatar": null,
                "type": "entrepreneur",
                "entrepreneur_profile": {
                    "address": null,
                    "school_name": null,
                    "present_post_other": null,
                    "present_company": null,
                    "lang_other": null,
                    "transfer": null,
                    "work_start_date": null,
                    "school_major": null
                },
                "created_at": "2023-05-23T06:36:34.000000Z"
            }
        }
- get('founders/{id}')
    `REQUEST BODY`
    `POSSIBLE RESPONSES`
        {
            "data": {
                "company_name": "GLUE STANCE株式会社",
                "is_listed_company": false,
                "no_of_employees": 1,
                "capital": 5000,
                "last_year_sales": 20000,
                "established_on": "2021-03-01",
                "business_partner_company": "外貨ex by GMO株式会社",
                "major_bank": "GMOあおぞらネット銀行",
                "company_features": "動画を使ったマーケティングに強いコンサル会社です。",
                "job_description": "主に動画を活用したマーケティング支援。NFT作成などのクリエイティブ領域もから事業設計まで。",
                "application_conditions": "他業種へのコネクションがある方",
                "employee_benefits": "なし",
                "work_start_date_4_entr": "2022-05-09",
                "company_logo": null,
                "company_banner": null
            }
        }

## staff token required

- get('staff/founder-profiles/{id}/founder-users')
    `REQUEST BODY`
    `POSSIBLE RESPONSES`
        {
            "founder_users": [
                {
                    "id": 9,
                    "role": "admin",
                    "first_name": "John",
                    "last_name": "Doe",
                    "email": "johndoe@example.com"
                },
                {
                    "id": 12,
                    "role": "admin",
                    "first_name": "John",
                    "last_name": "Doe",
                    "email": "johndoe@examplee.com"
                },
                {
                    "id": 13,
                    "role": "admin",
                    "first_name": "John",
                    "last_name": "Doe",
                    "email": "johndoe@exampleee.com"
                }
            ]
        }
- post('staff/founder-users')
    `REQUEST BODY`
        {
            "founder_id": ['required' ],
            "first_name": ['required' ],
            "last_name": ['required' ],
            "email": ['required' ],
            "password": ['required', Password::min(8)],
            "role": (one of the following)['readwrite', 'readonly', 'expired'],
        }
    `POSSIBLE RESPONSES`
- get('staff/founder-users/{userId}')
    `REQUEST BODY`
    `POSSIBLE RESPONSES`
        {
            "first_name": "john",
            "last_name": "Doee",
            "email": "johndoe@exampleeee.fr",
            "role": "readonly"
        }
- put('staff/founder-users/{userId}')
    `REQUEST BODY`
        (at least one of the following)
        {
            "founder_id": ['required' ],
            "first_name": ['required' ],
            "last_name": ['required' ],
            "email": ['required' ],
            "password": ['required', Password::min(8)],
            "role": (one of the following)['readwrite', 'readonly', 'expired'],
        }
    `POSSIBLE RESPONSES`
- get('staff/organizers')
    `REQUEST BODY`
    `POSSIBLE RESPONSES`
        [
            {
                "id": 10000,
                "professional_corporation_name": "Acme Inc.",
                "name_of_person_in_charge": "Jane Doe",
                "email": "karim3alaoui@gmail.com",
                "phone_number": "1234567890",
                "square_one_members_id": "abc123",
                "created_at": "2023-05-08T06:06:04.000000Z",
                "updated_at": "2023-05-30T08:21:21.000000Z",
                "email_verified_at": null
            }
        ]
- get('staff/organizers/{userId}')
    `REQUEST BODY`
    `POSSIBLE RESPONSES`
        {
            "id": 10000,
            "professional_corporation_name": "Acme Inc.",
            "name_of_person_in_charge": "Jane Doe",
            "email": "karim3alaoui@gmail.com",
            "phone_number": "1234567890",
            "square_one_members_id": "abc123",
            "created_at": "2023-05-08T06:06:04.000000Z",
            "updated_at": "2023-05-30T08:21:21.000000Z",
            "email_verified_at": null
        }
- get('staff/organizers/{userId}/founder-profiles')
    `REQUEST BODY`
    `POSSIBLE RESPONSES`
        [
            {
                "id": 4,
                "user_id": 10000,
                "company_name": "電通株式会社",
                "is_listed_company": true,
                "area_id": 5,
                "prefecture_id": 1,
                "no_of_employees": 33,
                "capital": 500,
                "last_year_sales": 10000,
                "established_on": "2021-01-01",
                "business_partner_company": "デロイトトーマツコンサルティング",
                "major_bank": "三菱UFJ銀行",
                "company_features": "一つのプロジェクトを成功させるために何でもします",
                "job_description": "・広告運用・企画",
                "application_conditions": "楽しく働ける方",
                "employee_benefits": "通信教育、財形貯蓄、退職金、確定給付型企業年金、次世代育成支援金、グループ表彰、持ち株会、LTD（長期所得補償保険）、永年勤続表彰（旅行招待）、慶弔見舞金、単身者用借上社宅制度",
                "created_at": "2021-12-08T13:25:58.000000Z",
                "updated_at": "2021-12-08T13:29:23.000000Z",
                "offered_income_range_id": 4,
                "work_start_date_4_entr": null,
                "company_logo_path": null,
                "company_banner_img_path": "logos/cU9KWiLkcwGlHVCw6d3LcSxokELmqlZfKKVnESAc.jpg",
                "company_logo_disk": null,
                "company_banner_disk": "s3",
                "area": {
                    "id": 5,
                    "name_ja": "北海道",
                    "deleted_at": null,
                    "sort_order": 3
                },
                "prefecture": {
                    "id": 1,
                    "name_ja": "北海道",
                    "area_id": 5,
                    "deleted_at": null,
                    "in_area_sort_order": 1,
                    "sort_order": 1
                },
                "offered_income": {
                    "id": 4,
                    "lower_limit": 701,
                    "upper_limit": 800,
                    "unit": "ten thousand",
                    "currency": "jpy",
                    "is_lowest_limit": null,
                    "is_highest_limit": null
                }
            },
            {
                "id": 6,
                "user_id": 10000,
                "company_name": "sdasd",
                "is_listed_company": true,
                "area_id": 3,
                "prefecture_id": 25,
                "no_of_employees": 2319,
                "capital": 21321,
                "last_year_sales": 21321,
                "established_on": "2021-02-01",
                "business_partner_company": "sadas",
                "major_bank": "asdas",
                "company_features": "sadsa",
                "job_description": "asdas",
                "application_conditions": "adas",
                "employee_benefits": "asdas",
                "created_at": "2021-12-08T13:42:21.000000Z",
                "updated_at": "2021-12-08T13:42:21.000000Z",
                "offered_income_range_id": 1,
                "work_start_date_4_entr": null,
                "company_logo_path": null,
                "company_banner_img_path": null,
                "company_logo_disk": null,
                "company_banner_disk": null,
                "area": {
                    "id": 3,
                    "name_ja": "関西",
                    "deleted_at": null,
                    "sort_order": 2
                },
                "prefecture": {
                    "id": 25,
                    "name_ja": "滋賀県",
                    "area_id": 3,
                    "deleted_at": null,
                    "in_area_sort_order": 1,
                    "sort_order": 25
                },
                "offered_income": {
                    "id": 1,
                    "lower_limit": null,
                    "upper_limit": 500,
                    "unit": "ten thousand",
                    "currency": "jpy",
                    "is_lowest_limit": true,
                    "is_highest_limit": null
                }
            },
            {
                "id": 5,
                "user_id": 10000,
                "company_name": "株式会社博報堂",
                "is_listed_company": false,
                "area_id": 5,
                "prefecture_id": 1,
                "no_of_employees": 30,
                "capital": 1000,
                "last_year_sales": 8000,
                "established_on": "2016-02-01",
                "business_partner_company": "スバル自動車株式会社",
                "major_bank": "ゆうちょ銀行",
                "company_features": "チームワークを大事にしており、毎年チームビルディングの研修を行っています",
                "job_description": "・広告運用・企画",
                "application_conditions": "ユーモアがありまわりを明るくすることができる方",
                "employee_benefits": "グループ表彰、持ち株会、LTD（長期所得補償保険）、永年勤続表彰（旅行招待）、慶弔見舞金、単身者用借上社宅制度",
                "created_at": "2021-12-08T13:41:38.000000Z",
                "updated_at": "2022-04-04T07:04:21.000000Z",
                "offered_income_range_id": 4,
                "work_start_date_4_entr": "2022-04-05",
                "company_logo_path": null,
                "company_banner_img_path": "logos/KoZtzFSB1lSuvaOtbqG97EoVFVu0NjUjapm3jTKb.jpg",
                "company_logo_disk": null,
                "company_banner_disk": "s3",
                "area": {
                    "id": 5,
                    "name_ja": "北海道",
                    "deleted_at": null,
                    "sort_order": 3
                },
                "prefecture": {
                    "id": 1,
                    "name_ja": "北海道",
                    "area_id": 5,
                    "deleted_at": null,
                    "in_area_sort_order": 1,
                    "sort_order": 1
                },
                "offered_income": {
                    "id": 4,
                    "lower_limit": 701,
                    "upper_limit": 800,
                    "unit": "ten thousand",
                    "currency": "jpy",
                    "is_lowest_limit": null,
                    "is_highest_limit": null
                }
            }
        ]

## staff haver full access but the rest only GET

- apiResource('news-n-topics')
    `REQUEST BODY`
        'title': ['required', 'string', 'max:255'],
        'body': ['required', 'string', 'max:100000'],
        'visible_to': ['required', 'array', allowedValues = ['organizer', 'founder', 'entrepreneur', 'others']],                      
        'show_after' => ['nullable', 'after_or_equal:today', new ValidateDateTime()],
        'hide_after' => ['nullable', 'after:show_after', new ValidateDateTime()]
    `POSSIBLE RESPONSES`
        {
            "data": [
                {
                    "id": 15,
                    "title": "News for all",
                    "body": "This is a sample news topic.",
                    "show_after": "2023-04-27 10:36:27+09",
                    "hide_after": "2023-06-30 10:00:00+09",
                    "created_at": "2023-04-26T16:36:27.000000Z"
                },
                {
                    "id": 13,
                    "title": "News for users",
                    "body": "This is a sample news topic.",
                    "show_after": "2023-04-27 10:35:35+09",
                    "hide_after": "2023-06-30 10:00:00+09",
                    "created_at": "2023-04-26T16:35:35.000000Z"
                }
            ],
            "links": {
                "first": "http://focused-ritchie.153-122-197-139.plesk.page:8000/api/v1/news-topic?page=1",
                "last": "http://focused-ritchie.153-122-197-139.plesk.page:8000/api/v1/news-topic?page=1",
                "prev": null,
                "next": null
            },
            "meta": {
                "current_page": 1,
                "from": 1,
                "last_page": 1,
                "links": [
                    {
                        "url": null,
                        "label": "&laquo; Previous",
                        "active": false
                    },
                    {
                        "url": "http://focused-ritchie.153-122-197-139.plesk.page:8000/api/v1/news-topic?page=1",
                        "label": "1",
                        "active": true
                    },
                    {
                        "url": null,
                        "label": "Next &raquo;",
                        "active": false
                    }
                ],
                "path": "http://focused-ritchie.153-122-197-139.plesk.page:8000/api/v1/news-topic",
                "per_page": 15,
                "to": 2,
                "total": 2
            }
        }
- get('news-topic')
    `REQUEST BODY`
    `POSSIBLE RESPONSES`
        {
            "data": [
                {
                    "id": 15,
                    "title": "News for all",
                    "body": "This is a sample news topic.",
                    "show_after": "2023-04-27 10:36:27+09",
                    "hide_after": "2023-06-30 10:00:00+09",
                    "created_at": "2023-04-26T16:36:27.000000Z"
                },
                {
                    "id": 13,
                    "title": "News for users",
                    "body": "This is a sample news topic.",
                    "show_after": "2023-04-27 10:35:35+09",
                    "hide_after": "2023-06-30 10:00:00+09",
                    "created_at": "2023-04-26T16:35:35.000000Z"
                }
            ],
            "links": {
                "first": "http://focused-ritchie.153-122-197-139.plesk.page:8000/api/v1/news-topic?page=1",
                "last": "http://focused-ritchie.153-122-197-139.plesk.page:8000/api/v1/news-topic?page=1",
                "prev": null,
                "next": null
            },
            "meta": {
                "current_page": 1,
                "from": 1,
                "last_page": 1,
                "links": [
                    {
                        "url": null,
                        "label": "&laquo; Previous",
                        "active": false
                    },
                    {
                        "url": "http://focused-ritchie.153-122-197-139.plesk.page:8000/api/v1/news-topic?page=1",
                        "label": "1",
                        "active": true
                    },
                    {
                        "url": null,
                        "label": "Next &raquo;",
                        "active": false
                    }
                ],
                "path": "http://focused-ritchie.153-122-197-139.plesk.page:8000/api/v1/news-topic",
                "per_page": 15,
                "to": 2,
                "total": 2
            }
        }
    