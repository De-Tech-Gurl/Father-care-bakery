<?php

return [
    'name' => 'Father Care Bakery',
    'tagline' => 'Our Daily Bread',
    'address' => '4 Watt Street, Uko Ekong, beside The Apostolic Church Nigeria, Ikot Abasi L.G.A, Akwa Ibom State',
    'phone' => '08139502961',
    'email' => 'info@fathercarebakery.com',
    'hours' => [
        'weekdays' => 'Mon – Fri: 7:00 AM – 8:00 PM',
        'saturday' => 'Saturday: 8:00 AM – 6:00 PM',
        'sunday' => 'Sunday: 9:00 AM – 5:00 PM',
    ],
    'bank' => [
        'name' => env('BAKERY_BANK_NAME', 'OPAY microfinance bank'),
        'account_name' => env('BAKERY_BANK_ACCOUNT_NAME', 'Father Care Bakery et. Deborah Chuwkwuokike Chizobam'),
        'account_number' => env('BAKERY_BANK_ACCOUNT_NUMBER', '8139502961'),
    ],
];
