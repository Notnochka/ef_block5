<?php

use App\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserCanBeCreated(): void {
        $user = new User('John Doe', 30);

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('John Doe', $user->name);
        $this->assertSame(30, $user->age);
    }

    public function testUserFullName(): void {
        $user = new User('John Doe', 30);

        $this->assertSame('John Doe', $user->fullName());
    }
}

