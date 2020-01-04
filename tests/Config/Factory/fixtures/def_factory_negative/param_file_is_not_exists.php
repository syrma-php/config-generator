<?php

declare(strict_types=1);

use Syrma\ConfigGenerator\Config\ConfigDefinition as Def;
use Syrma\ConfigGenerator\Config\ConfigFileType;


$def0 = [
    Def::KEY_TYPE => ConfigFileType::TYPE_NGINX,
    Def::KEY_OUTPUT_BASE_PATH => __DIR__,
    Def::KEY_OUTPUT => 'fake.conf',
    Def::KEY_TEMPLATE => __DIR__ . '/fake.tpl',
    Def::KEY_ENVIRONMENTS => [
        'prod' => [],
    ],
    Def::KEY_PARAMETER_FILES => [
        '/not-exist.php'
    ]
];

return [
    Def::KEY_DEFAULTS => [],

    Def::KEY_DEFINITIONS => [
        'def0' => $def0,
    ]
];