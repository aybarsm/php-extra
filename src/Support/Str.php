<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Support;

use Aybarsm\Extra\Enums\ModeDirection;
use Aybarsm\Extra\Enums\ModeMatch;
use Aybarsm\Extra\Enums\ModeStrCase;

final class Str
{
    public static function random(
        int $length = 16,
        ModeStrCase|int|string|array $cases = ModeStrCase::LOWER_UPPER_NUMBER,
        array $alphabetLower = [
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
            'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
            'w', 'x', 'y', 'z'
        ],
        array $alphabetUpper = [
            'A', 'B', 'C', 'D', 'E', 'F', 'G',
            'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
            'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        ],
        array $alphabetNumber = [
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
        ],
        array $alphabetSymbol = [
            '~', '!', '#', '$', '%', '^', '&', '*', '(', ')', '-',
            '_', '.', ',', '<', '>', '?', '/', '\\', '{', '}', '[',
            ']', '|', ':', ';',
        ],
    ): string
    {
        $cases = ModeStrCase::makeAll($cases);

    }
    public static function len(string|\Stringable $subject): int
    {
        $subject = (string) $subject;
        return function_exists('mb_strlen') ? mb_strlen($subject) : strlen($subject);
    }

    public static function split(
        string|\Stringable $subject,
        int $length = 1,
        ?string $encoding = null,
    ): array|false
    {
        $subject = (string) $subject;
        return function_exists('mb_str_split') ? mb_str_split($subject, $length, $encoding) : str_split($subject, $length);
    }

    public static function isMatch(
        string|\Stringable|iterable $pattern,
        string|\Stringable $value,
        ModeMatch|string $match = ModeMatch::ANY,
    ): bool
    {
        $value = (string) $value;
        return ModeMatch::make($match, false)->matchesBy(
            namespace\Arr::wrap($pattern),
            static fn ($item) => preg_match((string) $item, $value) === 1,
        );
    }

    public static function contains(
        string|\Stringable $subject,
        string|\Stringable|array $needle,
        ModeMatch|string $match = ModeMatch::ANY,
    ): bool
    {
        $subject = (string) $subject;
        return ModeMatch::make($match, false)->matchesBy(
            namespace\Arr::wrap($needle),
            static fn ($n) => str_contains($subject, (string) $n),
        );
    }

    public static function start(string|\Stringable $value, string|\Stringable $prefix): string
    {
        [$value, $prefix] = [(string) $value, (string) $prefix];
        if ($prefix === '') return $value;
        $quoted = preg_quote($prefix, '/');

        return $prefix.preg_replace('/^(?:'.$quoted.')+/u', '', $value);
    }

    public static function finish(string|\Stringable $value, string|\Stringable $cap): string
    {
        [$value, $cap] = [(string) $value, (string) $cap];
        if ($cap === '') return $value;
        $quoted = preg_quote($cap, '/');

        return preg_replace('/(?:'.$quoted.')+$/u', '', $value).$cap;
    }

    public static function wrap(
        string|\Stringable $value,
        string|\Stringable $prefix,
        string|\Stringable $suffix,
    ): string
    {
        return self::finish(self::start($value, $prefix), $suffix);
    }
    public static function segments(
        mixed $str,
        string $separator,
        int $limit = -1,
        int $flags = PREG_SPLIT_NO_EMPTY,
    ): array
    {
        if (is_null($str)) return [];
        return preg_split('#' . preg_quote($separator, '/') . '#', trim((string) $str), $limit, $flags);
    }

    public static function chopStart(
        string|\Stringable $subject,
        string|\Stringable|array $needle,
    ): string
    {
        $subject = (string) $subject;
        foreach (namespace\Arr::wrap($needle) as $n) {
            $n = (string) $n;
            if ($n !== '' && str_starts_with($subject, $n)) {
                return mb_substr($subject, mb_strlen($n));
            }
        }

        return $subject;
    }

    public static function chopEnd(
        string|\Stringable $subject,
        string|\Stringable|array $needle,
    ): string
    {
        $subject = (string) $subject;
        foreach (namespace\Arr::wrap($needle) as $n) {
            $n = (string) $n;
            if ($n !== '' && str_ends_with($subject, $n)) {
                return mb_substr($subject, 0, -mb_strlen($n));
            }
        }

        return $subject;
    }
    public static function chop(
        string|\Stringable $subject,
        string|\Stringable|array $needle,
        string|ModeDirection $dir = ModeDirection::BOTH,
    ): string
    {
        $dir = ModeDirection::make($dir, false);
        if ($dir->isIncluded(ModeDirection::LEFT)) {
            $subject = self::chopStart($subject, $needle);
        }
        if ($dir->isIncluded(ModeDirection::RIGHT)) {
            $subject = self::chopEnd($subject, $needle);
        }
        return $subject;
    }

    public static function trim(
        string|\Stringable $subject,
        ?string $characters = null,
        string|ModeDirection $dir = ModeDirection::BOTH,
    ): string
    {
        $characters = $characters ?? " \n\r\t\v\0";
        $subject = (string) $subject;
        $dir = ModeDirection::make($dir, false);
        return match($dir) {
            ModeDirection::BOTH => trim($subject, $characters),
            ModeDirection::LEFT => ltrim($subject, $characters),
            ModeDirection::RIGHT => rtrim($subject, $characters),
        };
    }
    public static function trimRecursive(
        string|\Stringable $subject,
        ?string $characters = null,
        string|ModeDirection $dir = ModeDirection::BOTH,
        string|\Stringable ...$more,
    ): string
    {
        $characters = self::split($characters ?? " \n\r\t\v\0");

        $chop = [];
        foreach(namespace\Arr::whereFilled($more) as $item){
            $item = (string) $item;
            if (self::len($item) > 1){
                $chop[] = $item;
            }elseif (!in_array($item, $characters, true)) {
                $characters[] = $item;
            }
        }

        $characters = implode('', $characters);
        $subject = self::trim($subject, $characters, $dir);

        foreach($chop as $item){
            $subject = self::chop($subject, $item, $dir);
            $subject = self::trim($subject, $characters, $dir);
        }

        return $subject;
    }
}
