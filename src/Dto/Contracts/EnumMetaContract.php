<?php

declare(strict_types=1);

namespace Aybarsm\Extra\Dto\Contracts;

interface EnumMetaContract
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
    public function getAliasMap(): array;
    public function hasFlagMap(): bool;
    public function getFlagMap(): array;
}
