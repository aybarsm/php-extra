<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Support;

final class Fs
{
    public static function pathSegments(string|array|null|\Stringable ...$paths): array
    {
        if (namespace\Data::blank($paths)) {
            return namespace\Arr::whereFilled(namespace\Str::segments(getcwd(), DIRECTORY_SEPARATOR));
        }

        $segments = [];

        foreach(namespace\Arr::flatten($paths) as $path) {
            if (namespace\Data::blank($path)) continue;
            $inner = namespace\Str::segments((string) $path, DIRECTORY_SEPARATOR);
            if (namespace\Data::blank($inner)) continue;
            if (namespace\Data::blank($segments)) {
                $segmentStarts = null;
                if ($inner[0] === '.') {
                    unset($inner[0]);
                    $segmentStarts = getcwd();
                }elseif ($inner[0] === '~'){
                    unset($inner[0]);
                    $segmentStarts = $_SERVER['HOME'];
                }
                if (!is_null($segmentStarts)) {
                    $segments = namespace\Arr::whereFilled(namespace\Str::segments($segmentStarts, DIRECTORY_SEPARATOR));
                }
            }
            array_push($segments, ...$inner);
        }

        return $segments;
    }

    public static function path(string|array|null|\Stringable ...$paths): string
    {
        $paths = self::pathSegments(...$paths);
        $dirSep = DIRECTORY_SEPARATOR;

        if (namespace\Os::family()->isUnix()){
            return namespace\Str::start(implode($dirSep, $paths), $dirSep);
        }

        if (namespace\Os::family()->isWindows()){
            $patternDrive = '/^[A-Z]\:/';
            if (namespace\Str::isMatch($paths[0], $patternDrive)){
                $paths[0] = namespace\Str::finish($paths[0], $dirSep);
            }
        }

        return implode($dirSep, $paths);
    }
}
