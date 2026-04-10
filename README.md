## What It Does
It contains custom mixin classes for built-in macroable facades and also can be configured to load external mixins dynamically. It also includes some custom functions and helper traits.

## Installation

You can install the package via composer:

```bash
composer require aybarsm/laravel-extended-support
```

You can publish the config file by:

```bash
php artisan vendor:publish --provider="Aybarsm\Laravel\Support\ExtendedSupportServiceProvider" --tag=config
```

## Configure Mixins

You can remove or add new mixins to the load list and modify concretes by extending classes.

```php
return [
    'runtime' => [
        'replace_existing' => true,
        'class_autoload' => true,
        'required_trait' => 'Illuminate\Support\Traits\Macroable',
        'bind_pattern' => '/@mixin\s*([^\s*]+)/',
        'load' => [
            Aybarsm\Laravel\Support\Mixins\StringableMixin::class,
            Aybarsm\Laravel\Support\Mixins\StrMixin::class,
            Aybarsm\Laravel\Support\Mixins\ArrMixin::class,
            Aybarsm\Laravel\Support\Mixins\FileMixin::class,
            Aybarsm\Laravel\Support\Mixins\RuleMixin::class,
            Aybarsm\Laravel\Support\Mixins\ApplicationMixin::class,
            Aybarsm\Laravel\Support\Mixins\CommandMixin::class,
            Aybarsm\Laravel\Support\Mixins\ProcessMixin::class,
            Aybarsm\Laravel\Support\Mixins\CollectionMixin::class,
        ],
    ],
    'concretes' => [
        'ExtendedSupport' => Aybarsm\Laravel\Support\ExtendedSupport::class,
        'Supplements' => [
            'Str' => [
                'SemVer' => Aybarsm\Laravel\Support\Supplements\Str\SemVer::class,
            ],
            'Foundation' => [
                'Annotation' => Aybarsm\Laravel\Support\Supplements\Foundation\Annotation::class,
            ],
        ],
    ],
];
```

## Custom Mixins

You can create any new mixin by artisan command. The command will ask the class name and also provide the full list of classes that uses macroable trait to select easily or enter manually.

```bash
php artisan make:mixin ArrMixin
```

[![Make Mixin Command - Bind List](https://i.postimg.cc/g2h1kfQX/mixin-Bind-List.png)](https://postimg.cc/Hrp66Ppd)
[![Make Mixin Command - Bind Manual](https://i.postimg.cc/6pC1NC4C/mixin-Bind-Manual.png)](https://postimg.cc/cv4cRgmJ)

You can publish the stubs by:

```bash
php artisan vendor:publish --provider="Aybarsm\Laravel\Support\ExtendedSupportServiceProvider" --tag=stubs
```

Or you can manually create a class for mixin and identify the macroable class by @mixin annotation and add it to configuration to be loaded:

```php
<?php

namespace App\Mixins;

/** @mixin \Illuminate\Support\Arr */

class ArrMixin
{
    public static function toObject(): \Closure
    {
        return fn (array|object $arr, int $flags = JSON_NUMERIC_CHECK | JSON_FORCE_OBJECT): object => json_decode(json_encode($arr, $flags));
    }
}
```

```php
'runtime' => [
        'load' => [
            Aybarsm\Laravel\Support\Mixins\StringableMixin::class,
            Aybarsm\Laravel\Support\Mixins\StrMixin::class,
            Aybarsm\Laravel\Support\Mixins\ArrMixin::class,
            Aybarsm\Laravel\Support\Mixins\FileMixin::class,
            Aybarsm\Laravel\Support\Mixins\RuleMixin::class,
            Aybarsm\Laravel\Support\Mixins\ApplicationMixin::class,
            Aybarsm\Laravel\Support\Mixins\CommandMixin::class,
            Aybarsm\Laravel\Support\Mixins\ProcessMixin::class,
            Aybarsm\Laravel\Support\Mixins\CollectionMixin::class,
            App\Mixins\ArrMixin::class,
        ],
    ],
];
```

## Supplements

### Str :: Semantic Version

New Semantic Versioning helper class added. You can create a new SemVer instance directly or with macro Str::semVer() function. SemVer class is macroable too.

```php
use Aybarsm\Laravel\Support\Supplements\Str\SemVer;
use Aybarsm\Laravel\Support\Enums\SemVerScope;

// Regardless of multiple occurrences, the function always captures the first occurrence of \d+\.\d+\.\d+
$version = 'ver9.2.78beta-1.0.6';
$semVer = new SemVer($version);
$semVer = Str::semVer($version);

// Get scope of the Semantic Version:
dump($semVer->getScope(SemVerScope::MINOR)); // Output: "2"
dump($semVer->getScope(SemVerScope::MINOR, $asInteger = true)); // Output: 2

// More importantly you can easily calculate the next scopes of the Semantic Version.
dump($semVer->value()); // Output: "9.2.78"
$semVer = $semVer->next(SemVerScope::MINOR);
dump($semVer->value()); // Output: "9.3.0"
$semVer = $semVer->next(SemVerScope::PATCH);
dump($semVer->value()); // Output: "9.3.1"
$semVer = $semVer->next(SemVerScope::MAJOR);
dump($semVer->value()); // Output: "10.0.0"

// You can access the original Semantic Version by:
dump($semVer->getOriginal()); // Output: "9.2.78"

// If you would like to have an output with t original version structure
dump($semVer->value($asOriginal = true)); // Output: "ver10.0.0beta-1.0.6"

// Lastly, static function and Str macros have been implemented to validate Semantic Version string.
SemVer::validate('9.2.78'); // Output: true
// OR
Str::isSemVer('9.2'); // Output: false
```
## Helper Traits:

### EnumHelper:
```php
use Aybarsm\Laravel\Support\Traits\EnumHelper;

enum ProcessReturnType: int
{
    use EnumHelper;
    
    case STATUS = 0;
    case SUCCESSFUL = 1;
    case FAILED = 2;
    case EXIT_CODE = 3;
    case OUTPUT = 4;
    case ERROR_OUTPUT = 5;
    case INSTANCE = 6;
    case ALL_OUTPUT = 7;
}

$enum = ProcessReturnType::tryFrom(ProcessReturnType::byName('EXIT_CODE')); //returns designated enum as static
```
