<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Support;

final class Fs
{
    public static function pathSegments(string|array|null|\Stringable ...$paths): array
    {
        $segments = [];
        foreach(namespace\Arr::flatten($paths) as $path) {
            if (namespace\Data::blank($path)) continue;
            array_push($segments, namespace\Str::segments($path, DIRECTORY_SEPARATOR));
        }
        return $segments;
//        $paths = namespace\Arr::wrap($paths);
//        $paths = namespace\Arr::flatten($paths);
//
//        if (count($paths) === 0) {
//            return [getcwd()];
//        }
//
//        $segments = array_map(
//            static fn ($path) => namespace\Str::segments((string) $path, DIRECTORY_SEPARATOR),
//            ...$paths
//        );
//        $segments = namespace\Arr::flatten(
//            array_map(
//                static fn ($path) => $path,
//                $oaths
//            )
//        );
//        foreach($paths as $idx => $path) {
//            $path = str($path);
//
//            if ($idx === 0){
//                $path = $path->ltrim('\'"');
//                if ($path->ltrim()->startsWith('.')){
//                    $path = $path->ltrim()->chopStart('.')->prepend(getcwd() . DIRECTORY_SEPARATOR);
//                }elseif ($path->ltrim()->startsWith('~')){
//                    $path = $path->ltrim()->chopStart('~')->prepend($_SERVER['HOME'] . DIRECTORY_SEPARATOR);
//                }
//            }
//
//            if ($idx === array_key_last($paths)){
//                $path = $path->rtrim('\'"');
//            }
//
//            $segments = $segments->concat($path->split($pattern, -1, PREG_SPLIT_NO_EMPTY)->toArray());
//        }
//
//        return $segments;
    }
}
