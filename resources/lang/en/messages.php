<?php

return [
    'install' => [
        'information 1'   => 'Login address, can be modified in .env EASYADMIN.ADMIN',
        'information 2'   => "For the sake of backend security, it is not recommended to set the backend path to admin",
        'information 3'   => 'The system has already been installed. If you need to reinstall, please delete the file:/config/install/lock/install.lock, or delete the/install route.The page will redirect to ...',
        'ENV Tips'        => 'The .env configuration file does not exist',
        'PHP Tips'        => 'The PHP version cannot be less than 8.1.0',
        'PDO Tips'        => 'PDO is not currently enabled, installation cannot proceed',
        'validateError 1' => 'The backend address cannot contain special characters, only letters or numbers',
        'validateError 2' => 'The backend address cannot be less than 2 digits',
        'validateError 3' => 'The administrator password cannot be less than 5 digits',
        'validateError 4' => 'The administrator account cannot be less than 4 digits',
        'databaseError 1' => 'The database already exists. Please choose to overwrite the installation or modify the database name',
        'databaseError 2' => 'The minimum requirement for MySQL version is 5.7. x',
    ],
    'admin'   => [
        'index' => [
            'welcome' => [
                'template_message1' => 'This template is implemented based on layui2.11.x and font-awesome-6.x',
                'template_message2' => 'Note: This backend framework is permanently open source, but please do not sell or upload it to any material website, otherwise corresponding responsibilities will be pursued',
            ],
        ],
        // You can define the multilingual configuration required in the JS file here
        'js'    => [
            'hello world' => 'Hello World',
            'enter'       => 'please enter ',
            'language'    => 'en',
        ],
    ],
];
