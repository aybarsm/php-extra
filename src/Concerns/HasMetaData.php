<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Concerns;

trait HasMetaData
{
    protected static array $_metaData_ = [];

    protected static function getMetaData(?string $key = null, mixed $default = null): mixed
    {
        if (blank($key)) return static::$_metaData_;
        if (static::hasMetaData($key)) return static::$_metaData_[$key];
        return $default;
    }

    protected static function setMetaData(string $key, mixed $value): void
    {
        static::$_metaData_[$key] = $value;
    }

    protected static function hasMetaData(string $key): bool
    {
        return array_key_exists($key, static::$_metaData_);
    }

    protected static function unsetMetaData(string $key): void
    {
        if (static::hasMetaData($key)) {
            unset(static::$_metaData_[$key]);
        }
    }

    protected static function getMetaDataKey(
        string|int|null|iterable|\Stringable $key,
        bool $forceSelf = true,
    ): ?string
    {
        return data_key($key, ($forceSelf ? static::class : null));
    }
}
