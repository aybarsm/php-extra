<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Support;

final class Filesystem extends \SplFileInfo
{
    public function __construct(string $filename)
    {
        parent::__construct(self::resolve($filename));
    }

    public function exists(): bool
    {
        return $this->getRealPath() !== false;
    }

    public function ensureFilename(string $filename): static
    {
        if (trim($filename) === '') {
            throw new \InvalidArgumentException('Filename cannot be empty.');
        }

        $current = $this->getFilename();
        if ($this->exists() && $this->isFile() && $current === $filename) return $this;

        if (!$this->exists()) {
            $isProbablyFile = preg_match('/\.[a-zA-Z0-9]+$/', $current) === 1;
            $useBasePath = $isProbablyFile ? $this->getPath() :  $this->getPathname();
        }else {
            $useBasePath = $this->isFile() ? $this->getPath() : $this->getPathname();
        }

        return new static(self::resolve($useBasePath, $filename));
    }

    public function getFilenameWithoutExtension(): string
    {
        return pathinfo($this->getFilename(), \PATHINFO_FILENAME);
    }

    public function getContents(): string
    {
        set_error_handler(function ($type, $msg) use (&$error) { $error = $msg; });

        try {
            $content = file_get_contents($this->getPathname());
        } finally {
            restore_error_handler();
        }

        if (false === $content) {
            throw new \RuntimeException($error);
        }

        return $content;
    }

    public function putContents(mixed $data, int $flags = 0, $context = null): void
    {
        set_error_handler(function ($type, $msg) use (&$error) { $error = $msg; });

        try {
            $content = file_put_contents($this->getPathname(), $data, $flags, $context);
        } finally {
            restore_error_handler();
        }

        if (false === $content) {
            throw new \RuntimeException($error);
        }
    }

    public function makeDirectory($mode = 0755, $recursive = false, $force = false): bool
    {
        if ($force) {
            return @mkdir($this->getPathname(), $mode, $recursive);
        }

        return mkdir($this->getPathname(), $mode, $recursive);
    }

    public static function segments(string|array|null|\Stringable ...$paths): array
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

    public static function resolve(string|array|null|\Stringable ...$paths): string
    {
        $paths = self::segments(...$paths);
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

        throw_if_(
            !file_exists($path),
            \InvalidArgumentException::class,
            sprintf('Path `%s` does not exist to locate `%s`', $path, $search)
        );

        throw_if_(
            is_int($limit) && $limit < 1,
            \InvalidArgumentException::class,
            sprintf('Integer limit `%s` must be greater than 1 to locate `%s` in `%s`.', (string) $limit, $search, $path)
        );

        throw_if_(
            is_string($limit) && (!file_exists($limit) || !is_dir($limit)),
            \InvalidArgumentException::class,
            sprintf('String limit `%s` must be an existing directory to locate `%s` in `%s`.', $limit, $search, $path)
        );

        $path = realpath($path);
        $path = is_dir($path) ? $path : dirname($path);
        $limit = is_string($limit) ? realpath($limit) : null;

        throw_if_(
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
