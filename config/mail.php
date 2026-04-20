<?php

return [
    'enabled' => false,
    'driver' => 'smtp',
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'encryption' => 'tls',
    'username' => '',
    'password' => '',
    'from_email' => '',
    'from_name' => 'Eco-Q House',
    'timeout' => 15,
    'log_file' => base_path('storage/logs/mail.log'),
];
