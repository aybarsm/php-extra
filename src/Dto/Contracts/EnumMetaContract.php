<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Dto\Contracts;

use UnitEnum;

interface EnumMetaContract extends \JsonSerializable
{
    public function getClass(): string;
    public function getFile(): string;
    public function getCases(): array;
    public function getNames(): array;
    public function getValues(): array;
    public function isBacked(): bool;
    public function isBackedString(): bool;
    public function isBackedInt(): bool;
    public function isFlaggable(): bool;
    public function getFlagsAll(): int;
    public function hasAliasMap(): bool;
    public function hasAlias(string|\Stringable $alias): bool;
    public function getAliasMap(): array;
    public function hasFlagMap(): bool;
    public function getFlagMap(): array;
    public function getCasesAssoc(bool $includeAliases = false): array;
    public function findCaseByName(
        mixed $search,
        bool $strict = false,
        bool $includeAliases = true,
    ): ?UnitEnum;

    public function findCaseByValue(
        mixed $search,
        bool $strict = false,
        bool $includeAliases = true,
    ): ?UnitEnum;

    public function findCase(
        mixed $search,
        bool $strict = false,
        bool $includeAliases = true,
    ): ?UnitEnum;

    public function findCasesByFlags(
        int|\BackedEnum ...$flags
    ): array;
}
