<?php

namespace Laramina\Contracts;

interface AdminModule
{
    public function name(): string;

    public function label(): string;

    public function icon(): ?string;

    public function route(): string;
}
