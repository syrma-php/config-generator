<?php

declare(strict_types=1);

namespace Syrma\ConfigGenerator\Util;

class ParameterBag
{
    /**
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function append(self $newData): void
    {
        $this->data = array_replace($this->data, $newData->data);
    }

    public function all(): array
    {
        return $this->data;
    }
}
