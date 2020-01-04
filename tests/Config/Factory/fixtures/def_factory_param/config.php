<?php

declare(strict_types=1);

use Syrma\ConfigGenerator\Config\ConfigDefinition as Def;
use Syrma\ConfigGenerator\Config\ConfigFileType;

$def0 = [
    Def::KEY_TYPE => ConfigFileType::TYPE_CRON,
    Def::KEY_OUTPUT_BASE_PATH => __DIR__,
    Def::KEY_OUTPUT => 'fake.conf',
    Def::KEY_TEMPLATE => __DIR__ . '/fake.tpl',
    Def::KEY_PARAMETERS => [
        'def_0' => 'DEF-0',
        'bar_3' => 'BAR-3-NEW'
    ],
    Def::KEY_PARAMETER_FILES => [
        __DIR__ . '/param_4.php',
        __DIR__ . '/param_5.php',
    ],
    Def::KEY_ENVIRONMENTS => [
        'live' => [
            Def::KEY_PARAMETERS => [
                'live_0' => 'live-0',
                'bar_6' => 'BAR-6-NEW-LIVE'
            ],
            Def::KEY_PARAMETER_FILES => [
                __DIR__ . '/param_7.php',
                __DIR__ . '/param_8.php',
            ],
        ],
        'dev' => [
            Def::KEY_PARAMETERS => [
                'dev_0' => 'dev-0',
                'bar_6' => 'BAR-6-NEW-DEV'
            ],
            Def::KEY_PARAMETER_FILES => [
                __DIR__ . '/param_7.php',
                __DIR__ . '/param_8.php',
            ],
        ]
    ]
];

$def1 = [
    Def::KEY_TYPE => ConfigFileType::TYPE_NGINX,
    Def::KEY_OUTPUT_BASE_PATH => __DIR__,
    Def::KEY_OUTPUT => 'fake.conf',
    Def::KEY_TEMPLATE => __DIR__ . '/fake.tpl',
    Def::KEY_ENVIRONMENTS => [
        'prod' => [

        ],
    ]

];


return [
    Def::KEY_DEFAULTS => [
        Def::KEY_PARAMETERS => [
            'bar_0' => 'BAR-0',
            // 'bar_1' => from  param_1.php
            // 'bar_2' => from  param_2.php

            'bar_3' => 'BAR-3', // over-write def config
            'bar_4' => 'BAR-4', // over-write param_4.php
            'bar_5' => 'BAR-5', // over-write param_5.php

            'bar_6' => 'BAR-6', // over-write env config
            'bar_7' => 'BAR-7', // over-write param_7.php
            'bar_8' => 'BAR-8', // over-write param_8.php
        ],

        Def::KEY_PARAMETER_FILES => [
            __DIR__ . '/param_1.php',
            __DIR__ . '/param_2.php',
        ]
    ],

    Def::KEY_DEFINITIONS => [
        'def0' => $def0,
        'def1' => $def1,
    ]
];