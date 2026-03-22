<?php

use PHPUnit\Framework\TestCase;
use Mockery;
use App\Repositories\UserRepository;
use App\User;
use App\UserService;

class MockTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function testUserRepositoryMock(): void
    {
        $userRepositoryMock = Mockery::mock(UserRepository::class);

        $userRepositoryMock
            ->shouldReceive('findUserByEmail')
            ->once()
            ->with('test@example.com')
            ->andReturn(new User('John Doe', 'test@example.com', 30));

        $userService = new UserService($userRepositoryMock);

        $user = $userService->getUserByEmail('test@example.com');

        $this->assertInstanceOf(User::class, $user);
        $this->assertSame('John Doe', $user->name);
    }
}
