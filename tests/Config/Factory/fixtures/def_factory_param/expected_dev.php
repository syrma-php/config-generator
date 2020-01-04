<?php

declare(strict_types=1);

return [
    'bar_0' => 'BAR-0',     // from default.parameters
    'bar_1' => 'BAR-1-NEW', // from param_1.php
    'bar_2' => 'BAR-2-NEW', // from param_2.php

    'bar_3' => 'BAR-3-NEW', // from def config
    'bar_4' => 'BAR-4-NEW', // from param_4.php
    'bar_5' => 'BAR-5-NEW', // from param_5.php

    'bar_6' => 'BAR-6-NEW-DEV', // from env config
    'bar_7' => 'BAR-7-NEW', // from param_7.php
    'bar_8' => 'BAR-8-NEW', // from param_8.php

    'param_1' => 'PARAM-1', //from param_1.php
    'param_2' => 'PARAM-2', //from param_2.php
    'param_4' => 'PARAM-4', //from param_4.php
    'param_5' => 'PARAM-5', //from param_5.php
    'param_7' => 'PARAM-7', //from param_7.php
    'param_8' => 'PARAM-8', //from param_8.php

    'def_0' => 'DEF-0', //from def parameters
    'dev_0' => 'dev-0', //from env parameters
];