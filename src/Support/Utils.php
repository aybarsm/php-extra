<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Support;

final class Utils
{
    public static function sentinel(): string
    {
        if (!defined('AYBARSM_SENTINEL')) {
            if (defined('LARAVEL_START')) {
                $sentinel = constant('LARAVEL_START');
            }else {
                $sentinel = microtime(true);
            }
            define('AYBARSM_SENTINEL', hash('xxh128', (string) $sentinel));
        }

        return constant('AYBARSM_SENTINEL');
    }

    public static function callUntil(\Closure $check, mixed $default, \Closure ...$calls): mixed
    {
        if (blank($calls)){
            return value($default);
        }

        foreach($calls as $call) {
            if ($result = value($check($call()))) {
                return $result;
            }
        }

        return value($default);
    }
}
