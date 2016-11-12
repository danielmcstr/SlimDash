<?php
return [
    'settings' => [
        'displayErrorDetails'    => true,
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        // Renderer settings
        'renderer'               => [
            'folders' => [
                'theme' => APP_PATH . '/themes/app-main',
            ],
            'ext'     => 'twig',
        ],
        'language'               => 'en',

        // Monolog settings
        'logger'                 => [
            'name' => 'slim-app',
            'path' => APP_PATH . '/logs/app.log',
        ],
        'session'                => [
            'namespace' => 'slimdash__',
        ],
        'modules_dir'            => APP_PATH . '/modules/',
        'appmodule'              => '\\AppMain\\AppMainModule',
    ],
];
