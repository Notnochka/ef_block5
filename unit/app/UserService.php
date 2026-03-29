<?php

namespace App;

class UserService
{
    public function __construct(
        private UserRepository $repository
    ) {}

    public function getUserByEmail(string $email): ?User
    {
        return $this->repository->findUserByEmail($email);
    }
}
