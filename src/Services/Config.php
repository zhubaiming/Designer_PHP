<?php

declare(strict_types=1);

namespace Hongyi\Designer\Services;

use Hongyi\Designer\Exceptions\InvalidConfigException;

class Config
{
    private array $items;

    public function __construct(mixed $items = [])
    {
        $this->items = $items;
    }

    /**
     * @throws InvalidConfigException
     */
    public function get($id)
    {
        $value = $this->items;

        if (!str_contains($id, '.')) {
            if (!array_key_exists($id, $value)) {
                throw new InvalidConfigException();
            }

            return $value[$id];
        } else {
            foreach (explode('.', $id) as $key) {
                if (!is_array($value) || !array_key_exists($key, $value)) {
                    throw new InvalidConfigException();
                }

                $value = $value[$key];
            }
        }

        return $value;
    }
}