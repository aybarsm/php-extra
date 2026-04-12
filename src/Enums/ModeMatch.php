<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Enums;

use Aybarsm\Extra\Concerns\HasEnumHelpers;
use Aybarsm\Extra\Contracts\Concerns\HasEnumHelpersContract;

enum ModeMatch implements HasEnumHelpersContract
{
    use HasEnumHelpers;
    case ANY;
    case ALL;

    public function early(bool $result): ?bool
    {
        if ($result && $this === self::ANY) {
            return true;
        }elseif(! $result && $this === self::ALL) {
            return false;
        }

        return null;
    }

    public function final(): bool
    {
        return $this === self::ALL;
    }

    public function matchesBy(mixed $of, \Closure $callback): bool
    {
        foreach(array_wrap($of) as $idx => $item) {
            $res = $this->early(call_user_func_array($callback, [$item, $idx]));
            if (is_bool($res)){
                return $res;
            }
        }

        return $this->final();
    }
}
