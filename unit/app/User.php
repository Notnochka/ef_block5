<?php

namespace App;

class User
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly int $age = 0
    ) {}

    public function fullName(): string {
        return $this->name;
    }
}
