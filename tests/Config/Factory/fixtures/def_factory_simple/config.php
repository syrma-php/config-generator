<?php

declare(strict_types=1);

use Syrma\ConfigGenerator\Config\ConfigDefinition as Def;
use Syrma\ConfigGenerator\Config\ConfigFileType;

$def0 = [
    Def::KEY_TYPE => ConfigFileType::TYPE_CRON,
    Def::KEY_OUTPUT_BASE_PATH => __DIR__,
    Def::KEY_OUTPUT => sprintf('%s.%s.conf', Def::MARKER_ENV, Def::MARKER_DEFINITION),
    Def::KEY_TEMPLATE => sprintf('%s/%s.%s.tpl', __DIR__, Def::MARKER_ENV, Def::MARKER_DEFINITION),
    Def::KEY_ENVIRONMENTS => [
        'live' => [
        ],
        'dev' => [
            Def::KEY_OUTPUT => sprintf('%s/%s.%s.conf', Def::MARKER_ENV, Def::MARKER_ENV, Def::MARKER_DEFINITION),
        ],
    ],
];

$def1 = [
    Def::KEY_TYPE => ConfigFileType::TYPE_NGINX,
    Def::KEY_OUTPUT_BASE_PATH => __DIR__.'/out',
    Def::KEY_TEMPLATE => sprintf('%s/%s.%s.tpl', __DIR__, Def::MARKER_ENVIRONMENT, Def::MARKER_DEFINITION),
    Def::KEY_ENVIRONMENTS => [
        'prod' => [
            Def::KEY_OUTPUT => sprintf('%s.%s.conf', Def::MARKER_ENVIRONMENT, Def::MARKER_DEFINITION),
        ],
        'test' => [
            Def::KEY_OUTPUT => sprintf('conf.d/%s.%s.conf', Def::MARKER_ENV, Def::MARKER_DEFINITION),
            Def::KEY_TEMPLATE => __DIR__.'/fake.tpl',
        ],
    ],
];

return [
    Def::KEY_DEFAULTS => [
    ],

    Def::KEY_DEFINITIONS => [
        'def0' => $def0,
        'def1' => $def1,
    ],
];
