<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Support;

class Laravel
{
    public static function contractExists(
        string|\Stringable $class,
        bool $autoload = true
    ): bool
    {
        $class = (string) $class;
        $class = namespace\Str::start($class, '\\Illuminate\\Contracts\\');
        $class = namespace\Str::start($class, '::class');
        return class_exists($class, $autoload);
    }
}
