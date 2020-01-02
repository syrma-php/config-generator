<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Definition;

use function constant;
use InvalidArgumentException;

class ConfigFileType
{
    public const TYPE_PLAIN = 'plain';
    public const TYPE_INI = 'ini';
    public const TYPE_XML = 'xml';
    public const TYPE_YML = 'yml';

    public const TYPE_CRON = 'cron';
    public const TYPE_NGINX = 'nginx';

    public const ALL = [
        self::TYPE_PLAIN,
        self::TYPE_INI,
        self::TYPE_XML,
        self::TYPE_YML,

        self::TYPE_CRON,
        self::TYPE_NGINX,
    ];

    /**
     * @var self[]
     */
    private static $instancePool = [];

    /**
     * @var string;
     */
    private $value;

    private function __construct(string $value)
    {
        if (null === @constant($alias = __CLASS__.'::TYPE_'.strtoupper($value))) {
            throw new InvalidArgumentException(sprintf('The constant (%s) is not defined!', $alias));
        }

        $this->value = $value;
    }

    public static function create(string $value): self
    {
        if (false === isset(self::$instancePool[$value])) {
            self::$instancePool[$value] = new self($value);
        }

        return self::$instancePool[$value];
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
