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

    public static function parentLocate(
        string|\Stringable $path,
        string|\Stringable $search,
        string|\Stringable|int|null $limit = null,
    ): false|string
    {
        $path = (string) $path;
        $search = (string) $search;
        $limit = is_null($limit) ? null : (is_int($limit) ? $limit : (string) $limit);

        throw_if(
            !file_exists($path),
            \InvalidArgumentException::class,
            sprintf('Path `%s` does not exist to locate `%s`', $path, $search)
        );

        throw_if(
            is_int($limit) && $limit < 1,
            \InvalidArgumentException::class,
            sprintf('Integer limit `%s` must be greater than 1 to locate `%s` in `%s`.', (string) $limit, $search, $path)
        );

        throw_if(
            is_string($limit) && (!file_exists($limit) || !is_dir($limit)),
            \InvalidArgumentException::class,
            sprintf('String limit `%s` must be an existing directory to locate `%s` in `%s`.', $limit, $search, $path)
        );

        $path = realpath($path);
        $path = is_dir($path) ? $path : dirname($path);
        $limit = is_string($limit) ? realpath($limit) : null;

        throw_if(
            is_string($limit) && !str_starts_with($path, $limit),
            \InvalidArgumentException::class,
            sprintf('String limit `%s` must be a parent or same path of `%s` to locate `%s`.', $limit, $path, $search)
        );

        $counter = 0;
        while(true){
            $counter++;
            $lookup = $path . DIRECTORY_SEPARATOR . $search;

            if (file_exists($lookup)){
                return $lookup;
            }

            if (is_string($limit) && $limit === $path){
                break;
            }elseif(is_int($limit) && $limit === $counter){
                break;
            }

            $path = dirname($path);
        }

        return false;
    }
}
