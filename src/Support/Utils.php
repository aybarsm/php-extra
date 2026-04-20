<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Support;

final class Utils
{
    private static function prepareDivideBy(
        int $max,
        int $dividedBy,
        int $min,
        array $values,
    ): array
    {
        $hasValues = count($values) > 0;

        if (!$hasValues) return [$max, $values];

        rsort($values);

        $lastKey = array_key_last($values);
        $cap = $max - array_sum($values);

        if ($cap < 0 || $values[$lastKey] < $min) {
            $subtract = abs($values[$lastKey] < $min ? ($max % $dividedBy) : $cap);
            $values[0] -= rand(1, $subtract);
            rsort($values);
            $cap = $max - array_sum($values);
        }

        return [$cap, $values];
    }
    public static function divideBy(
        int $max,
        int $dividedBy,
        int $min = 0,
    ): array
    {
        throw_if_(
            $max <= 0 || $dividedBy <= 0,
            \InvalidArgumentException::class,
            'The maximum and dividedBy values must be greater than 0'
        );

        throw_if_(
            $min < 0,
            \InvalidArgumentException::class,
            'The minimum value must be equal or greater than 0'
        );

        throw_if_(
            $max < $dividedBy,
            \InvalidArgumentException::class,
            sprintf('Maximum value `%d` cannot be lower than dividedBy value of `%d`', $max, $dividedBy)
        );

        throw_if_(
            $max < ($min * $dividedBy),
            \InvalidArgumentException::class,
            sprintf('Maximum value `%d` cannot be lower than mn * dividedBy value of `%d`', $max, ($min * $dividedBy))
        );

        if ($max === $dividedBy) return array_fill(0, $max, 1);
        ds([
            'max' => $max,
            'dividedBy' => $dividedBy,
        ]);

        $ret = [];

        while(true){
            $ttlMatches = array_sum($ret) === $max;
            $isCompleted = $ttlMatches && ($min === 0 || (count($ret) > 0 && $ret[count($ret) - 1] >= $min));

            if ($isCompleted){
                break;
            }

            for($i = 0; $i < $dividedBy; $i++){
                [$cap, $ret] = self::prepareDivideBy($max, $dividedBy, $min, $ret);

                if (!isset($ret[$i])) $ret[$i] = 0;
                $ret[$i] += rand($min, $cap);
            }
            rsort($ret);
        }

//        shuffle($ret);
//
//        return array_values($ret);
        return $ret;
    }
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
